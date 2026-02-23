<?php

namespace App\Console\Commands;

use App\Models\StockIn;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyExpiringStock extends Command
{
    protected $signature   = 'stock:notify-expiring {--days=3 : Notify if expiring within this many days}';
    protected $description = 'Send notifications for stock that is expiring soon or already expired';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $expiring = StockIn::with('produk')
            ->whereNotNull('expiry_date')
            ->where('expiry_notified', false)
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->get();

        $count = 0;

        foreach ($expiring as $stockIn) {
            try {
                $produk  = $stockIn->produk;
                $status  = $stockIn->expiry_status;
                $label   = match ($status) {
                    'expired'  => 'KADALUWARSA',
                    'critical' => 'Kritis (1 hari)',
                    'warning'  => "Peringatan ({$days} hari)",
                    default    => 'Mendekati kedaluwarsa',
                };

                $message = "⚠️ Stok Ikan: {$produk?->nama} — {$stockIn->qty} Kg\n" .
                           "Status: {$label}\n" .
                           "Tanggal Kedaluwarsa: {$stockIn->expiry_date->format('d/m/Y')}\n" .
                           "Supplier: " . ($stockIn->supplier ?? '-') . "\n" .
                           "Segera periksa & pisahkan stok ini!";

                // Create admin notification
                \App\Models\AdminNotification::create([
                    'type'    => 'stock_expiry',
                    'title'   => "Stok {$label}: {$produk?->nama}",
                    'message' => $message,
                    'data'    => json_encode(['stock_in_id' => $stockIn->id, 'status' => $status]),
                    'is_read' => false,
                ]);

                // Mark as notified
                $stockIn->update(['expiry_notified' => true]);
                $count++;

                $this->info("Notified: {$produk?->nama} — {$label} (Exp: {$stockIn->expiry_date->format('d/m/Y')})");
            } catch (\Exception $e) {
                Log::error("Failed to notify expiring stock #{$stockIn->id}: " . $e->getMessage());
                $this->error("Failed: stock_in #{$stockIn->id}");
            }
        }

        if ($count > 0) {
            $this->info("Sent {$count} expiry notification(s).");
        } else {
            $this->info("No expiring stock to notify.");
        }

        return Command::SUCCESS;
    }
}
