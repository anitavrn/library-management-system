<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // ======================
    // MEMBER
    // ======================

    // POST /transactions
    // Member request borrow => status pending
    public function borrow(Request $request)
    {
        $data = $request->validate([
            'api_book_id'  => 'required|string',
            'title'        => 'required|string',
            'author'       => 'nullable|string',
            'borrow_date'  => 'required|date|date_equals:today',
            'due_date'     => 'required|date|after_or_equal:borrow_date',
        ]);

        // cari buku internal berdasarkan api_book_id
        $book = Book::where('api_book_id', $data['api_book_id'])->first();

        // kalau belum ada di kelola buku, tolak
        if (!$book) {
            return response()->json([
                'message' => 'Buku belum tersedia di perpustakaan. Silakan hubungi librarian.'
            ], 400);
        }

        // ✅ cek stok harus berlaku untuk SEMUA kondisi (baik book ada / tidak)
        if ($book->stock < 1) {
            return response()->json([
                'message' => 'Stok buku habis. Tidak bisa melakukan peminjaman.'
            ], 400);
        }

        // optional: update title/author biar sinkron (kalau kamu mau)
        $book->update([
            'title'  => $data['title'],
            'author' => $data['author'] ?? $book->author,
        ]);

        // buat transaksi pending
        $trx = Transaction::create([
            'user_id'     => $request->user()->id,
            'book_id'     => $book->id,
            'status'      => 'pending',
            'borrow_date' => $data['borrow_date'],
            'due_date'    => $data['due_date'],
        ]);

        return response()->json([
            'message' => 'Request peminjaman dikirim (menunggu konfirmasi librarian)',
            'data'    => $trx->load(['book'])
        ], 201);
    }


    public function myTransactions(Request $request)
    {
        $list = Transaction::with(['book'])
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Riwayat peminjaman member',
            'data'    => $list
        ], 200);
    }

    // GET /transactions/fines
    // List transaksi member yang memiliki denda (belum dibayar)
    public function fines(Request $request)
    {
        // Ambil semua transaksi borrowed dan yang punya fine_amount > 0
        $transactions = Transaction::with(['book'])
            ->where('user_id', $request->user()->id)
            ->where(function($query) {
                $query->where('status', 'borrowed')
                      ->orWhere('fine_amount', '>', 0);
            })
            ->orderBy('id', 'desc')
            ->get();

        // Filter: hanya yang benar-benar punya denda (terlambat ATAU fine_amount > 0) DAN belum dibayar
        $list = $transactions->filter(function($trx) {
            // Jika sudah dibayar, skip
            if ($trx->fine_paid_at) {
                return false;
            }

            // Jika fine_amount sudah di-set dan > 0, include
            if ($trx->fine_amount > 0) {
                return true;
            }

            // Jika belum ada fine_amount, cek apakah terlambat
            if ($trx->status === 'borrowed' && $trx->due_date) {
                $dueDate = \Carbon\Carbon::parse($trx->due_date);
                $now = now();
                if ($now->gt($dueDate)) {
                    // Terlambat! Hitung dan SET fine_amount sekarang
                    // Gunakan floor() untuk bulatkan ke bawah agar konsisten
                    $daysLate = floor($dueDate->diffInDays($now));
                    $fineAmount = $daysLate * 1000;

                    // Set fine_amount ke database
                    $trx->fine_amount = $fineAmount;
                    $trx->save();

                    return true;
                }
            }

            return false;
        })->values();

        return response()->json([
            'message' => 'Daftar denda member',
            'data' => $list
        ], 200);
    }

    // POST /transactions/{id}/pay-fine
    // Member mengirim permintaan pembayaran denda (menunggu persetujuan admin)
    public function payFine(Request $request, $id)
    {
        $trx = Transaction::with(['book'])->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Pastikan transaksi milik user yang login
        if ($trx->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($trx->fine_amount <= 0) {
            return response()->json(['message' => 'Tidak ada denda untuk transaksi ini'], 400);
        }

        if ($trx->fine_paid_at) {
            return response()->json(['message' => 'Denda sudah dibayar'], 400);
        }

        if ($trx->fine_payment_requested_at) {
            return response()->json(['message' => 'Permintaan pembayaran sudah dikirim, menunggu persetujuan admin'], 400);
        }

        $trx->update(['fine_payment_requested_at' => now()]);

        return response()->json([
            'message' => 'Permintaan pembayaran denda dikirim, tunggu persetujuan admin',
            'data' => $trx->fresh(['book'])
        ], 200);
    }

    // GET /transactions/recommendations
    // Get book recommendations based on user's borrowing history
    public function recommendations(Request $request)
    {
        try {
            // Get user's most borrowed books
            $borrowedBooks = Transaction::with(['book'])
                ->where('user_id', $request->user()->id)
                ->whereIn('status', ['borrowed', 'returned'])
                ->whereNotNull('book_id')
                ->latest()
                ->take(5)
                ->get();

            // Collect subjects from borrowed books using Open Library API
            $subjects = [];
            foreach ($borrowedBooks as $transaction) {
                if ($transaction->book && $transaction->book->api_book_id) {
                    $apiUrl = "https://openlibrary.org/works/{$transaction->book->api_book_id}.json";

                    try {
                        $response = file_get_contents($apiUrl);
                        $bookData = json_decode($response, true);

                        if (isset($bookData['subjects'])) {
                            foreach (array_slice($bookData['subjects'], 0, 3) as $subject) {
                                $subjects[] = $subject;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            // Get most common subjects
            $subjectCounts = array_count_values($subjects);
            arsort($subjectCounts);
            $topSubjects = array_slice(array_keys($subjectCounts), 0, 3);

            // If no subjects found, use default genres
            if (empty($topSubjects)) {
                $topSubjects = ['fiction', 'science', 'history'];
            }

            // Fetch recommendations from Open Library based on top subjects
            $recommendations = [];
            foreach ($topSubjects as $subject) {
                $searchUrl = "https://openlibrary.org/subjects/" . urlencode(strtolower($subject)) . ".json?limit=5";

                try {
                    $response = file_get_contents($searchUrl);
                    $data = json_decode($response, true);

                    if (isset($data['works'])) {
                        foreach ($data['works'] as $work) {
                            $recommendations[] = [
                                'api_book_id' => str_replace('/works/', '', $work['key']),
                                'title' => $work['title'] ?? 'Unknown',
                                'author' => $work['authors'][0]['name'] ?? 'Unknown',
                                'subject' => $subject,
                                'cover_id' => $work['cover_id'] ?? null
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Remove duplicates and limit to 10
            $uniqueRecommendations = [];
            $seen = [];
            foreach ($recommendations as $book) {
                $key = $book['api_book_id'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $uniqueRecommendations[] = $book;
                }
                if (count($uniqueRecommendations) >= 10) break;
            }

            return response()->json([
                'message' => 'Rekomendasi buku berdasarkan riwayat peminjaman Anda',
                'subjects' => $topSubjects,
                'data' => $uniqueRecommendations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil rekomendasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ======================
    // ADMIN (LIBRARIAN)
    // ======================

    // GET /admin/transactions/pending-borrow
    public function pendingBorrow()
    {
        $list = Transaction::with(['user', 'book'])
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar peminjaman pending',
            'data'    => $list
        ], 200);
    }

    // PUT /admin/transactions/{id}/approve?days=7
    public function approveBorrow(Request $request, $id)
    {
        $trx = Transaction::with(['book', 'user'])->find($id);
        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($trx->status !== 'pending') {
            return response()->json(['message' => 'Transaksi bukan status pending'], 400);
        }

        if (!$trx->book) {
            return response()->json(['message' => 'Book not found for this transaction'], 404);
        }

        // wajib stok cukup
        if ($trx->book->stock < 1) {
            return response()->json([
                'message' => 'Stok buku habis. Silakan tambah stok di menu Kelola Buku.'
            ], 400);
        }

        // $days = (int) $request->query('days', 7);
        // if ($days < 1) $days = 7;

        $trx->update([
            'status'      => 'borrowed',
            // 'borrow_date' => now(),
            // 'due_date'    => now()->addDays($days),
            'approved_by' => $request->user()->id,
        ]);

        // kurangi stok buku
        $trx->book->decrement('stock');

        return response()->json([
            'message' => 'Peminjaman disetujui',
            'data'    => $trx->fresh(['user', 'book'])
        ], 200);
    }

    // GET /admin/transactions/borrowed
    public function borrowed()
    {
        $list = Transaction::with(['user', 'book'])
            ->where('status', 'borrowed')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar transaksi borrowed',
            'data' => $list
        ], 200);
    }

    // GET /admin/transactions/fines
    // List all transactions that have fines (filterable by status=paid|unpaid|all)
    public function adminFines(Request $request)
    {
        $status = $request->query('status', 'unpaid');

        $q = Transaction::with(['user', 'book'])->where('fine_amount', '>', 0);

        if ($status === 'unpaid') {
            $q->whereNull('fine_paid_at');
        } elseif ($status === 'paid') {
            $q->whereNotNull('fine_paid_at');
        }

        $list = $q->orderBy('id', 'desc')->get();

        return response()->json([
            'message' => 'Daftar denda (admin)',
            'data' => $list
        ], 200);
    }

    // PUT /admin/transactions/{id}/mark-fine-paid
    // Admin approves a payment request and marks fine as paid (sets fine_paid_at)
    public function adminMarkFinePaid(Request $request, $id)
    {
        $trx = Transaction::with(['user', 'book'])->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($trx->fine_amount <= 0) {
            return response()->json(['message' => 'Tidak ada denda untuk transaksi ini'], 400);
        }

        if ($trx->fine_paid_at) {
            return response()->json(['message' => 'Denda sudah dibayar sebelumnya'], 400);
        }

        if (!$trx->fine_payment_requested_at) {
            return response()->json(['message' => 'Belum ada permintaan pembayaran dari member'], 400);
        }

        $trx->update(['fine_paid_at' => now()]);

        return response()->json([
            'message' => 'Denda disetujui dan ditandai lunas oleh admin',
            'data' => $trx->fresh(['user', 'book'])
        ], 200);
    }

    // ======================
    // MEMBER - RETURN BOOK
    // ======================

    // PUT /transactions/{id}/return
    // Member request return => validasi denda, set fine_amount, status return_pending
    public function requestReturn(Request $request, $id)
    {
        $trx = Transaction::with(['book'])->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Pastikan transaksi milik user yang login
        if ($trx->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hanya bisa return jika status borrowed
        if ($trx->status !== 'borrowed') {
            return response()->json(['message' => 'Buku tidak sedang dipinjam'], 400);
        }

        // Hitung denda CURRENT berdasarkan keterlambatan
        $currentFine = 0;
        if ($trx->due_date) {
            $dueDate = \Carbon\Carbon::parse($trx->due_date);
            $now = now();
            if ($now->gt($dueDate)) {
                // Gunakan floor() untuk bulatkan ke bawah agar konsisten
                $daysLate = floor($dueDate->diffInDays($now));
                $currentFine = $daysLate * 1000; // Rp 1000 per hari
            }
        }

        // LOGIKA BARU: Cek apakah fine_amount sudah pernah di-set
        if ($trx->fine_amount == 0) {
            // PERTAMA KALI request return
            if ($currentFine > 0) {
                // Set fine_amount ke database
                $trx->update(['fine_amount' => $currentFine]);

                return response()->json([
                    'message' => 'Ada denda sebesar Rp ' . number_format($currentFine, 0, ',', '.') . '. Silakan bayar denda terlebih dahulu di halaman Denda sebelum mengajukan pengembalian.',
                    'fine_amount' => $currentFine,
                    'requires_payment' => true
                ], 400);
            }

            // Tidak ada denda, langsung bisa return
            $trx->update(['status' => 'return_pending']);

            return response()->json([
                'message' => 'Permintaan pengembalian berhasil. Silahkan tunggu konfirmasi petugas',
                'data' => $trx->fresh(['book'])
            ], 200);
        } else {
            // SUDAH PERNAH set fine_amount sebelumnya
            // Re-calculate untuk cek apakah denda bertambah

            // Toleransi: jika selisih < Rp 1.000 (kurang dari 1 hari), anggap tidak bertambah
            $difference = $currentFine - $trx->fine_amount;

            if ($difference >= 1000) {
                // DENDA BERTAMBAH minimal 1 hari! Update fine_amount dan tolak
                $trx->update(['fine_amount' => $currentFine]);

                return response()->json([
                    'message' => 'Denda bertambah dari Rp ' . number_format($trx->fine_amount, 0, ',', '.') . ' menjadi Rp ' . number_format($currentFine, 0, ',', '.') . '. Silakan bayar selisih denda sebesar Rp ' . number_format($difference, 0, ',', '.') . ' terlebih dahulu.',
                    'fine_increased' => true,
                    'previous_fine' => $trx->fine_amount,
                    'current_fine' => $currentFine,
                    'additional_fine' => $difference
                ], 400);
            }

            // Denda tidak bertambah, cek apakah sudah dibayar
            if (!$trx->fine_paid_at) {
                return response()->json([
                    'message' => 'Denda sebesar Rp ' . number_format($trx->fine_amount, 0, ',', '.') . ' belum dibayar. Silakan bayar denda terlebih dahulu di halaman Denda.',
                    'fine_amount' => $trx->fine_amount,
                    'requires_payment' => true
                ], 400);
            }

            // Denda sudah lunas, bisa return
            $trx->update(['status' => 'return_pending']);

            return response()->json([
                'message' => 'Permintaan pengembalian berhasil. Silahkan tunggu konfirmasi petugas',
                'data' => $trx->fresh(['book'])
            ], 200);
        }
    }

    // ======================
    // ADMIN - APPROVE RETURN
    // ======================

    // GET /admin/transactions/return-pending
    public function pendingReturn()
    {
        $list = Transaction::with(['user', 'book'])
            ->where('status', 'return_pending')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar pengembalian pending',
            'data' => $list
        ], 200);
    }

    // PUT /admin/transactions/{id}/approve-return
    public function approveReturn(Request $request, $id)
    {
        $trx = Transaction::with(['book'])->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($trx->status !== 'return_pending') {
            return response()->json(['message' => 'Transaksi bukan status return_pending'], 400);
        }

        // Validasi: jika ada denda, pastikan sudah dibayar
        if ($trx->fine_amount > 0 && !$trx->fine_paid_at) {
            return response()->json([
                'message' => 'Tidak bisa approve pengembalian. Denda sebesar Rp ' . number_format($trx->fine_amount, 0, ',', '.') . ' belum dibayar oleh member.'
            ], 400);
        }

        // Update status menjadi returned
        $trx->update([
            'status' => 'returned',
            'return_date' => now()
        ]);

        // Kembalikan stok buku
        if ($trx->book) {
            $trx->book->increment('stock');
        }

        return response()->json([
            'message' => 'Pengembalian buku disetujui' . ($trx->fine_amount > 0 ? ' (denda sudah lunas)' : ''),
            'data' => $trx->fresh(['user', 'book'])
        ], 200);
    }
}
