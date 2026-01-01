<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminDashboardController;

// =======================
// AUTH (PUBLIC)
// =======================
Route::post('/register', [AuthController::class, 'register']); // opsional untuk admin
Route::post('/login', [AuthController::class, 'login']);

// =======================
// PROTECTED (LOGIN REQUIRED)
// =======================
Route::middleware('auth:sanctum')->group(function () {

    // =======================
    // USER INFO (MEMBER & ADMIN)
    // =======================
    Route::get('/me', [AuthController::class, 'me']);

    // =======================
    // MEMBER
    // =======================

    // Inventaris internal (buku fisik yang ada di perpustakaan)
    Route::get('/books', [BookController::class, 'index']);

    // Transaksi member
    Route::post('/transactions', [TransactionController::class, 'borrow']);
    Route::put('/transactions/{id}/return', [TransactionController::class, 'requestReturn']);
    // Lihat daftar pengembalian pending
    Route::get('/transactions/my', [TransactionController::class, 'myTransactions']);
    // Daftar denda dan bayar denda
    Route::get('/transactions/fines', [TransactionController::class, 'fines']);
    Route::post('/transactions/{id}/pay-fine', [TransactionController::class, 'payFine']);


    // Logout (member & admin)
    Route::post('/logout', [AuthController::class, 'logout']);

    // =======================
    // ADMIN / LIBRARIAN
    // =======================
    Route::prefix('admin')->middleware('is_admin')->group(function () {

        // Dashboard ringkasan
        Route::get('/dashboard/summary', [AdminDashboardController::class, 'summary']);

        // CRUD buku (inventaris internal)
        Route::get('/books', [BookController::class, 'index']);
        Route::post('/books', [BookController::class, 'store']);
        Route::get('/books/{id}', [BookController::class, 'show']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);

        // =======================
        // TRANSAKSI (ADMIN)
        // =======================

        // Lihat daftar peminjaman pending
        Route::get('/transactions/pending-borrow', [TransactionController::class, 'pendingBorrow']);

        // Konfirmasi / approve peminjaman
        Route::put('/transactions/{id}/approve', [TransactionController::class, 'approveBorrow']);

        // Lihat daftar transaksi yang sedang dipinjam
        Route::get('/transactions/borrowed', [TransactionController::class, 'borrowed']);

        // ADMIN: lihat daftar denda (paid / unpaid)
        Route::get('/transactions/fines', [TransactionController::class, 'adminFines']);
        // ADMIN: tandai denda lunas
        Route::put('/transactions/{id}/mark-fine-paid', [TransactionController::class, 'adminMarkFinePaid']);

        // Pengembalian buku
        Route::get('/transactions/return-pending', [TransactionController::class, 'pendingReturn']);
        Route::put('/transactions/{id}/approve-return', [TransactionController::class, 'approveReturn']);
    });
});
