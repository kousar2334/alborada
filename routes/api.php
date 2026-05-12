<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\UtilityController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ResellerController;
use App\Http\Middleware\LogApiRequest;

Route::group(['prefix' => 'classified/v1'], function () {
    Route::get('safety-tips', [UtilityController::class, 'safetyTips']);
    Route::get('quick-sell-tips', [UtilityController::class, 'quickSellTips']);
    Route::post('store-image', [UtilityController::class, 'storeImage']);

    Route::group(['middleware' => 'auth:jwt-customer'], function () {
        Route::get('dashboard-overview', [DashboardController::class, 'customerDashboardOverview']);

        //api/classified/v1/notifications
        Route::group(['prefix' => 'notifications'], function () {
            Route::post('list', [NotificationController::class, 'customerAllNotifications']);
            Route::post('mark-as-read-single-notification', [NotificationController::class, 'markAsRead']);
            Route::get('mark-as-read-all-notification', [NotificationController::class, 'markAsReadAllNotification']);
            Route::post('delete-notification', [NotificationController::class, 'deleteNotification']);
        });

        //api/classified/v1/chat
        Route::group(['prefix' => 'chat'], function () {
            Route::post('create-chat', [ChatController::class, 'createChat']);
            Route::post('list', [ChatController::class, 'chatList']);
            Route::post('details', [ChatController::class, 'chatDetails']);
            Route::post('store-new-message', [ChatController::class, 'storeNewMessage']);
            Route::post('delete', [ChatController::class, 'deleteChat']);
        });
    });
});

// Reseller API (Sanctum token-based)
Route::prefix('reseller/v1')->middleware(['auth:sanctum', LogApiRequest::class])->group(function () {
    Route::get('/clients',            [ResellerController::class, 'listClients']);
    Route::post('/clients/create',    [ResellerController::class, 'createClient']);
    Route::post('/clients/suspend',   [ResellerController::class, 'suspendClient']);
    Route::post('/clients/reactivate',[ResellerController::class, 'reactivateClient']);
    Route::get('/credits',            [ResellerController::class, 'creditBalance']);
    Route::get('/plans',              [ResellerController::class, 'availablePlans']);
});
