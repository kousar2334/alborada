<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdsController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\MemberController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\UtilityController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\LocationController;
use App\Http\Controllers\Backend\UserAuthController;
use App\Http\Controllers\Backend\ConditionController;
use App\Http\Controllers\Backend\ContactUsController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\CustomFieldController;
use App\Http\Controllers\Backend\SiteSettingController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\PricingPlanController;
use App\Http\Controllers\Backend\ClassifiedSettingController;
use App\Http\Controllers\Backend\AdReportController;
use App\Http\Controllers\Backend\ReportReasonController;
use App\Http\Controllers\Backend\ConversationController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Backend\AdvertisementController;
use App\Http\Controllers\Backend\HomePageBuilderController;
use App\Http\Controllers\Backend\NewsletterController as BackendNewsletterController;
use App\Http\Controllers\Backend\PaymentSettingsController;
use App\Http\Controllers\Backend\BankPaymentController;

Route::prefix('admin')->group(function () {

    // Auth (guest only)
    Route::get('/login', [UserAuthController::class, 'login'])->name('admin.auth.login')->middleware('guest');
    Route::post('/login', [UserAuthController::class, 'loginAttempt'])->name('admin.auth.login.attempt')->middleware('guest');

    Route::group(['middleware' => ['auth', 'admin']], function () {

        // Profile
        Route::get('/profile', [UserAuthController::class, 'profile'])->name('admin.auth.profile');
        Route::post('/profile-update', [UserAuthController::class, 'profileUpdate'])->name('admin.auth.profile.update');
        Route::post('/password-update', [UserAuthController::class, 'passwordUpdate'])->name('admin.auth.password.update');
        Route::get('/logout', [UserAuthController::class, 'logout'])->name('admin.auth.logout');

        // Dashboard AJAX stats
        Route::post('business-stats', [DashboardController::class, 'businessStats'])->name('business.stats')
            ->middleware('can:View Dashboard');
        Route::post('ad--posting-stats', [DashboardController::class, 'adStats'])->name('reports.ad.chart')
            ->middleware('can:View Dashboard');
        Route::post('member-registration-stats', [DashboardController::class, 'memberStats'])->name('reports.member.chart')
            ->middleware('can:View Dashboard');

        /**
         * DASHBOARD
         */
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('admin.dashboard')
            ->middleware('can:View Dashboard');

        /**
         * MEMBERS MODULE
         */
        Route::prefix('members')->group(function () {
            Route::get('/', [MemberController::class, 'memberList'])->name('admin.members.list')
                ->middleware('can:Manage Members');
            Route::post('store', [MemberController::class, 'memberStore'])->name('admin.members.store')
                ->middleware('can:Create Member');
            Route::post('edit', [MemberController::class, 'memberEdit'])->name('admin.members.edit')
                ->middleware('can:Edit Member');
            Route::post('update', [MemberController::class, 'memberUpdate'])->name('admin.members.update')
                ->middleware('can:Edit Member');
            Route::post('reset/password', [MemberController::class, 'memberPasswordReset'])->name('admin.members.password.reset')
                ->middleware('can:Edit Member');
            Route::post('delete', [MemberController::class, 'memberDelete'])->name('admin.members.delete')
                ->middleware('can:Delete Member');
        });

        /**
         * Media Management (shared picker — requires at least media view access)
         */
        Route::post('/upload-media-file', [MediaController::class, 'uploadMediaFile'])->name('upload.media.file')
            ->middleware('can:Manage Media');
        Route::post('/media-items-list', [MediaController::class, 'mediaList'])->name('media.file.list')
            ->middleware('can:Manage Media');
        Route::post('/selected-media-details', [MediaController::class, 'selectedMediaDetails'])->name('media.selected.file.details')
            ->middleware('can:Manage Media');
    });
});
