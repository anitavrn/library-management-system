<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run()
    {
        Book::create([
            'title'     => 'Pemrograman Web',
            'author'    => 'Budi Raharjo',
            'publisher' => 'Informatika',
            'year'      => 2021,
            'stock'     => 5
        ]);

        Book::create([
            'title'     => 'Algoritma dan Struktur Data',
            'author'    => 'Rinaldi Munir',
            'publisher' => 'Informatika',
            'year'      => 2019,
            'stock'     => 3
        ]);
    }
}
