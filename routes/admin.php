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

        // Notifications
        Route::get('/admin-notification-list', [NotificationController::class, 'adminNotifications'])->name('admin.notification.list');
        Route::post('/admin-notification-mark-as-read', [NotificationController::class, 'adminNotificationMarkAsRead'])->name('admin.notification.mark.as.read.single');
        Route::post('/admin-notification-mark-as-read-all', [NotificationController::class, 'adminAllNotificationMarkAsRead'])->name('admin.notification.mark.as.read.all');

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
         * ADS / LISTINGS MODULE
         */
        Route::prefix('listing')->group(function () {
            // List & view
            Route::get('/', [AdsController::class, 'adListing'])->name('classified.ads.list')
                ->middleware('can:Manage Ads');
            Route::get('/featured', [AdsController::class, 'featuredAdListing'])->name('classified.ads.list.featured')
                ->middleware('can:Manage Ads');
            Route::get('/get-states', [AdsController::class, 'getStates'])->name('classified.ads.get.states')
                ->middleware('can:Manage Ads');
            Route::get('/get-cities', [AdsController::class, 'getCities'])->name('classified.ads.get.cities')
                ->middleware('can:Manage Ads');
            // Edit
            Route::get('/edit/{id}', [AdsController::class, 'editAd'])->name('classified.ads.edit')
                ->middleware('can:Edit Ad');
            Route::post('/update', [AdsController::class, 'updateAd'])->name('classified.ads.update')
                ->middleware('can:Edit Ad');
            // Delete
            Route::post('/delete', [AdsController::class, 'deleteAd'])->name('classified.ads.delete')
                ->middleware('can:Delete Ad');

            // ── Ad Categories ─────────────────────────────────────────────
            Route::prefix('categories')->group(function () {
                Route::get('/', [CategoryController::class, 'categories'])->name('classified.ads.categories.list')
                    ->middleware('can:Manage Ad Categories');
                Route::get('options', [CategoryController::class, 'CategoryOption'])->name('classified.ads.categories.options')
                    ->middleware('can:Manage Ad Categories');
                Route::post('store', [CategoryController::class, 'categoryStore'])->name('classified.ads.categories.store')
                    ->middleware('can:Create Ad Category');
                Route::post('edit', [CategoryController::class, 'categoryEdit'])->name('classified.ads.categories.edit')
                    ->middleware('can:Edit Ad Category');
                Route::get('{id}/edit', [CategoryController::class, 'categoryEditPage'])->name('classified.ads.categories.edit.page')
                    ->middleware('can:Edit Ad Category');
                Route::post('update', [CategoryController::class, 'categoryUpdate'])->name('classified.ads.categories.update')
                    ->middleware('can:Edit Ad Category');
                Route::post('delete', [CategoryController::class, 'categoryDelete'])->name('classified.ads.categories.delete')
                    ->middleware('can:Delete Ad Category');
            });

            // ── Conditions ────────────────────────────────────────────────
            Route::prefix('condition')->group(function () {
                Route::get('/', [ConditionController::class, 'conditions'])->name('classified.ads.condition.list')
                    ->middleware('can:Manage Conditions');
                Route::post('store', [ConditionController::class, 'storeCondition'])->name('classified.ads.condition.store')
                    ->middleware('can:Create Condition');
                Route::post('edit', [ConditionController::class, 'editCondition'])->name('classified.ads.condition.edit')
                    ->middleware('can:Edit Condition');
                Route::get('edit/{id}', [ConditionController::class, 'editConditionPage'])->name('classified.ads.condition.edit.page')
                    ->middleware('can:Edit Condition');
                Route::post('update', [ConditionController::class, 'updateCondition'])->name('classified.ads.condition.update')
                    ->middleware('can:Edit Condition');
                Route::post('delete', [ConditionController::class, 'deleteCondition'])->name('classified.ads.condition.delete')
                    ->middleware('can:Delete Condition');
            });

            // ── Tags ──────────────────────────────────────────────────────
            Route::prefix('tag')->group(function () {
                Route::get('/', [TagController::class, 'tags'])->name('classified.ads.tag.list')
                    ->middleware('can:Manage Tags');
                Route::get('options', [TagController::class, 'tagOption'])->name('classified.ads.tag.options')
                    ->middleware('can:Manage Tags');
                Route::post('bulk-action', [TagController::class, 'tagBulkAction'])->name('classified.ads.tag.bulk.action')
                    ->middleware('can:Manage Tags');
                Route::post('store', [TagController::class, 'storeTag'])->name('classified.ads.tag.store')
                    ->middleware('can:Create Tag');
                Route::post('edit', [TagController::class, 'editTag'])->name('classified.ads.tag.edit')
                    ->middleware('can:Edit Tag');
                Route::post('update', [TagController::class, 'updateTag'])->name('classified.ads.tag.update')
                    ->middleware('can:Edit Tag');
                Route::post('delete', [TagController::class, 'deleteTag'])->name('classified.ads.tag.delete')
                    ->middleware('can:Delete Tag');
            });

            // ── Custom Fields ─────────────────────────────────────────────
            Route::prefix('custom-field')->group(function () {
                Route::get('/', [CustomFieldController::class, 'customFields'])->name('classified.ads.custom.field.list')
                    ->middleware('can:Manage Custom Fields');
                Route::post('assign-category', [CustomFieldController::class, 'assignCategory'])->name('classified.ads.custom.field.assign.category')
                    ->middleware('can:Manage Custom Fields');
                Route::post('bulk-action', [CustomFieldController::class, 'customFieldBulkAction'])->name('classified.ads.custom.field.bulk.action')
                    ->middleware('can:Manage Custom Fields');
                Route::get('options/{id}', [CustomFieldController::class, 'customFieldOptions'])->name('classified.ads.custom.field.options')
                    ->middleware('can:Manage Custom Fields');

                Route::post('store', [CustomFieldController::class, 'storeCustomField'])->name('classified.ads.custom.field.store')
                    ->middleware('can:Create Custom Field');

                Route::post('edit}', [CustomFieldController::class, 'editCustomField'])->name('classified.ads.custom.field.edit')
                    ->middleware('can:Edit Custom Field');
                Route::get('edit/{id}', [CustomFieldController::class, 'editCustomFieldPage'])->name('classified.ads.custom.field.edit.page')
                    ->middleware('can:Edit Custom Field');
                Route::post('update', [CustomFieldController::class, 'updateCustomField'])->name('classified.ads.custom.field.update')
                    ->middleware('can:Edit Custom Field');

                Route::post('delete', [CustomFieldController::class, 'deleteCustomField'])->name('classified.ads.custom.field.delete')
                    ->middleware('can:Delete Custom Field');

                // Custom Field Options (tied to Edit Custom Field)
                Route::post('options/store', [CustomFieldController::class, 'customFieldOptionStore'])->name('classified.ads.custom.field.options.store')
                    ->middleware('can:Edit Custom Field');
                Route::post('options/edit', [CustomFieldController::class, 'customFieldOptionEdit'])->name('classified.ads.custom.field.options.edit')
                    ->middleware('can:Edit Custom Field');
                Route::get('options/edit/{id}', [CustomFieldController::class, 'editOptionPage'])->name('classified.ads.custom.field.options.edit.page')
                    ->middleware('can:Edit Custom Field');
                Route::post('options/update', [CustomFieldController::class, 'customFieldOptionUpdate'])->name('classified.ads.custom.field.options.update')
                    ->middleware('can:Edit Custom Field');
                Route::post('options/bulk-action', [CustomFieldController::class, 'customFieldOptionBulkAction'])->name('classified.ads.custom.field.options.bulk.action')
                    ->middleware('can:Edit Custom Field');
                Route::post('options/delete', [CustomFieldController::class, 'customFieldOptionDelete'])->name('classified.ads.custom.field.options.delete')
                    ->middleware('can:Delete Custom Field');
            });

            // ── Ad Reports ────────────────────────────────────────────────
            Route::prefix('reports')->group(function () {
                Route::get('/', [AdReportController::class, 'index'])->name('classified.ads.reports.list')
                    ->middleware('can:Manage Ad Reports');
                Route::post('status', [AdReportController::class, 'updateStatus'])->name('classified.ads.reports.status')
                    ->middleware('can:Manage Ad Reports');
                Route::post('delete', [AdReportController::class, 'delete'])->name('classified.ads.reports.delete')
                    ->middleware('can:Delete Ad Report');
            });

            // ── Report Reasons ────────────────────────────────────────────
            Route::prefix('report-reasons')->group(function () {
                Route::get('/', [ReportReasonController::class, 'index'])->name('classified.ads.report.reasons.list')
                    ->middleware('can:Manage Report Reasons');
                Route::post('store', [ReportReasonController::class, 'store'])->name('classified.ads.report.reasons.store')
                    ->middleware('can:Create Report Reason');
                Route::get('edit/{id}', [ReportReasonController::class, 'edit'])->name('classified.ads.report.reasons.edit')
                    ->middleware('can:Edit Report Reason');
                Route::post('update', [ReportReasonController::class, 'update'])->name('classified.ads.report.reasons.update')
                    ->middleware('can:Edit Report Reason');
                Route::post('delete', [ReportReasonController::class, 'delete'])->name('classified.ads.report.reasons.delete')
                    ->middleware('can:Delete Report Reason');
            });
        });

        /**
         * PRICING PLANS MODULE
         */
        Route::prefix('pricing-plans')->group(function () {
            Route::get('/', [PricingPlanController::class, 'plans'])->name('admin.pricing.plans.list')
                ->middleware('can:Manage Pricing Plans');
            Route::post('store', [PricingPlanController::class, 'planStore'])->name('admin.pricing.plans.store')
                ->middleware('can:Create Pricing Plan');
            Route::post('edit', [PricingPlanController::class, 'planEdit'])->name('admin.pricing.plans.edit')
                ->middleware('can:Edit Pricing Plan');
            Route::post('update', [PricingPlanController::class, 'planUpdate'])->name('admin.pricing.plans.update')
                ->middleware('can:Edit Pricing Plan');
            Route::post('delete', [PricingPlanController::class, 'planDelete'])->name('admin.pricing.plans.delete')
                ->middleware('can:Delete Pricing Plan');
        });

        /**
         * SUBSCRIPTIONS MODULE
         */
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('admin.subscriptions.list')
                ->middleware('can:Manage Subscriptions');
            Route::post('approve', [SubscriptionController::class, 'approve'])->name('admin.subscriptions.approve')
                ->middleware('can:Approve Subscription');
            Route::post('reject', [SubscriptionController::class, 'reject'])->name('admin.subscriptions.reject')
                ->middleware('can:Reject Subscription');
            Route::post('delete', [SubscriptionController::class, 'delete'])->name('admin.subscriptions.delete')
                ->middleware('can:Delete Subscription');
        });

        /**
         * BANK PAYMENTS MODULE
         */
        Route::prefix('bank-payments')->group(function () {
            Route::get('/', [BankPaymentController::class, 'index'])->name('admin.bank.payments')
                ->middleware('can:Manage Bank Payments');
            Route::post('approve', [BankPaymentController::class, 'approve'])->name('admin.bank.payments.approve')
                ->middleware('can:Approve Bank Payment');
            Route::post('reject', [BankPaymentController::class, 'reject'])->name('admin.bank.payments.reject')
                ->middleware('can:Reject Bank Payment');
        });

        /**
         * PAYMENT SETTINGS MODULE
         */
        Route::prefix('payment-settings')->group(function () {
            Route::get('/', [PaymentSettingsController::class, 'index'])->name('admin.payment.settings')
                ->middleware('can:Manage Payment Settings');
            Route::post('update', [PaymentSettingsController::class, 'update'])->name('admin.payment.settings.update')
                ->middleware('can:Manage Payment Settings');
        });

        /**
         * LOCATIONS MODULE
         */
        Route::prefix('location')->group(function () {
            // Countries
            Route::prefix('country')->group(function () {
                Route::get('', [LocationController::class, 'countries'])->name('classified.locations.country.list')
                    ->middleware('can:Manage Locations');
                Route::post('store/new', [LocationController::class, 'storeNewCountry'])->name('classified.locations.country.store')
                    ->middleware('can:Create Location');
                Route::post('edit', [LocationController::class, 'editCountry'])->name('classified.locations.country.edit')
                    ->middleware('can:Edit Location');
                Route::post('update', [LocationController::class, 'updateCountry'])->name('classified.locations.country.update')
                    ->middleware('can:Edit Location');
                Route::post('delete', [LocationController::class, 'deleteCountry'])->name('classified.locations.country.delete')
                    ->middleware('can:Delete Location');
            });
            // States
            Route::prefix('state')->group(function () {
                Route::get('', [LocationController::class, 'states'])->name('classified.locations.state.list')
                    ->middleware('can:Manage Locations');
                Route::post('store/new', [LocationController::class, 'storeNewState'])->name('classified.locations.state.store')
                    ->middleware('can:Create Location');
                Route::post('edit', [LocationController::class, 'editState'])->name('classified.locations.state.edit')
                    ->middleware('can:Edit Location');
                Route::post('update', [LocationController::class, 'updateState'])->name('classified.locations.state.update')
                    ->middleware('can:Edit Location');
                Route::post('delete', [LocationController::class, 'deleteState'])->name('classified.locations.state.delete')
                    ->middleware('can:Delete Location');
            });
            // Cities
            Route::prefix('city')->group(function () {
                Route::get('', [LocationController::class, 'cities'])->name('classified.locations.city.list')
                    ->middleware('can:Manage Locations');
                Route::post('store/new', [LocationController::class, 'storeNewCity'])->name('classified.locations.city.store')
                    ->middleware('can:Create Location');
                Route::post('edit', [LocationController::class, 'editCity'])->name('classified.locations.city.edit')
                    ->middleware('can:Edit Location');
                Route::post('update', [LocationController::class, 'updateCity'])->name('classified.locations.city.update')
                    ->middleware('can:Edit Location');
                Route::post('delete', [LocationController::class, 'deleteCity'])->name('classified.locations.city.delete')
                    ->middleware('can:Delete Location');
            });
        });

        /**
         * SAFETY TIPS MODULE
         */
        Route::prefix('safety-tips')->group(function () {
            Route::get('/', [ClassifiedSettingController::class, 'safetyTips'])->name('classified.settings.safety.tips.list')
                ->middleware('can:Manage Safety Tips');
            Route::post('/store', [ClassifiedSettingController::class, 'storeSafetyTips'])->name('classified.settings.safety.tips.store')
                ->middleware('can:Create Safety Tip');
            Route::get('/edit/{id}', [ClassifiedSettingController::class, 'editSafetyTips'])->name('classified.settings.safety.tips.edit')
                ->middleware('can:Edit Safety Tip');
            Route::post('/update', [ClassifiedSettingController::class, 'updateSafetyTips'])->name('classified.settings.safety.tips.update')
                ->middleware('can:Edit Safety Tip');
            Route::post('/delete', [ClassifiedSettingController::class, 'deleteSafetyTips'])->name('classified.settings.safety.tips.delete')
                ->middleware('can:Delete Safety Tip');
        });

        /**
         * ADVERTISEMENTS (BANNER ADS) MODULE
         */
        Route::prefix('advertisement')->group(function () {
            Route::get('/', [AdvertisementController::class, 'index'])->name('admin.advertisement.list')
                ->middleware('can:Manage Advertisements');
            Route::get('{id}/analytics', [AdvertisementController::class, 'analytics'])->name('admin.advertisement.analytics')
                ->middleware('can:Manage Advertisements');
            Route::post('store', [AdvertisementController::class, 'store'])->name('admin.advertisement.store')
                ->middleware('can:Create Advertisement');
            Route::post('edit', [AdvertisementController::class, 'edit'])->name('admin.advertisement.edit')
                ->middleware('can:Edit Advertisement');
            Route::post('update', [AdvertisementController::class, 'update'])->name('admin.advertisement.update')
                ->middleware('can:Edit Advertisement');
            Route::post('delete', [AdvertisementController::class, 'delete'])->name('admin.advertisement.delete')
                ->middleware('can:Delete Advertisement');
        });

        /**
         * NEWSLETTER MODULE
         */
        Route::prefix('newsletter')->group(function () {
            // Subscribers
            Route::get('subscribers', [BackendNewsletterController::class, 'subscribers'])->name('admin.newsletter.subscribers')
                ->middleware('can:Manage Newsletter');
            Route::post('subscribers/delete', [BackendNewsletterController::class, 'deleteSubscriber'])->name('admin.newsletter.subscribers.delete')
                ->middleware('can:Delete Newsletter Subscriber');

            // Campaigns
            Route::get('campaigns', [BackendNewsletterController::class, 'campaigns'])->name('admin.newsletter.campaigns')
                ->middleware('can:Manage Newsletter');
            Route::get('campaigns/create', [BackendNewsletterController::class, 'createCampaign'])->name('admin.newsletter.campaigns.create')
                ->middleware('can:Create Newsletter Campaign');
            Route::post('campaigns/store', [BackendNewsletterController::class, 'storeCampaign'])->name('admin.newsletter.campaigns.store')
                ->middleware('can:Create Newsletter Campaign');
            Route::get('campaigns/{id}/edit', [BackendNewsletterController::class, 'editCampaign'])->name('admin.newsletter.campaigns.edit')
                ->middleware('can:Edit Newsletter Campaign');
            Route::post('campaigns/{id}/update', [BackendNewsletterController::class, 'updateCampaign'])->name('admin.newsletter.campaigns.update')
                ->middleware('can:Edit Newsletter Campaign');
            Route::post('campaigns/delete', [BackendNewsletterController::class, 'deleteCampaign'])->name('admin.newsletter.campaigns.delete')
                ->middleware('can:Delete Newsletter Campaign');
            Route::post('campaigns/{id}/send', [BackendNewsletterController::class, 'sendCampaign'])->name('admin.newsletter.campaigns.send')
                ->middleware('can:Send Newsletter Campaign');
            Route::get('campaigns/{id}/stats', [BackendNewsletterController::class, 'campaignStats'])->name('admin.newsletter.campaigns.stats')
                ->middleware('can:Manage Newsletter');
        });

        /**
         * CONTACT MESSAGES MODULE
         */
        Route::get('messages', [ContactUsController::class, 'messages'])->name('admin.contact.us.message.list')
            ->middleware('can:Manage Message');
        Route::post('messages/reply', [ContactUsController::class, 'replyMessage'])->name('admin.contact.us.message.reply')
            ->middleware('can:Reply Message');
        Route::post('delete/message', [ContactUsController::class, 'deleteMessage'])->name('admin.contact.us.message.delete')
            ->middleware('can:Delete Message');

        /**
         * CONVERSATIONS MODULE
         */
        Route::prefix('conversations')->group(function () {
            Route::get('/', [ConversationController::class, 'index'])->name('admin.conversations.index')
                ->middleware('can:Manage Conversations');
            Route::get('{uid}', [ConversationController::class, 'show'])->name('admin.conversations.show')
                ->middleware('can:Manage Conversations');
        });

        /**
         * MEDIA MODULE
         */
        Route::get('media-manage', [MediaController::class, 'mediaManager'])->name('admin.media.list')
            ->middleware('can:Manage Media');
        Route::post('delete/media', [MediaController::class, 'deleteMedia'])->name('admin.media.delete')
            ->middleware('can:Delete Media');

        /**
         * BLOGS MODULE
         */
        Route::prefix('blogs')->group(function () {
            // Blog Categories
            Route::get('categories', [BlogController::class, 'categoriesList'])->name('admin.blogs.categories.list')
                ->middleware('can:Manage Blog Category');
            Route::get('category-dropdown-options', [BlogController::class, 'categoryDropdownOptions'])->name('admin.blogs.categories.dropdown.options')
                ->middleware('can:Manage Blog Category');
            Route::post('store-new-category', [BlogController::class, 'storeNewCategory'])->name('admin.blogs.categories.store')
                ->middleware('can:Create Blog Category');
            Route::get('edit-category/{id}', [BlogController::class, 'editCategory'])->name('admin.blogs.categories.edit')
                ->middleware('can:Edit Blog Category');
            Route::post('category-update', [BlogController::class, 'updateCategory'])->name('admin.blogs.categories.update')
                ->middleware('can:Edit Blog Category');
            Route::post('delete-category', [BlogController::class, 'deleteCategory'])->name('admin.blogs.categories.delete')
                ->middleware('can:Delete Blog Category');

            // Blog Tags dropdown (utility — requires at least blog access)
            Route::get('tags-dropdown-options', [BlogController::class, 'tagsDropdownOptions'])->name('admin.blogs.tags.dropdown.options')
                ->middleware('can:Manage Blog');

            // Blogs
            Route::get('/', [BlogController::class, 'blogList'])->name('admin.blogs.list')
                ->middleware('can:Manage Blog');
            Route::get('create-new-blog', [BlogController::class, 'createNewBlog'])->name('admin.blogs.create')
                ->middleware('can:Create New Blog');
            Route::post('store-new-blog', [BlogController::class, 'storeNewBlog'])->name('admin.blogs.new.store')
                ->middleware('can:Create New Blog');
            Route::get('edit/{blog}', [BlogController::class, 'editBlog'])->name('admin.blogs.edit')
                ->middleware('can:Edit Blog');
            Route::post('update-blog', [BlogController::class, 'updateBlog'])->name('admin.blogs.update')
                ->middleware('can:Edit Blog');
            Route::post('delete-blog', [BlogController::class, 'deleteBlog'])->name('admin.blogs.delete')
                ->middleware('can:Delete Blog');

            // Blog Comments
            Route::get('/comments', [BlogController::class, 'blogComments'])->name('admin.blogs.comment.list')
                ->middleware('can:Manage Blog');
            Route::post('/comment-delete', [BlogController::class, 'blogCommentDelete'])->name('admin.blogs.comment.delete')
                ->middleware('can:Delete Blog Comment');
        });

        /**
         * PAGES MODULE
         */
        Route::prefix('pages')->group(function () {
            Route::get('/', [PageController::class, 'pageList'])->name('admin.page.list')
                ->middleware('can:Manage Pages');
            Route::get('create-new-page', [PageController::class, 'createNewPage'])->name('admin.page.create')
                ->middleware('can:Create New Page');
            Route::post('store-new-page', [PageController::class, 'storeNewPage'])->name('admin.page.new.store')
                ->middleware('can:Create New Page');
            Route::get('edit/{page}', [PageController::class, 'editPage'])->name('admin.page.edit')
                ->middleware('can:Edit Page');
            Route::post('update-page', [PageController::class, 'updatePage'])->name('admin.page.update')
                ->middleware('can:Edit Page');
            Route::post('delete-page', [PageController::class, 'deletePage'])->name('admin.page.delete')
                ->middleware('can:Delete Page');
        });

        /**
         * APPEARANCES MODULE
         */
        Route::prefix('appearance')->group(function () {
            // Menus
            Route::get('/menus', [MenuController::class, 'menus'])->name('admin.appearance.menu.builder')
                ->middleware('can:Manage Menu');
            Route::post('/menu-management', [MenuController::class, 'menuManagement'])->name('admin.appearance.menu.builder.menu.management')
                ->middleware('can:Manage Menu');
            Route::post('/add-menu-items', [MenuController::class, 'addMenuItems'])->name('admin.appearance.menu.builder.add.menu.items')
                ->middleware('can:Manage Menu');
            Route::post('/remove-menu-item', [MenuController::class, 'removeMenuItem'])->name('admin.appearance.menu.builder.remove.menu.item')
                ->middleware('can:Manage Menu');
            Route::post('/update-menu-item', [MenuController::class, 'updateMenuItem'])->name('admin.appearance.menu.builder.update.menu.item')
                ->middleware('can:Manage Menu');
            Route::post('/delete-menu', [MenuController::class, 'deleteMenu'])->name('admin.appearance.menu.builder.delete.menu')
                ->middleware('can:Delete Menu');

            // Site Settings
            Route::get('/site-setting', [SiteSettingController::class, 'siteSetting'])->name('admin.appearance.site.setting')
                ->middleware('can:Manage Site Settings');
            Route::post('/site-setting-update', [SiteSettingController::class, 'siteSettingUpdate'])->name('admin.appearance.site.setting.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/footer-setting', [SiteSettingController::class, 'footerSetting'])->name('admin.appearance.site.setting.footer')
                ->middleware('can:Manage Site Settings');
            Route::post('/footer-setting-update', [SiteSettingController::class, 'footerSettingUpdate'])->name('admin.appearance.site.setting.footer.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/seo-setting', [SiteSettingController::class, 'seoSetting'])->name('admin.appearance.site.setting.seo')
                ->middleware('can:Manage Site Settings');
            Route::post('/seo-setting-update', [SiteSettingController::class, 'seoSettingUpdate'])->name('admin.appearance.site.setting.seo.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/colors', [SiteSettingController::class, 'colorSetting'])->name('admin.appearance.site.setting.colors')
                ->middleware('can:Manage Site Settings');
            Route::post('/colors-update', [SiteSettingController::class, 'colorSettingUpdate'])->name('admin.appearance.site.setting.colors.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/custom-css', [SiteSettingController::class, 'customCssSetting'])->name('admin.appearance.site.setting.custom.css')
                ->middleware('can:Manage Site Settings');
            Route::post('/custom-css-update', [SiteSettingController::class, 'customCssSettingUpdate'])->name('admin.appearance.site.setting.custom.css.update')
                ->middleware('can:Manage Site Settings');
        });

        /**
         * HOME PAGE BUILDER MODULE
         */
        Route::prefix('home-builder')->group(function () {
            Route::get('/', [HomePageBuilderController::class, 'index'])->name('admin.home.builder')
                ->middleware('can:Manage Home Builder');
            Route::post('update-order', [HomePageBuilderController::class, 'updateOrder'])->name('admin.home.builder.order')
                ->middleware('can:Manage Home Builder');
            Route::post('toggle-active', [HomePageBuilderController::class, 'toggleActive'])->name('admin.home.builder.toggle')
                ->middleware('can:Manage Home Builder');
            Route::post('update-content', [HomePageBuilderController::class, 'updateContent'])->name('admin.home.builder.content')
                ->middleware('can:Manage Home Builder');
        });

        /**
         * ADMIN USERS / ROLES / PERMISSIONS MODULE
         */
        // Permissions
        Route::get('permissions', [UserController::class, 'permissions'])->name('admin.users.permission.list')
            ->middleware('can:Permission List View');

        // Roles
        Route::get('roles', [UserController::class, 'roles'])->name('admin.users.role.list')
            ->middleware('can:Role List View');
        Route::post('store-new-role', [UserController::class, 'storeNewRole'])->name('admin.users.role.store')
            ->middleware('can:Role Create');
        Route::post('edit-role', [UserController::class, 'editRole'])->name('admin.users.role.edit')
            ->middleware('can:Role Edit');
        Route::post('update-role', [UserController::class, 'updateRole'])->name('admin.users.role.update')
            ->middleware('can:Role Edit');
        Route::post('delete-role', [UserController::class, 'deleteRole'])->name('admin.users.role.delete')
            ->middleware('can:Role Delete');

        // Users
        Route::get('users', [UserController::class, 'users'])->name('admin.users.list')
            ->middleware('can:User List');
        Route::post('store-new-user', [UserController::class, 'storeNewUser'])->name('admin.users.store')
            ->middleware('can:User Create');
        Route::post('edit-user', [UserController::class, 'editUser'])->name('admin.users.edit')
            ->middleware('can:User Edit');
        Route::post('update-user', [UserController::class, 'updateUser'])->name('admin.users.update')
            ->middleware('can:User Edit');
        Route::post('delete-user', [UserController::class, 'deleteUser'])->name('admin.users.delete')
            ->middleware('can:User Delete');

        /**
         * SYSTEM MODULE
         */
        Route::prefix('system')->group(function () {
            // Environment
            Route::get('environment-setup', [SettingController::class, 'environmentSettings'])->name('admin.system.settings.environment')
                ->middleware('can:Update Environment');
            Route::post('environment-setup-update', [SettingController::class, 'environmentSettingsUpdate'])->name('admin.system.settings.environment.update')
                ->middleware('can:Update Environment');

            // SMTP
            Route::get('smtp-setup', [SettingController::class, 'smtpSettings'])->name('admin.system.settings.smtp')
                ->middleware('can:Update SMTP');
            Route::post('smtp-setup-update', [SettingController::class, 'smtpSettingsUpdate'])->name('admin.system.settings.smtp.update')
                ->middleware('can:Update SMTP');
            Route::post('send-test-mail', [SettingController::class, 'testMail'])->name('admin.system.settings.smtp.mail.test')
                ->middleware('can:Update SMTP');

            // Social Login
            Route::get('social-login', [SettingController::class, 'socialLogin'])->name('admin.system.settings.social.login')
                ->middleware('can:Manage Social Login');
            Route::post('social-login-update', [SettingController::class, 'socialLoginUpdate'])->name('admin.system.settings.social.login.update')
                ->middleware('can:Manage Social Login');

            // Language
            Route::get('languages', [LanguageController::class, 'language'])->name('admin.system.settings.language.list')
                ->middleware('can:Manage Language');
            Route::post('store-language', [LanguageController::class, 'languageStore'])->name('admin.system.settings.language.store')
                ->middleware('can:Add Language');
            Route::post('edit-language', [LanguageController::class, 'languageEdit'])->name('admin.system.settings.language.edit')
                ->middleware('can:Edit Language');
            Route::post('update-language', [LanguageController::class, 'languageUpdate'])->name('admin.system.settings.language.update')
                ->middleware('can:Edit Language');
            Route::post('delete-language', [LanguageController::class, 'languageDelete'])->name('admin.system.settings.language.delete')
                ->middleware('can:Delete Language');
            Route::get('translation/{id}', [LanguageController::class, 'LanguageKeys'])->name('admin.system.settings.language.translation')
                ->middleware('can:Manage Language');
            Route::post('translation-update', [LanguageController::class, 'translationUpdate'])->name('admin.system.settings.language.translation.update')
                ->middleware('can:Edit Language');
            // Set language session — auth only, no extra permission
            Route::get('set-language/{code}', [LanguageController::class, 'setSessionLanguage'])->name('admin.system.settings.language.set');
        });

        /**
         * CLASSIFIED SETTINGS MODULE
         */
        Route::prefix('classified-settings')->group(function () {
            Route::get('general', [ClassifiedSettingController::class, 'generalSettings'])->name('classified.settings.general')
                ->middleware('can:Manage General Settings');
            Route::get('currency', [ClassifiedSettingController::class, 'currencySettings'])->name('classified.settings.currency')
                ->middleware('can:Manage Currency Settings');
            Route::get('member', [ClassifiedSettingController::class, 'memberSettings'])->name('classified.settings.member')
                ->middleware('can:Manage Member Settings');
            Route::post('member/update', [ClassifiedSettingController::class, 'updateMemberSetting'])->name('classified.member.settings.update')
                ->middleware(['can:Manage Member Settings', 'demo']);
            Route::get('ads', [ClassifiedSettingController::class, 'adsSettings'])->name('classified.settings.ads')
                ->middleware('can:Manage Ads Settings');
            Route::get('map', [ClassifiedSettingController::class, 'mapSettings'])->name('classified.settings.map')
                ->middleware('can:Manage Map Settings');

            // Quick Sell Tips
            Route::get('quick-sell-tips', [ClassifiedSettingController::class, 'quickSellTips'])->name('classified.settings.quick.sell.tips.list')
                ->middleware('can:Manage Quick Sell Tips');
            Route::post('store-quick-sell-tips', [ClassifiedSettingController::class, 'storeQuickSellTips'])->name('classified.settings.quick.sell.tips.store')
                ->middleware(['can:Create Quick Sell Tip', 'demo']);
            Route::get('edit-quick-sell-tips/{id}', [ClassifiedSettingController::class, 'editQuickSellTips'])->name('classified.settings.quick.sell.tips.edit')
                ->middleware('can:Edit Quick Sell Tip');
            Route::post('update-quick-sell-tips', [ClassifiedSettingController::class, 'updateQuickSellTips'])->name('classified.settings.quick.sell.tips.update')
                ->middleware(['can:Edit Quick Sell Tip', 'demo']);
            Route::post('delete-quick-sell-tips', [ClassifiedSettingController::class, 'deleteQuickSellTips'])->name('classified.settings.quick.sell.tips.delete')
                ->middleware(['can:Delete Quick Sell Tip', 'demo']);

            // Ad Share Options
            Route::get('share-options', [ClassifiedSettingController::class, 'shareOptions'])->name('classified.settings.share.options.list')
                ->middleware('can:Manage Ad Share Options');
            Route::post('share-option-update-status', [ClassifiedSettingController::class, 'shareOptionUpdateStatus'])->name('classified.settings.share.options.status.update')
                ->middleware(['can:Manage Ad Share Options', 'demo']);

            Route::post('update', [ClassifiedSettingController::class, 'updateSetting'])->name('classified.settings.update');
        });
    });
});

/**
 * Utility (auth + admin required)
 */
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/email-sending', [UtilityController::class, 'sendingEmail'])->name('utility.email.send');
    Route::get('/admin/clear-system-cache', [UtilityController::class, 'clearCache'])->name('utility.clear.cache')
        ->middleware('can:Update Environment');
    Route::post('/store-summer-note-image', [UtilityController::class, 'storeEditorImage'])->name('utility.store.editor.image');

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
