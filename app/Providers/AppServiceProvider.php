<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Tambahkan ini
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\PengaturanDisplay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Paginator::useTailwind();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('pengaturan_display')) {
                    $view->with('pengaturanDisplay', PengaturanDisplay::getSetting());
                }
            } catch (\Throwable $e) {}
        });
    }
}
