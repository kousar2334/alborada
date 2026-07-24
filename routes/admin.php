<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\MemberController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\UtilityController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\UserAuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\SiteSettingController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\MissingModuleController;
use App\Http\Controllers\Backend\PricingPlanController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Backend\HomePageBuilderController;
use App\Http\Controllers\Backend\PaymentSettingsController;
use App\Http\Controllers\Backend\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Backend\ResellerManagementController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ApiLogController;
use App\Http\Controllers\Backend\AppDownloaderCodeController;
use App\Http\Controllers\Backend\FeaturedContentController;
use App\Http\Controllers\Backend\MediaContentController;
use App\Http\Controllers\Backend\ChannelController;

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

        Route::get('/core/page', fn() => to_route('admin.page.list'))->name('core.page');
        Route::get('/core/email/smtp-configuration', fn() => to_route('admin.system.settings.smtp'))->name('core.email.smtp.configuration');

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
            Route::post('assign', [SubscriptionController::class, 'assign'])->name('admin.subscriptions.assign');
            Route::post('approve', [SubscriptionController::class, 'approve'])->name('admin.subscriptions.approve');
            Route::post('reject', [SubscriptionController::class, 'reject'])->name('admin.subscriptions.reject');
            Route::post('delete', [SubscriptionController::class, 'delete'])->name('admin.subscriptions.delete');
            Route::post('send-payment-link', [SubscriptionController::class, 'sendPaymentLink'])->name('admin.subscriptions.send.payment.link');
            Route::post('reprovision', [SubscriptionController::class, 'reprovision'])->name('admin.subscriptions.reprovision');
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

        Route::get('/payment-settings', [PaymentSettingsController::class, 'index'])->name('admin.payment.settings');
        Route::post('/payment-settings/update', [PaymentSettingsController::class, 'update'])->name('admin.payment.settings.update');

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

        /**
         * Support Tickets
         */
        Route::prefix('support/tickets')->group(function () {
            Route::get('/', [AdminSupportTicketController::class, 'index'])->name('admin.tickets.index');
            Route::get('/{id}', [AdminSupportTicketController::class, 'show'])->name('admin.tickets.show');
            Route::post('/{id}/reply', [AdminSupportTicketController::class, 'reply'])->name('admin.tickets.reply');
            Route::post('/assign', [AdminSupportTicketController::class, 'assign'])->name('admin.tickets.assign');
            Route::post('/status', [AdminSupportTicketController::class, 'updateStatus'])->name('admin.tickets.status');
        });

        /**
         * Reseller Management
         */
        Route::prefix('resellers')->group(function () {
            Route::get('/', [ResellerManagementController::class, 'index'])->name('admin.resellers.index');
            Route::post('/store', [ResellerManagementController::class, 'store'])->name('admin.resellers.store');
            Route::post('/top-up', [ResellerManagementController::class, 'topUpCredits'])->name('admin.resellers.top.up');
            Route::get('/{id}/logs', [ResellerManagementController::class, 'creditLogs'])->name('admin.resellers.credit.logs');
            Route::get('/{id}/edit', [ResellerManagementController::class, 'edit'])->name('admin.resellers.edit');
            Route::put('/{id}', [ResellerManagementController::class, 'update'])->name('admin.resellers.update');
            Route::post('/{id}/approve', [ResellerManagementController::class, 'approve'])->name('admin.resellers.approve');
            Route::post('/{id}/reject', [ResellerManagementController::class, 'reject'])->name('admin.resellers.reject');
            Route::post('/{id}/delete', [ResellerManagementController::class, 'destroy'])->name('admin.resellers.delete');
        });

        /**
         * Reports & Analytics
         */
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/revenue-chart', [ReportController::class, 'revenueChart'])->name('admin.reports.revenue.chart');
            Route::get('/subscribers-chart', [ReportController::class, 'activeSubscribersChart'])->name('admin.reports.subscribers.chart');
            Route::get('/expiring-soon', [ReportController::class, 'expiringSoon'])->name('admin.reports.expiring.soon');
            Route::get('/reseller-performance', [ReportController::class, 'resellerPerformance'])->name('admin.reports.reseller.performance');
            Route::get('/export', [ReportController::class, 'exportCsv'])->name('admin.reports.export');
        });

        /**
         * API Logs
         */
        Route::get('/api-logs', [ApiLogController::class, 'index'])->name('admin.api.logs');

        /**
         * IPTV Settings
         */
        Route::get('/system/settings/iptv', [SettingController::class, 'iptvSettings'])->name('admin.system.settings.iptv');
        Route::post('/system/settings/iptv/update', [SettingController::class, 'iptvSettingsUpdate'])->name('admin.system.settings.iptv.update');
        Route::post('/system/settings/iptv/sync-packages', [SettingController::class, 'syncIptvPackages'])->name('admin.system.settings.iptv.sync-packages');

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
        Route::post('/media/update-details', [MediaController::class, 'updateMediaDetails'])->name('admin.media.update.details')
            ->middleware('can:Manage Media');

        // ── App Downloader Codes ──────────────────────────────────────────────
        Route::resource('downloader-codes', AppDownloaderCodeController::class)
            ->except(['show'])
            ->names([
                'index'   => 'admin.downloader-codes.index',
                'store'   => 'admin.downloader-codes.store',
                'edit'    => 'admin.downloader-codes.edit',
                'update'  => 'admin.downloader-codes.update',
                'destroy' => 'admin.downloader-codes.destroy',
            ]);
        Route::post('/downloader-codes/{downloaderCode}/toggle', [AppDownloaderCodeController::class, 'toggleStatus'])
            ->name('admin.downloader-codes.toggle');

        // ── Featured Content ──────────────────────────────────────────────────
        Route::resource('featured-content', FeaturedContentController::class)
            ->except(['show'])
            ->names([
                'index'   => 'admin.featured-content.index',
                'create'  => 'admin.featured-content.create',
                'store'   => 'admin.featured-content.store',
                'edit'    => 'admin.featured-content.edit',
                'update'  => 'admin.featured-content.update',
                'destroy' => 'admin.featured-content.destroy',
            ]);

        // ── Movies & TV Shows ─────────────────────────────────────────────────
        Route::resource('media-content', MediaContentController::class)
            ->except(['show'])
            ->names([
                'index'   => 'admin.media-content.index',
                'create'  => 'admin.media-content.create',
                'store'   => 'admin.media-content.store',
                'edit'    => 'admin.media-content.edit',
                'update'  => 'admin.media-content.update',
                'destroy' => 'admin.media-content.destroy',
            ]);

        // ── Chat Widget Settings ──────────────────────────────────────────────
        Route::get('/settings/chat-widget', [SettingController::class, 'chatWidget'])->name('admin.settings.chat-widget');
        Route::post('/settings/chat-widget/update', [SettingController::class, 'updateChatWidget'])->name('admin.settings.chat-widget.update');

        // ── Channels ──────────────────────────────────────────────────────────
        Route::resource('channels', ChannelController::class)
            ->except(['show'])
            ->names([
                'index'   => 'admin.channels.index',
                'create'  => 'admin.channels.create',
                'store'   => 'admin.channels.store',
                'edit'    => 'admin.channels.edit',
                'update'  => 'admin.channels.update',
                'destroy' => 'admin.channels.destroy',
            ]);
        Route::post('/channels/{channel}/toggle', [ChannelController::class, 'toggleStatus'])
            ->name('admin.channels.toggle');
    });
});
