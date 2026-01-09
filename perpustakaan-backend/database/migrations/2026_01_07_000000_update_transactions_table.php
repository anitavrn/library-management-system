<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('rejected_reason')->nullable()->after('status');
        });

        // Update ENUM manually because Doctrine DBAL sometimes has issues with Enums or it's not installed
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'borrowed', 'return_pending', 'returned', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert enum
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'borrowed', 'return_pending', 'returned') NOT NULL DEFAULT 'pending'");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('rejected_reason');
        });
    }
};
