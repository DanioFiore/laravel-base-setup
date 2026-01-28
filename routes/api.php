<?php

use Illuminate\Support\Facades\Route;

// api versioning
Route::prefix('v1')->middleware('throttle:api_requests_rate_limiter')->group(base_path('routes/api_v1.php'));
