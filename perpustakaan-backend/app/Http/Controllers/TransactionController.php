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
    // Smart Borrow Logic: Auto-Approve if in stock, Pending if new, Error if stock 0
    public function borrow(Request $request)
    {
        $data = $request->validate([
            'api_book_id'  => 'required|string',
            'title'        => 'required|string',
            'author'       => 'nullable|string',
            'borrow_date'  => 'required|date',
            'due_date'     => 'required|date|after:borrow_date',
        ]);

        // 1. Cek apakah buku sudah ada di sistem
        $book = Book::where('api_book_id', $data['api_book_id'])->first();

        if ($book) {
            // SCENARIO A: Buku terdaftar & stok tersedia -> AUTO APPROVE
            if ($book->stock > 0) {
                $trx = Transaction::create([
                    'user_id'     => $request->user()->id,
                    'book_id'     => $book->id,
                    'status'      => 'borrowed',
                    'borrow_date' => $data['borrow_date'],
                    'due_date'    => $data['due_date'],
                    'approved_by' => 1, // System approved
                ]);

                $book->decrement('stock');

                return response()->json([
                    'message' => 'Peminjaman berhasil! Buku tersedia dan status Anda otomatis disetujui.',
                    'status_type' => 'auto_approved',
                    'data'    => $trx->load(['book'])
                ], 201);
            } 
            
            // SCENARIO B: Buku terdaftar tapi stok HABIS
            return response()->json([
                'message' => 'Maaf, stok buku ini sedang kosong di perpustakaan kami.',
                'status_type' => 'out_of_stock'
            ], 400);
        }

        // SCENARIO C: Buku BELUM ADA (Request baru) -> PENDING
        $newBook = Book::create([
            'api_book_id' => $data['api_book_id'],
            'title'       => $data['title'],
            'author'      => $data['author'] ?? 'Unknown',
            'publisher'   => 'Unknown',
            'year'        => date('Y'),
            'stock'       => 0, // Request awal 0
        ]);

        $trx = Transaction::create([
            'user_id'     => $request->user()->id,
            'book_id'     => $newBook->id,
            'status'      => 'pending',
            'borrow_date' => $data['borrow_date'],
            'due_date'    => $data['due_date'],
        ]);

        return response()->json([
            'message' => 'Buku ini belum ada di koleksi kami. Permintaan pengadaan telah dikirim ke librarian.',
            'status_type' => 'pending_request',
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

    public function fines(Request $request)
    {
        $user = $request->user();
        $list = Transaction::with(['book'])
            ->where('user_id', $user->id)
            ->whereNull('fine_paid_at') // Hanya yang belum lunas
            ->where(function($q) {
                // 1. Ada denda yang sudah di-lock tapi belum dibayar
                $q->where('fine_amount', '>', 0)
                // 2. ATAU sedang dipinjam dan sudah lewat tenggat (hourly precision)
                ->orWhere(function($sq) {
                    $sq->where('status', 'borrowed')
                       ->where('due_date', '<', now());
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar denda member',
            'data'    => $list
        ], 200);
    }

    public function payFine(Request $request, $id)
    {
        $trx = Transaction::find($id);
        if (!$trx) return response()->json(['message' => 'Transaction not found'], 404);
        if ($trx->user_id !== $request->user()->id) return response()->json(['message' => 'Unauthorized'], 403);

        // LOCK-IN: Jika fine_amount masih 0 tapi sudah lewat tenggat, simpan angka estimasinya
        if ($trx->fine_amount == 0 && $trx->due_date && now()->gt($trx->due_date)) {
            $dueDate = \Carbon\Carbon::parse($trx->due_date);
            $hoursLate = $dueDate->diffInHours(now());
            $daysLate = ceil($hoursLate / 24);
            if ($daysLate == 0) $daysLate = 1;
            $trx->fine_amount = $daysLate * 1000;
        }

        $trx->update([
            'fine_payment_requested_at' => now(),
            'fine_amount' => $trx->fine_amount
        ]);

        return response()->json([
            'message' => 'Permintaan konfirmasi pembayaran denda berhasil dikirim',
            'data'    => $trx
        ], 200);
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

    // PUT /admin/transactions/{id}/approve
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
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        // 🔴 JIKA STOK HABIS → TOLAK & UPDATE STATUS
        if ($trx->book->stock < 1) {
            $trx->update([
                'status' => 'rejected',
                'rejected_reason' => 'stok_habis',
            ]);

            return response()->json([
                'message' => 'Stok buku habis. Peminjaman ditolak.'
            ], 200);
        }

        // ✅ JIKA STOK ADA → SETUJUI
        $trx->update([
            'status'      => 'borrowed',
            'approved_by' => $request->user()->id,
        ]);

        // kurangi stok buku
        $trx->book->decrement('stock');

        return response()->json([
            'message' => 'Peminjaman disetujui',
            'data'    => $trx->fresh(['user', 'book'])
        ], 200);
    }

    // PUT /admin/transactions/{id}/reject
    public function rejectBorrow(Request $request, $id)
    {
        $trx = Transaction::with(['book', 'user'])->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($trx->status !== 'pending') {
            return response()->json(['message' => 'Transaksi bukan status pending'], 400);
        }

        $trx->update([
            'status'          => 'rejected',
            'rejected_reason' => $request->input('reason', 'Ditolak admin'),
            'approved_by'     => $request->user()->id, // Admin yang menolak
        ]);

        return response()->json([
            'message' => 'Peminjaman ditolak',
            'data'    => $trx->fresh()
        ], 200);
    }

    // ======================
    // MEMBER - RETURN BOOK
    // ======================

    // PUT /transactions/{id}/return
    public function requestReturn(Request $request, $id)
    {
        $trx = Transaction::with(['book'])->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($trx->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($trx->status !== 'borrowed') {
            return response()->json(['message' => 'Buku tidak sedang dipinjam'], 400);
        }

        $currentFine = 0;

        if ($trx->due_date) {
            $dueDate = \Carbon\Carbon::parse($trx->due_date);
            if (now()->gt($dueDate)) {
                // Gunakan selisih jam dan ceil untuk menghitung denda harian (telat 1 jam = denda 1 hari)
                $hoursLate = $dueDate->diffInHours(now());
                $daysLate = ceil($hoursLate / 24);
                if ($daysLate == 0) $daysLate = 1; // Jika telat menit/jam tapi pembulatan nol
                $currentFine = $daysLate * 1000;
            }
        }

        if ($trx->fine_amount == 0) {
            if ($currentFine > 0) {
                $trx->update(['fine_amount' => $currentFine]);
                return response()->json([
                    'message' => 'Ada denda sebesar Rp ' . number_format($currentFine, 0, ',', '.') . '. Silakan bayar denda terlebih dahulu.',
                    'requires_payment' => true
                ], 400);
            }

            $trx->update([
                'status' => 'returned',
                'return_date' => now()
            ]);
            
            // Tambahkan stok buku kembali secara otomatis
            if ($trx->book) {
                $trx->book->increment('stock');
            }

            return response()->json([
                'message' => 'Buku berhasil dikembalikan! Stok telah diperbarui.',
                'data' => $trx->fresh(['book'])
            ], 200);
        }

        if (!$trx->fine_paid_at) {
            return response()->json([
                'message' => 'Denda belum dibayar. Silakan bayar terlebih dahulu.',
                'requires_payment' => true
            ], 400);
        }

        $trx->update([
            'status' => 'returned',
            'return_date' => now()
        ]);

        if ($trx->book) {
            $trx->book->increment('stock');
        }

        return response()->json([
            'message' => 'Buku berhasil dikembalikan! Stok telah diperbarui.',
            'data' => $trx->fresh(['book'])
        ], 200);
    }

    // ======================
    // ADMIN - LISTS
    // ======================

    // GET /admin/transactions/borrowed
    public function borrowed()
    {
        $list = Transaction::with(['user', 'book'])
            ->where('status', 'borrowed')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar buku sedang dipinjam',
            'data'    => $list
        ], 200);
    }

    // GET /admin/transactions/return-pending
    public function pendingReturn()
    {
        $list = Transaction::with(['user', 'book'])
            ->where('status', 'return_pending')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar pengembalian pending',
            'data'    => $list
        ], 200);
    }

    // GET /admin/transactions/fines
    public function adminFines()
    {
        $list = Transaction::with(['user', 'book'])
            ->whereNull('fine_paid_at') // Hanya yang belum lunas
            ->where(function($q) {
                // Tampilkan jika:
                // 1. Ada denda yang sudah dicatat (locked)
                // 2. ATAU ada permintaan pembayaran
                // 3. ATAU statusnya sedang dipinjam tapi sudah lewat tenggat
                $q->where('fine_amount', '>', 0)
                  ->orWhereNotNull('fine_payment_requested_at')
                  ->orWhere(function($sq) {
                      $sq->where('status', 'borrowed')
                         ->where('due_date', '<', now());
                  });
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar denda admin',
            'data'    => $list
        ], 200);
    }

    // GET /admin/transactions/rejected
    public function rejected()
    {
        $list = Transaction::with(['user', 'book'])
            ->where('status', 'rejected')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Riwayat penolakan',
            'data'    => $list
        ], 200);
    }

    // PUT /admin/transactions/{id}/update-reason
    public function updateRejectReason(Request $request, $id)
    {
        $trx = Transaction::find($id);
        if (!$trx) return response()->json(['message' => 'Transaction not found'], 404);

        $trx->update([
            'rejected_reason' => $request->input('reason', $trx->rejected_reason)
        ]);

        return response()->json([
            'message' => 'Alasan penolakan diperbarui',
            'data'    => $trx
        ], 200);
    }

    // PUT /admin/transactions/{id}/undo-reject
    public function undoReject($id)
    {
        $trx = Transaction::find($id);
        if (!$trx) return response()->json(['message' => 'Transaction not found'], 404);

        if ($trx->status !== 'rejected') {
            return response()->json(['message' => 'Hanya transaksi rejected yang bisa di-undo'], 400);
        }

        $trx->update([
            'status' => 'pending',
            'rejected_reason' => null
        ]);

        return response()->json([
            'message' => 'Penolakan dibatalkan, status kembali ke pending',
            'data'    => $trx
        ], 200);
    }

    // DELETE /admin/transactions/{id}
    public function destroy($id)
    {
        $trx = Transaction::find($id);
        if (!$trx) return response()->json(['message' => 'Transaction not found'], 404);

        $trx->delete();
        return response()->json(['message' => 'Log transaksi berhasil dihapus'], 200);
    }

    // PUT /admin/transactions/{id}/mark-fine-paid
    public function adminMarkFinePaid(Request $request, $id)
    {
        $trx = Transaction::with('book')->find($id);

        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Tandai denda lunas
        $trx->update(['fine_paid_at' => now()]);

        // AUTOMATIC RETURN: Jika buku masih status 'borrowed', otomatis jadikan 'returned'
        // Sesuai permintaan: "immediate return once fines are cleared"
        if ($trx->status === 'borrowed') {
            $trx->update([
                'status' => 'returned',
                'return_date' => now()
            ]);

            if ($trx->book) {
                $trx->book->increment('stock');
            }
        }

        return response()->json([
            'message' => 'Denda berhasil ditandai lunas dan buku otomatis dikembalikan',
            'data'    => $trx->fresh(['user', 'book'])
        ], 200);
    }

    // GET /transactions/recommendations (MEMBER)
    public function recommendations(Request $request)
    {
        // 1. Ambil riwayat peminjaman user untuk cari preferensi
        $borrowedBooks = Transaction::with('book')
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['borrowed', 'returned', 'return_pending'])
            ->latest()
            ->limit(10)
            ->get();

        $queryAuthor = 'novel'; // default pencarian

        if ($borrowedBooks->isNotEmpty()) {
            // Ambil penulis dari buku yang paling sering/terakhir dipinjam
            $authors = $borrowedBooks->pluck('book.author')->filter()->unique();
            if ($authors->isNotEmpty()) {
                $queryAuthor = $authors->first();
            }
        }

        try {
            // 2. Cari buku sejenis berdasarkan penulis via Open Library API
            $response = \Illuminate\Support\Facades\Http::get("https://openlibrary.org/search.json", [
                'author' => $queryAuthor,
                'limit'  => 12
            ]);

            if ($response->successful()) {
                $docs = $response->json('docs') ?? [];
                
                $recommendations = collect($docs)->map(function($item) {
                    return [
                        'api_book_id' => str_replace('/works/', '', $item['key'] ?? ''),
                        'title'       => $item['title'] ?? 'Unknown Title',
                        'author'      => $item['author_name'][0] ?? 'Unknown',
                        'cover_id'    => $item['cover_i'] ?? null,
                    ];
                })->filter(fn($b) => !empty($b['api_book_id']))->values();

                return response()->json([
                    'message' => 'Rekomendasi buku berdasarkan minat Anda',
                    'data'    => $recommendations
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memuat rekomendasi'], 500);
        }

        return response()->json(['message' => 'Tidak ada rekomendasi saat ini', 'data' => []], 200);
    }

    // PUT /admin/transactions/{id}/approve-return
    public function approveReturn(Request $request, $id)
    {
        $trx = Transaction::with('book')->find($id);
        if (!$trx) return response()->json(['message' => 'Transaction not found'], 404);

        if ($trx->status !== 'return_pending') {
            return response()->json(['message' => 'Hanya status return_pending yang bisa disetujui'], 400);
        }

        $trx->update([
            'status'      => 'returned',
            'return_date' => now()
        ]);

        if ($trx->book) {
            $trx->book->increment('stock');
        }

        return response()->json([
            'message' => 'Pengembalian buku berhasil disetujui',
            'data'    => $trx->fresh(['user', 'book'])
        ], 200);
    }
}
