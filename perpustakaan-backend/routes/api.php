<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TransactionController;

// =======================
// AUTH
// =======================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// =======================
// PROTECTED (SANCTUM)
// =======================

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/books', [BookController::class, 'index']);

    Route::post('/transactions', [TransactionController::class, 'borrow']);
    Route::put('/transactions/{id}/return', [TransactionController::class, 'returnBook']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
