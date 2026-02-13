<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
    }
}
