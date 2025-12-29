<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();

            $table->enum('status', ['pending', 'borrowed', 'return_pending', 'returned'])->default('pending');

            $table->dateTime('borrow_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('return_date')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable(); // admin id
            $table->integer('fine_amount')->default(0);
            $table->dateTime('fine_paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
