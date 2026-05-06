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
use App\Http\Controllers\Backend\MissingModuleController;
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
         * NOTIFICATIONS
         */
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'adminNotifications'])->name('admin.notification.list');
            Route::post('/mark-as-read', [NotificationController::class, 'adminNotificationMarkAsRead'])->name('admin.notification.mark.as.read.single');
            Route::post('/mark-as-read-all', [NotificationController::class, 'adminAllNotificationMarkAsRead'])->name('admin.notification.mark.as.read.all');
        });

        /**
         * UTILITIES
         */
        Route::prefix('utilities')->group(function () {
            Route::post('/store-editor-image', [UtilityController::class, 'storeEditorImage'])->name('utility.store.editor.image');
            Route::get('/clear-cache', [UtilityController::class, 'clearCache'])->name('utility.clear.cache');
        });

        Route::get('/core/page', fn () => to_route('admin.page.list'))->name('core.page');
        Route::get('/core/email/smtp-configuration', fn () => to_route('admin.system.settings.smtp'))->name('core.email.smtp.configuration');

        /**
         * LANGUAGES
         */
        Route::prefix('system/settings/languages')->group(function () {
            Route::get('/', [LanguageController::class, 'language'])->name('admin.system.settings.language.list');
            Route::post('/store', [LanguageController::class, 'languageStore'])->name('admin.system.settings.language.store');
            Route::post('/edit', [LanguageController::class, 'languageEdit'])->name('admin.system.settings.language.edit');
            Route::post('/update', [LanguageController::class, 'languageUpdate'])->name('admin.system.settings.language.update');
            Route::post('/delete', [LanguageController::class, 'languageDelete'])->name('admin.system.settings.language.delete');
            Route::get('/translation/{id}', [LanguageController::class, 'LanguageKeys'])->name('admin.system.settings.language.translation');
            Route::post('/translation/update', [LanguageController::class, 'translationUpdate'])->name('admin.system.settings.language.translation.update');
            Route::get('/set/{code}', [LanguageController::class, 'setSessionLanguage'])->name('admin.system.settings.language.set');
        });

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
         * PRICING PLANS MODULE
         */
        Route::prefix('pricing-plans')->group(function () {
            Route::get('/', [PricingPlanController::class, 'index'])->name('admin.pricing.plans.list');
            Route::post('store', [PricingPlanController::class, 'store'])->name('admin.pricing.plans.store');
            Route::post('edit', [PricingPlanController::class, 'edit'])->name('admin.pricing.plans.edit');
            Route::post('update', [PricingPlanController::class, 'update'])->name('admin.pricing.plans.update');
            Route::post('delete', [PricingPlanController::class, 'destroy'])->name('admin.pricing.plans.delete');
        });

        /**
         * SUBSCRIPTIONS MODULE
         */
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('admin.subscriptions.list');
            Route::post('approve', [SubscriptionController::class, 'approve'])->name('admin.subscriptions.approve');
            Route::post('reject', [SubscriptionController::class, 'reject'])->name('admin.subscriptions.reject');
            Route::post('delete', [SubscriptionController::class, 'delete'])->name('admin.subscriptions.delete');
        });

        /**
         * BLOGS MODULE
         */
        Route::prefix('blogs')->group(function () {
            Route::get('/', [BlogController::class, 'blogList'])->name('admin.blogs.list')
                ->middleware('can:Manage Blog');
            Route::get('/create', [BlogController::class, 'createNewBlog'])->name('admin.blogs.create')
                ->middleware('can:Create New Blog');
            Route::post('/store', [BlogController::class, 'storeNewBlog'])->name('admin.blogs.new.store')
                ->middleware('can:Create New Blog');
            Route::get('/{blog}/edit', [BlogController::class, 'editBlog'])->name('admin.blogs.edit')
                ->middleware('can:Edit Blog');
            Route::post('/update', [BlogController::class, 'updateBlog'])->name('admin.blogs.update')
                ->middleware('can:Edit Blog');
            Route::post('/delete', [BlogController::class, 'deleteBlog'])->name('admin.blogs.delete')
                ->middleware('can:Delete Blog');

            Route::get('/comments', [BlogController::class, 'blogComments'])->name('admin.blogs.comment.list')
                ->middleware('can:Manage Blog');
            Route::post('/comments/delete', [BlogController::class, 'blogCommentDelete'])->name('admin.blogs.comment.delete')
                ->middleware('can:Delete Blog Comment');

            Route::get('/categories', [BlogController::class, 'categoriesList'])->name('admin.blogs.categories.list')
                ->middleware('can:Manage Blog Category');
            Route::post('/categories/store', [BlogController::class, 'storeNewCategory'])->name('admin.blogs.categories.store')
                ->middleware('can:Create Blog Category');
            Route::get('/categories/{id}/edit', [BlogController::class, 'editCategory'])->name('admin.blogs.categories.edit')
                ->middleware('can:Edit Blog Category');
            Route::post('/categories/update', [BlogController::class, 'updateCategory'])->name('admin.blogs.categories.update')
                ->middleware('can:Edit Blog Category');
            Route::post('/categories/delete', [BlogController::class, 'deleteCategory'])->name('admin.blogs.categories.delete')
                ->middleware('can:Delete Blog Category');
            Route::post('/categories/dropdown-options', [BlogController::class, 'categoryDropdownOptions'])->name('admin.blogs.categories.dropdown.options')
                ->middleware('can:Manage Blog Category');
            Route::post('/tags/dropdown-options', [BlogController::class, 'tagsDropdownOptions'])->name('admin.blogs.tags.dropdown.options')
                ->middleware('can:Manage Blog');
        });

        /**
         * PAGES MODULE
         */
        Route::prefix('pages')->group(function () {
            Route::get('/', [PageController::class, 'pageList'])->name('admin.page.list')
                ->middleware('can:Manage Pages');
            Route::get('/create', [PageController::class, 'createNewPage'])->name('admin.page.create')
                ->middleware('can:Create New Page');
            Route::post('/store', [PageController::class, 'storeNewPage'])->name('admin.page.new.store')
                ->middleware('can:Create New Page');
            Route::get('/{page}/edit', [PageController::class, 'editPage'])->name('admin.page.edit')
                ->middleware('can:Edit Page');
            Route::post('/update', [PageController::class, 'updatePage'])->name('admin.page.update')
                ->middleware('can:Edit Page');
            Route::post('/delete', [PageController::class, 'deletePage'])->name('admin.page.delete')
                ->middleware('can:Delete Page');
        });

        /**
         * USERS MODULE
         */
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'users'])->name('admin.users.list')
                ->middleware('can:User List');
            Route::post('/store', [UserController::class, 'storeNewUser'])->name('admin.users.store')
                ->middleware('can:User Create');
            Route::post('/edit', [UserController::class, 'editUser'])->name('admin.users.edit')
                ->middleware('can:User Edit');
            Route::post('/update', [UserController::class, 'updateUser'])->name('admin.users.update')
                ->middleware('can:User Edit');
            Route::post('/delete', [UserController::class, 'deleteUser'])->name('admin.users.delete')
                ->middleware('can:User Delete');

            Route::get('/permissions', [UserController::class, 'permissions'])->name('admin.users.permission.list')
                ->middleware('can:Permission List View');
            Route::get('/roles', [UserController::class, 'roles'])->name('admin.users.role.list')
                ->middleware('can:Role List View');
            Route::post('/roles/store', [UserController::class, 'storeNewRole'])->name('admin.users.role.store')
                ->middleware('can:Role Create');
            Route::post('/roles/edit', [UserController::class, 'editRole'])->name('admin.users.role.edit')
                ->middleware('can:Role Edit');
            Route::post('/roles/update', [UserController::class, 'updateRole'])->name('admin.users.role.update')
                ->middleware('can:Role Edit');
            Route::post('/roles/delete', [UserController::class, 'deleteRole'])->name('admin.users.role.delete')
                ->middleware('can:Role Delete');
        });

        /**
         * SYSTEM SETTINGS
         */
        Route::prefix('system/settings')->group(function () {
            Route::get('/environment', [SettingController::class, 'environmentSettings'])->name('admin.system.settings.environment')
                ->middleware('can:Update Environment');
            Route::post('/environment/update', [SettingController::class, 'environmentSettingsUpdate'])->name('admin.system.settings.environment.update')
                ->middleware('can:Update Environment');
            Route::get('/smtp', [SettingController::class, 'smtpSettings'])->name('admin.system.settings.smtp')
                ->middleware('can:Update SMTP');
            Route::post('/smtp/update', [SettingController::class, 'smtpSettingsUpdate'])->name('admin.system.settings.smtp.update')
                ->middleware('can:Update SMTP');
            Route::post('/smtp/test-mail', [SettingController::class, 'testMail'])->name('admin.system.settings.smtp.mail.test')
                ->middleware('can:Update SMTP');
            Route::get('/social-login', [SettingController::class, 'socialLogin'])->name('admin.system.settings.social.login')
                ->middleware('can:Manage Social Login');
            Route::post('/social-login/update', [SettingController::class, 'socialLoginUpdate'])->name('admin.system.settings.social.login.update')
                ->middleware('can:Manage Social Login');
        });

        /**
         * APPEARANCE
         */
        Route::prefix('appearance')->group(function () {
            Route::get('/menu-builder', [MenuController::class, 'menus'])->name('admin.appearance.menu.builder')
                ->middleware('can:Manage Menu');
            Route::post('/menu-builder/manage', [MenuController::class, 'menuManagement'])->name('admin.appearance.menu.builder.menu.management')
                ->middleware('can:Manage Menu');
            Route::post('/menu-builder/add-items', [MenuController::class, 'addMenuItems'])->name('admin.appearance.menu.builder.add.menu.items')
                ->middleware('can:Manage Menu');
            Route::post('/menu-builder/remove-item', [MenuController::class, 'removeMenuItem'])->name('admin.appearance.menu.builder.remove.menu.item')
                ->middleware('can:Manage Menu');
            Route::post('/menu-builder/update-item', [MenuController::class, 'updateMenuItem'])->name('admin.appearance.menu.builder.update.menu.item')
                ->middleware('can:Manage Menu');
            Route::post('/menu-builder/delete', [MenuController::class, 'deleteMenu'])->name('admin.appearance.menu.builder.delete.menu')
                ->middleware('can:Delete Menu');

            Route::get('/site-setting', [SiteSettingController::class, 'siteSetting'])->name('admin.appearance.site.setting')
                ->middleware('can:Manage Site Settings');
            Route::post('/site-setting/update', [SiteSettingController::class, 'siteSettingUpdate'])->name('admin.appearance.site.setting.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/site-setting/footer', [SiteSettingController::class, 'footerSetting'])->name('admin.appearance.site.setting.footer')
                ->middleware('can:Manage Site Settings');
            Route::post('/site-setting/footer/update', [SiteSettingController::class, 'footerSettingUpdate'])->name('admin.appearance.site.setting.footer.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/site-setting/seo', [SiteSettingController::class, 'seoSetting'])->name('admin.appearance.site.setting.seo')
                ->middleware('can:Manage Site Settings');
            Route::post('/site-setting/seo/update', [SiteSettingController::class, 'seoSettingUpdate'])->name('admin.appearance.site.setting.seo.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/site-setting/colors', [SiteSettingController::class, 'colorSetting'])->name('admin.appearance.site.setting.colors')
                ->middleware('can:Manage Site Settings');
            Route::post('/site-setting/colors/update', [SiteSettingController::class, 'colorSettingUpdate'])->name('admin.appearance.site.setting.colors.update')
                ->middleware('can:Manage Site Settings');
            Route::get('/site-setting/custom-css', [SiteSettingController::class, 'customCssSetting'])->name('admin.appearance.site.setting.custom.css')
                ->middleware('can:Manage Site Settings');
            Route::post('/site-setting/custom-css/update', [SiteSettingController::class, 'customCssSettingUpdate'])->name('admin.appearance.site.setting.custom.css.update')
                ->middleware('can:Manage Site Settings');
        });

        Route::prefix('home-builder')->group(function () {
            Route::get('/', [HomePageBuilderController::class, 'index'])->name('admin.home.builder')
                ->middleware('can:Manage Home Builder');
            Route::post('/content', [HomePageBuilderController::class, 'updateContent'])->name('admin.home.builder.content')
                ->middleware('can:Manage Home Builder');
            Route::post('/order', [HomePageBuilderController::class, 'updateOrder'])->name('admin.home.builder.order')
                ->middleware('can:Manage Home Builder');
            Route::post('/toggle', [HomePageBuilderController::class, 'toggle'])->name('admin.home.builder.toggle')
                ->middleware('can:Manage Home Builder');
        });

        /**
         * CLASSIFIED ADS MODULES
         */
        Route::prefix('classified/ads')->group(function () {
            Route::prefix('categories')->group(function () {
                Route::get('/', [CategoryController::class, 'categories'])->name('classified.ads.categories.list')
                    ->middleware('can:Manage Ad Categories');
                Route::post('/store', [CategoryController::class, 'categoryStore'])->name('classified.ads.categories.store')
                    ->middleware('can:Create Ad Category');
                Route::post('/edit', [CategoryController::class, 'categoryEdit'])->name('classified.ads.categories.edit')
                    ->middleware('can:Edit Ad Category');
                Route::get('/{id}/edit', [CategoryController::class, 'categoryEditPage'])->name('classified.ads.categories.edit.page')
                    ->middleware('can:Edit Ad Category');
                Route::post('/update', [CategoryController::class, 'categoryUpdate'])->name('classified.ads.categories.update')
                    ->middleware('can:Edit Ad Category');
                Route::post('/delete', [CategoryController::class, 'categoryDelete'])->name('classified.ads.categories.delete')
                    ->middleware('can:Delete Ad Category');
                Route::get('/options', [CategoryController::class, 'CategoryOption'])->name('classified.ads.categories.options')
                    ->middleware('can:Manage Ad Categories');
            });

            Route::prefix('conditions')->group(function () {
                Route::get('/', [ConditionController::class, 'conditions'])->name('classified.ads.condition.list')
                    ->middleware('can:Manage Conditions');
                Route::post('/store', [ConditionController::class, 'storeCondition'])->name('classified.ads.condition.store')
                    ->middleware('can:Create Condition');
                Route::post('/edit', [ConditionController::class, 'editCondition'])->name('classified.ads.condition.edit')
                    ->middleware('can:Edit Condition');
                Route::get('/{id}/edit', [ConditionController::class, 'editConditionPage'])->name('classified.ads.condition.edit.page')
                    ->middleware('can:Edit Condition');
                Route::post('/update', [ConditionController::class, 'updateCondition'])->name('classified.ads.condition.update')
                    ->middleware('can:Edit Condition');
                Route::post('/delete', [ConditionController::class, 'deleteCondition'])->name('classified.ads.condition.delete')
                    ->middleware('can:Delete Condition');
            });

            Route::prefix('tags')->group(function () {
                Route::get('/', [TagController::class, 'tags'])->name('classified.ads.tag.list')
                    ->middleware('can:Manage Tags');
                Route::post('/store', [TagController::class, 'storeTag'])->name('classified.ads.tag.store')
                    ->middleware('can:Create Tag');
                Route::post('/update', [TagController::class, 'updateTag'])->name('classified.ads.tag.update')
                    ->middleware('can:Edit Tag');
                Route::post('/delete', [TagController::class, 'deleteTag'])->name('classified.ads.tag.delete')
                    ->middleware('can:Delete Tag');
                Route::get('/options', [TagController::class, 'tagOption'])->name('classified.ads.tag.options')
                    ->middleware('can:Manage Tags');
            });

            Route::prefix('custom-fields')->group(function () {
                Route::get('/', [CustomFieldController::class, 'customFields'])->name('classified.ads.custom.field.list')
                    ->middleware('can:Manage Custom Fields');
                Route::post('/store', [CustomFieldController::class, 'storeCustomField'])->name('classified.ads.custom.field.store')
                    ->middleware('can:Create Custom Field');
                Route::post('/edit', [CustomFieldController::class, 'editCustomField'])->name('classified.ads.custom.field.edit')
                    ->middleware('can:Edit Custom Field');
                Route::get('/{id}/edit', [CustomFieldController::class, 'editCustomFieldPage'])->name('classified.ads.custom.field.edit.page')
                    ->middleware('can:Edit Custom Field');
                Route::post('/update', [CustomFieldController::class, 'updateCustomField'])->name('classified.ads.custom.field.update')
                    ->middleware('can:Edit Custom Field');
                Route::post('/delete', [CustomFieldController::class, 'deleteCustomField'])->name('classified.ads.custom.field.delete')
                    ->middleware('can:Delete Custom Field');
                Route::post('/assign-category', [CustomFieldController::class, 'assignCategory'])->name('classified.ads.custom.field.assign.category')
                    ->middleware('can:Edit Custom Field');
                Route::get('/{id}/options', [CustomFieldController::class, 'customFieldOptions'])->name('classified.ads.custom.field.options')
                    ->middleware('can:Manage Custom Fields');
                Route::post('/options/store', [CustomFieldController::class, 'customFieldOptionStore'])->name('classified.ads.custom.field.options.store')
                    ->middleware('can:Create Custom Field');
                Route::post('/options/edit', [CustomFieldController::class, 'customFieldOptionEdit'])->name('classified.ads.custom.field.options.edit')
                    ->middleware('can:Edit Custom Field');
                Route::get('/options/{id}/edit', [CustomFieldController::class, 'editOptionPage'])->name('classified.ads.custom.field.options.edit.page')
                    ->middleware('can:Edit Custom Field');
                Route::post('/options/update', [CustomFieldController::class, 'customFieldOptionUpdate'])->name('classified.ads.custom.field.options.update')
                    ->middleware('can:Edit Custom Field');
                Route::post('/options/delete', [CustomFieldController::class, 'customFieldOptionDelete'])->name('classified.ads.custom.field.options.delete')
                    ->middleware('can:Delete Custom Field');
            });

            Route::prefix('report-reasons')->group(function () {
                Route::get('/', [ReportReasonController::class, 'index'])->name('classified.ads.report.reasons.list')
                    ->middleware('can:Manage Report Reasons');
                Route::post('/store', [ReportReasonController::class, 'store'])->name('classified.ads.report.reasons.store')
                    ->middleware('can:Create Report Reason');
                Route::get('/{id}/edit', [ReportReasonController::class, 'edit'])->name('classified.ads.report.reasons.edit')
                    ->middleware('can:Edit Report Reason');
                Route::post('/update', [ReportReasonController::class, 'update'])->name('classified.ads.report.reasons.update')
                    ->middleware('can:Edit Report Reason');
                Route::post('/delete', [ReportReasonController::class, 'delete'])->name('classified.ads.report.reasons.delete')
                    ->middleware('can:Delete Report Reason');
            });
        });

        /**
         * ADVERTISEMENTS
         */
        Route::prefix('advertisements')->group(function () {
            Route::get('/', [AdvertisementController::class, 'index'])->name('admin.advertisement.list')
                ->middleware('can:Manage Advertisements');
            Route::post('/store', [AdvertisementController::class, 'store'])->name('admin.advertisement.store')
                ->middleware('can:Create Advertisement');
            Route::post('/edit', [AdvertisementController::class, 'edit'])->name('admin.advertisement.edit')
                ->middleware('can:Edit Advertisement');
            Route::post('/update', [AdvertisementController::class, 'update'])->name('admin.advertisement.update')
                ->middleware('can:Edit Advertisement');
            Route::post('/delete', [AdvertisementController::class, 'delete'])->name('admin.advertisement.delete')
                ->middleware('can:Delete Advertisement');
            Route::get('/{id}/analytics', [AdvertisementController::class, 'analytics'])->name('admin.advertisement.analytics')
                ->middleware('can:Manage Advertisements');
        });

        /**
         * Registered placeholders for backend modules whose controllers are absent in this checkout.
         */
        Route::match(['GET', 'POST'], '/bank-payments', MissingModuleController::class)->name('admin.bank.payments');
        Route::post('/bank-payments/approve', MissingModuleController::class)->name('admin.bank.payments.approve');
        Route::post('/bank-payments/reject', MissingModuleController::class)->name('admin.bank.payments.reject');

        Route::match(['GET', 'POST'], '/payment-settings/update', MissingModuleController::class)->name('admin.payment.settings.update');

        Route::get('/contact-messages', MissingModuleController::class)->name('admin.contact.us.message.list');
        Route::post('/contact-messages/reply', MissingModuleController::class)->name('admin.contact.us.message.reply');
        Route::post('/contact-messages/delete', MissingModuleController::class)->name('admin.contact.us.message.delete');

        Route::get('/conversations', MissingModuleController::class)->name('admin.conversations.index');
        Route::get('/conversations/{uid}', MissingModuleController::class)->name('admin.conversations.show');

        Route::prefix('newsletter')->group(function () {
            Route::get('/campaigns', MissingModuleController::class)->name('admin.newsletter.campaigns');
            Route::get('/campaigns/create', MissingModuleController::class)->name('admin.newsletter.campaigns.create');
            Route::post('/campaigns/store', MissingModuleController::class)->name('admin.newsletter.campaigns.store');
            Route::get('/campaigns/{id}/edit', MissingModuleController::class)->name('admin.newsletter.campaigns.edit');
            Route::post('/campaigns/{id}/update', MissingModuleController::class)->name('admin.newsletter.campaigns.update');
            Route::post('/campaigns/delete', MissingModuleController::class)->name('admin.newsletter.campaigns.delete');
            Route::get('/campaigns/{id}/stats', MissingModuleController::class)->name('admin.newsletter.campaigns.stats');
            Route::post('/subscribers/delete', MissingModuleController::class)->name('admin.newsletter.subscribers.delete');
        });

        Route::prefix('classified')->group(function () {
            Route::get('/ads/{id}/edit', MissingModuleController::class)->name('classified.ads.edit');
            Route::post('/ads/update', MissingModuleController::class)->name('classified.ads.update');
            Route::get('/ads/get-states', MissingModuleController::class)->name('classified.ads.get.states');
            Route::get('/ads/get-cities', MissingModuleController::class)->name('classified.ads.get.cities');

            Route::get('/ads/reports', MissingModuleController::class)->name('classified.ads.reports.list');
            Route::post('/ads/reports/status', MissingModuleController::class)->name('classified.ads.reports.status');
            Route::post('/ads/reports/delete', MissingModuleController::class)->name('classified.ads.reports.delete');

            Route::get('/locations/countries', MissingModuleController::class)->name('classified.locations.country.list');
            Route::post('/locations/countries/store', MissingModuleController::class)->name('classified.locations.country.store');
            Route::post('/locations/countries/edit', MissingModuleController::class)->name('classified.locations.country.edit');
            Route::post('/locations/countries/update', MissingModuleController::class)->name('classified.locations.country.update');
            Route::post('/locations/countries/delete', MissingModuleController::class)->name('classified.locations.country.delete');

            Route::get('/locations/states', MissingModuleController::class)->name('classified.locations.state.list');
            Route::post('/locations/states/store', MissingModuleController::class)->name('classified.locations.state.store');
            Route::post('/locations/states/edit', MissingModuleController::class)->name('classified.locations.state.edit');
            Route::post('/locations/states/update', MissingModuleController::class)->name('classified.locations.state.update');
            Route::post('/locations/states/delete', MissingModuleController::class)->name('classified.locations.state.delete');

            Route::get('/locations/cities', MissingModuleController::class)->name('classified.locations.city.list');
            Route::post('/locations/cities/store', MissingModuleController::class)->name('classified.locations.city.store');
            Route::post('/locations/cities/edit', MissingModuleController::class)->name('classified.locations.city.edit');
            Route::post('/locations/cities/update', MissingModuleController::class)->name('classified.locations.city.update');
            Route::post('/locations/cities/delete', MissingModuleController::class)->name('classified.locations.city.delete');

            Route::get('/settings/general', MissingModuleController::class)->name('classified.settings.general');
            Route::get('/settings/member', MissingModuleController::class)->name('classified.settings.member');
            Route::get('/settings/currency', MissingModuleController::class)->name('classified.settings.currency');
            Route::get('/settings/ads', MissingModuleController::class)->name('classified.settings.ads');
            Route::get('/settings/map', MissingModuleController::class)->name('classified.settings.map');
            Route::post('/settings/update', MissingModuleController::class)->name('classified.settings.update');
            Route::post('/member-settings/update', MissingModuleController::class)->name('classified.member.settings.update');

            Route::get('/settings/safety-tips', MissingModuleController::class)->name('classified.settings.safety.tips.list');
            Route::post('/settings/safety-tips/store', MissingModuleController::class)->name('classified.settings.safety.tips.store');
            Route::get('/settings/safety-tips/{id}/edit', MissingModuleController::class)->name('classified.settings.safety.tips.edit');
            Route::post('/settings/safety-tips/update', MissingModuleController::class)->name('classified.settings.safety.tips.update');
            Route::post('/settings/safety-tips/delete', MissingModuleController::class)->name('classified.settings.safety.tips.delete');

            Route::get('/settings/quick-sell-tips', MissingModuleController::class)->name('classified.settings.quick.sell.tips.list');
            Route::post('/settings/quick-sell-tips/store', MissingModuleController::class)->name('classified.settings.quick.sell.tips.store');
            Route::get('/settings/quick-sell-tips/{id}/edit', MissingModuleController::class)->name('classified.settings.quick.sell.tips.edit');
            Route::post('/settings/quick-sell-tips/update', MissingModuleController::class)->name('classified.settings.quick.sell.tips.update');
            Route::post('/settings/quick-sell-tips/delete', MissingModuleController::class)->name('classified.settings.quick.sell.tips.delete');

            Route::get('/settings/share-options', MissingModuleController::class)->name('classified.settings.share.options.list');
            Route::post('/settings/share-options/status', MissingModuleController::class)->name('classified.settings.share.options.status.update');
        });

        /**
         * Media Management (shared picker — requires at least media view access)
         */
        Route::get('/media', [MediaController::class, 'mediaManager'])->name('admin.media.list')
            ->middleware('can:Manage Media');
        Route::post('/media/delete', [MediaController::class, 'deleteMedia'])->name('admin.media.delete')
            ->middleware('can:Manage Media');
        Route::post('/upload-media-file', [MediaController::class, 'uploadMediaFile'])->name('upload.media.file')
            ->middleware('can:Manage Media');
        Route::post('/media-items-list', [MediaController::class, 'mediaList'])->name('media.file.list')
            ->middleware('can:Manage Media');
        Route::post('/selected-media-details', [MediaController::class, 'selectedMediaDetails'])->name('media.selected.file.details')
            ->middleware('can:Manage Media');
    });
});
