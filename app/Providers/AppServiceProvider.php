<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        // Route model bindings: map URL parameters to Indonesian model classes
        Route::model('employee', \App\Models\Karyawan::class);
        Route::model('member', \App\Models\Anggota::class);
        Route::model('feedback', \App\Models\Saran::class);
    }
}
