<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function borrow(Request $request)
{
    $request->validate([
        'book_key' => 'required|string'
    ]);

    return response()->json([
        'message' => 'Buku berhasil dipinjam',
        'book_key' => $request->book_key,
        'user_id' => auth()->id()
    ]);
}


    public function returnBook($id){
        $trx = Transaction::findOrFail($id);
        $trx->update([
            'return_date'=>now(),
            'status'=>'returned'
        ]);
        return $trx;
    }
}

