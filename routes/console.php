<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel expired orders every hour
Schedule::command('orders:cancel-expired')->hourly();

// Confirm stock for COD orders every 30 minutes
Schedule::command('orders:confirm-cod-stock')->everyThirtyMinutes();

// Notify admins of expiring stock every morning at 7 AM
Schedule::command('stock:notify-expiring --days=3')->dailyAt('07:00');

// Critical check: notify if stock expires tomorrow (run twice daily)
Schedule::command('stock:notify-expiring --days=1')->twiceDaily(7, 14);
