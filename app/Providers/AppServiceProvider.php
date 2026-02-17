<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Produk;
use App\Observers\ProdukObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Produk::observe(ProdukObserver::class);
        
        // Force HTTPS untuk ngrok atau production
        if (request()->header('x-forwarded-proto') === 'https' || 
            str_contains(request()->header('host') ?? '', 'ngrok') ||
            app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
