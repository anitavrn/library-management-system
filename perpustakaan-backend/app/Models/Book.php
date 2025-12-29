<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'api_book_id',
        'title',
        'author',
        'publisher',
        'year',
        'stock',
    ];
}
