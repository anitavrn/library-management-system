<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "===== CEK TRANSAKSI ID 1 =====\n\n";

$trx = Transaction::with(['book', 'user'])->find(1);

if ($trx) {
    echo "ID Transaksi: " . $trx->id . "\n";
    echo "User: " . $trx->user->username . " (" . $trx->user->email . ")\n";
    echo "Buku: " . $trx->book->title . "\n";
    echo "Status: " . $trx->status . "\n";
    echo "Borrow Date: " . $trx->borrow_date . "\n";
    echo "Due Date: " . $trx->due_date . "\n";
    echo "Fine Amount: Rp " . number_format($trx->fine_amount, 0, ',', '.') . "\n";
    echo "Fine Paid At: " . ($trx->fine_paid_at ?? 'NULL') . "\n";
    echo "Fine Payment Requested At: " . ($trx->fine_payment_requested_at ?? 'NULL') . "\n";

    echo "\n===== SIMULASI DENDA =====\n";
    echo "Hari ini: " . now()->format('Y-m-d') . "\n";

    if ($trx->due_date) {
        $dueDate = \Carbon\Carbon::parse($trx->due_date);
        $now = now();

        if ($now->gt($dueDate)) {
            $daysLate = $dueDate->diffInDays($now);
            $fineAmount = $daysLate * 1000;
            echo "Terlambat: " . $daysLate . " hari\n";
            echo "Denda yang harus dibayar: Rp " . number_format($fineAmount, 0, ',', '.') . "\n";
        } else {
            echo "Belum terlambat\n";
        }
    }
} else {
    echo "Transaksi ID 1 tidak ditemukan\n";
}

echo "\n";
