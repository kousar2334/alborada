<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ResellerController;
use App\Http\Controllers\Api\FeaturedContentController;
use App\Http\Middleware\LogApiRequest;

// Public API (no auth) — consumed by player apps for promo/commercial content
Route::prefix('v1')->group(function () {
    Route::get('/featured-content', [FeaturedContentController::class, 'index']);
});

// Reseller API (Sanctum token-based)
Route::prefix('reseller/v1')->middleware(['auth:sanctum', LogApiRequest::class])->group(function () {
    Route::get('/clients',            [ResellerController::class, 'listClients']);
    Route::post('/clients/create',    [ResellerController::class, 'createClient']);
    Route::post('/clients/suspend',   [ResellerController::class, 'suspendClient']);
    Route::post('/clients/reactivate', [ResellerController::class, 'reactivateClient']);
    Route::get('/credits',            [ResellerController::class, 'creditBalance']);
    Route::get('/plans',              [ResellerController::class, 'availablePlans']);
});
