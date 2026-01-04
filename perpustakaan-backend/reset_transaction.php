<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "===== RESET TRANSAKSI ID 1 =====\n\n";

$trx = Transaction::find(1);

if ($trx) {
    // Reset fine_amount, fine_paid_at, fine_payment_requested_at
    $trx->fine_amount = 0;
    $trx->fine_paid_at = null;
    $trx->fine_payment_requested_at = null;

    // Set due_date 5 hari yang lalu
    $trx->due_date = now()->subDays(5)->format('Y-m-d 00:00:00');
    $trx->save();

    echo "✅ Transaksi ID 1 berhasil di-reset!\n\n";
    echo "Status: " . $trx->status . "\n";
    echo "Due Date: " . $trx->due_date . " (5 hari yang lalu)\n";
    echo "Fine Amount: Rp 0 (akan di-set otomatis)\n";
    echo "Fine Paid At: NULL\n\n";

    // Hitung expected fine
    $dueDate = \Carbon\Carbon::parse($trx->due_date);
    $now = now();
    $daysLate = floor($dueDate->diffInDays($now));
    $expectedFine = $daysLate * 1000;

    echo "Expected Fine: Rp " . number_format($expectedFine, 0, ',', '.') . " (terlambat " . $daysLate . " hari)\n";
    echo "\nSilakan test ulang flow-nya!\n";
} else {
    echo "Transaksi ID 1 tidak ditemukan\n";
}

echo "\n";
