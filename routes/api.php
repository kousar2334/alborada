<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdsController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\UtilityController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
//api/classified/v1
Route::group(['prefix' => 'classified/v1'], function () {
    Route::post('parent-categories', [AdsController::class, 'parentCategories']);
    Route::post('categories', [AdsController::class, 'categories']);
    Route::post('all-categories', [AdsController::class, 'allCategories']);
    Route::post('category-details', [AdsController::class, 'categoryDetails']);
    Route::post('conditions', [AdsController::class, 'conditions']);
    Route::post('tags', [AdsController::class, 'tags']);
    Route::post('custom-field', [AdsController::class, 'customField']);
    Route::post('all-ads', [AdsController::class, 'AdListing']);
    Route::post('ad-details', [AdsController::class, 'adDetails']);
    Route::post('similar-ads', [AdsController::class, 'similarAds']);
    Route::post('store-image', [UtilityController::class, 'storeImage']);
    Route::get('safety-tips', [UtilityController::class, 'safetyTips']);
    Route::get('quick-sell-tips', [UtilityController::class, 'quickSellTips']);
    Route::get('ad-share-options', [UtilityController::class, 'shareOptions']);

    //api/classified/v1
    Route::group(['middleware' => 'auth:jwt-customer'], function () {
        Route::get('dashboard-overview', [DashboardController::class, 'customerDashboardOverview']);
        Route::post('calculate-payable-amount', [AdsController::class, 'calculatePayable']);
        Route::post('post-ad', [AdsController::class, 'storeMemberAd']);
        Route::post('update-ad', [AdsController::class, 'updateMemberAd']);
        Route::post('update-ad-status', [AdsController::class, 'updateMemberAdStatus']);
        Route::post('generate-payment-link', [AdsController::class, 'generatePaymentLink']);
        Route::post('edit-ad', [AdsController::class, 'editAd']);
        Route::post('customer-all-ads', [AdsController::class, 'customerAllAds']);
        Route::post('save-ad', [AdsController::class, 'saveAd']);
        Route::post('customer-saved-ads', [AdsController::class, 'savedAdList']);
        Route::post('remove-save-ad', [AdsController::class, 'removeSaveAd']);
        Route::post('delete-my-ad', [AdsController::class, 'deleteMemberAd']);

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
