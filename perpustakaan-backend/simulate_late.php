<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "===== UPDATE TRANSAKSI ID 1 (SIMULASI TERLAMBAT) =====\n\n";

$trx = Transaction::find(1);

if ($trx) {
    echo "BEFORE:\n";
    echo "Due Date: " . $trx->due_date . "\n";
    echo "Fine Amount: Rp " . number_format($trx->fine_amount, 0, ',', '.') . "\n";

    // Update due_date jadi 5 hari yang lalu
    $newDueDate = now()->subDays(5)->format('Y-m-d H:i:s');
    $trx->due_date = $newDueDate;
    $trx->save();

    echo "\nAFTER:\n";
    echo "Due Date: " . $trx->due_date . " (5 hari yang lalu)\n";
    echo "Fine Amount: Rp " . number_format($trx->fine_amount, 0, ',', '.') . " (belum di-set)\n";

    // Hitung denda yang seharusnya
    $dueDate = \Carbon\Carbon::parse($trx->due_date);
    $now = now();
    $daysLate = $dueDate->diffInDays($now);
    $expectedFine = $daysLate * 1000;

    echo "\nESKPEKTASI:\n";
    echo "Terlambat: " . $daysLate . " hari\n";
    echo "Denda yang harus dibayar: Rp " . number_format($expectedFine, 0, ',', '.') . "\n";
    echo "\n✅ Transaksi ID 1 sekarang TERLAMBAT 5 hari!\n";
    echo "Silakan buka return.html untuk test flow denda.\n";
} else {
    echo "Transaksi ID 1 tidak ditemukan\n";
}

echo "\n";
