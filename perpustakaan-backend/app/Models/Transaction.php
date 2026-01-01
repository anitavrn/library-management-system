<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id','book_id','status',
        'borrow_date','due_date','return_date',
        'approved_by','fine_amount','fine_paid_at','fine_payment_requested_at'
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'fine_paid_at' => 'datetime',
        'fine_payment_requested_at' => 'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function book(){ return $this->belongsTo(Book::class); }
}
