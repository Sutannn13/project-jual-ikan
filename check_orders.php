<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$orders = App\Models\Order::orderBy('id', 'desc')
    ->limit(10)
    ->get(['id', 'order_number', 'status', 'payment_proof', 'payment_uploaded_at', 'payment_method']);

echo "Recent Orders:\n";
echo str_repeat("=", 100) . "\n";
foreach ($orders as $order) {
    echo sprintf(
        "ID: %d | Order: %s | Status: %-18s | Proof: %-50s | Method: %s\n",
        $order->id,
        $order->order_number,
        $order->status,
        $order->payment_proof ?? '(null)',
        $order->payment_method ?? '(null)'
    );
}
echo str_repeat("=", 100) . "\n";
