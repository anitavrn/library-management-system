<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;

class AdminDashboardController extends Controller
{
    public function summary()
    {
        $totalBooks = Book::count();
        $borrowPending = Transaction::where('status','pending')->count();
        $returnPending = Transaction::where('status','return_pending')->count();
        $borrowedActive = Transaction::where('status','borrowed')->count();
        $unpaidFine = Transaction::where('fine_amount','>',0)->whereNull('fine_paid_at')->count();

        return response()->json([
            'total_books' => $totalBooks,
            'borrow_pending' => $borrowPending,
            'return_pending' => $returnPending,
            'borrowed_active' => $borrowedActive,
            'unpaid_fine_count' => $unpaidFine,
        ], 200);
    }
}
