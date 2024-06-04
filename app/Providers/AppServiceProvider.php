<?php

namespace App\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

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
        Http::macro('igdb', function () {
            return Http::withHeaders([
                'Accept'        => 'application/json',
                'Client-ID'     => config('services.igdb.client_id'),
                'Authorization' => 'Bearer '.config('services.igdb.access_token'),
            ])->baseUrl('https://api.igdb.com/v4/');
        });

        PendingRequest::macro('igdb', function () {
            return $this->withHeaders([
                'Accept'        => 'application/json',
                'Client-ID'     => config('services.igdb.client_id'),
                'Authorization' => 'Bearer '.config('services.igdb.access_token'),
            ])->baseUrl('https://api.igdb.com/v4/');
        });
    }
}
