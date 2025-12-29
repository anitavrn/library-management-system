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
            'api_book_id' => 'required|string',   // contoh: OL505944W
            'title'       => 'required|string',
            'author'      => 'nullable|string',
        ]);

        // cari buku internal berdasarkan api_book_id
        $book = Book::where('api_book_id', $data['api_book_id'])->first();

        // kalau belum ada -> buat otomatis (stok 0, nanti admin atur)
        if (!$book) {
            $book = Book::create([
                'api_book_id' => $data['api_book_id'],
                'title'       => $data['title'],
                'author'      => $data['author'] ?? null,
                'stock'       => 0,
                'location'    => null,
            ]);
        } else {
            // optional: update title/author kalau berubah dari API
            $book->update([
                'title'  => $data['title'],
                'author' => $data['author'] ?? $book->author,
            ]);
        }

        // buat transaksi pending
        $trx = Transaction::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'status'  => 'pending',
        ]);

        return response()->json([
            'message' => 'Request peminjaman dikirim (menunggu konfirmasi librarian)',
            'data'    => $trx->load(['book'])
        ], 201);
    }

    // ✅ TAMBAHAN FIX: riwayat transaksi member dari database (bukan localStorage)
    // GET /transactions/my
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

        $days = (int) $request->query('days', 7);
        if ($days < 1) $days = 7;

        $trx->update([
            'status'      => 'borrowed',
            'borrow_date' => now(),
            'due_date'    => now()->addDays($days),
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
}
