<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'full_name' => 'Librarian Admin',
                'username'  => 'librarian',
                'role'      => 'admin',
                'password'  => Hash::make('admin123'),
            ]
        );
    }
}
