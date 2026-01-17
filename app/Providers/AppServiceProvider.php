<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api_requests_rate_limiter', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())->response(function () {
                // Return a JSON response with a 429 status code and a message
                return response()->json([
                    'status' => 'ko',
                    'message' => 'Too many requests. Please try again later.',
                ], 429);
            });
        });
    }
}
