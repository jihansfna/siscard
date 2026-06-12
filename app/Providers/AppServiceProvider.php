<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

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
        if (
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            str_starts_with(config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }

        // Route model bindings: map URL parameters to Indonesian model classes
        Route::model('employee', \App\Models\Karyawan::class);
        Route::model('member', \App\Models\Anggota::class);
        Route::model('feedback', \App\Models\Saran::class);
    }
}
