<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\LocationController;
use App\Http\Controllers\Frontend\MemberAuthController;
use App\Http\Controllers\Frontend\MessageController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\SubscriptionController;
use App\Http\Controllers\Frontend\ResellerAuthController;
use App\Http\Controllers\Frontend\ResellerController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\StripeWebhookController;
use App\Http\Controllers\Frontend\SupportTicketController;
use App\Http\Controllers\Frontend\SetupGuideController;
use App\Http\Controllers\Frontend\HomepageController;

Route::get('/', [HomepageController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'contactPage'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'sendMessage'])->name('contact.send');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/track/open/{campaign}/{subscriber}', [NewsletterController::class, 'trackOpen'])->name('newsletter.track.open');

// Language Switcher (frontend public route)
Route::get('/language/switch/{code}', [LanguageController::class, 'setSessionLanguage'])->name('frontend.language.switch');
Route::get('/pricing-plans', [PageController::class, 'pricingPlans'])->name('pricing.plans');

// Static pages
Route::get('/page/{permalink}', [PageController::class, 'pagePreview'])->name('frontend.page.single.preview');

// Blog
Route::get('/blog', [BlogController::class, 'blogList'])->name('frontend.blog.list');
Route::get('/blog/{permalink}', [BlogController::class, 'blogDetails'])->name('frontend.new.details');
Route::post('/blog/{permalink}/comment', [BlogController::class, 'storeComment'])->name('frontend.blog.comment.store');


// Stripe webhook (no auth, no CSRF)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Payment link (public token-based URL)
Route::get('/pay/{token}', [SubscriptionController::class, 'paymentLinkRedirect'])->name('payment.link');

//Location Routes
Route::get('/state/list', [LocationController::class, 'stateListofCountry'])->name('location.country.states.options');
Route::get('/city/list', [LocationController::class, 'cityListofState'])->name('location.state.cities.options');

//Auth Routes
Route::group(['middleware' => ['guest']], function () {
    Route::get('/member/login', [MemberAuthController::class, 'memberLoginPage'])->name('member.login');
    Route::post('/member/login', [MemberAuthController::class, 'loginAttempt'])->name('member.login.attempt');
    Route::get('/member/register', [MemberAuthController::class, 'memberRegisterPage'])->name('member.register');
    Route::post('/member/register', [MemberAuthController::class, 'memberRegister'])->name('member.register.submit');

    Route::get('forgot-password', [MemberAuthController::class, 'forgotPasswordPage'])->name('member.forgot.password');
    Route::post('forgot-password', [MemberAuthController::class, 'forgotPassword'])->name('member.forgot.password.submit');
    Route::get('reset-password/{token}', [MemberAuthController::class, 'resetPasswordPage'])->name('member.reset.password');
    Route::post('reset-password', [MemberAuthController::class, 'resetPassword'])->name('member.reset.password.submit');

    // Social Login
    Route::get('/member/social/{provider}', [MemberAuthController::class, 'socialLogin'])->name('member.social.login');
    Route::get('/member/social/{provider}/callback', [MemberAuthController::class, 'socialCallback'])->name('member.social.callback');
});

Route::group(['middleware' => ['auth', 'customer']], function () {
    Route::get('/member/logout', [MemberAuthController::class, 'memberLogout'])->name('member.logout');
    Route::get('/member/dashboard', [MemberAuthController::class, 'memberDashboard'])->name('member.dashboard');

    // Messaging
    Route::get('/member/messages', [MessageController::class, 'index'])->name('member.messages.index');
    Route::get('/member/messages/{uid}', [MessageController::class, 'show'])->name('member.messages.show');
    Route::post('/member/messages/start', [MessageController::class, 'start'])->name('member.messages.start');
    Route::post('/member/messages/{uid}/send', [MessageController::class, 'sendMessage'])->name('member.messages.send');

    // Account
    Route::get('/member/account', [AccountController::class, 'accountPage'])->name('member.account');
    Route::put('/member/account/profile', [AccountController::class, 'updateProfile'])->name('member.account.update.profile');
    Route::put('/member/account/password', [AccountController::class, 'updatePassword'])->name('member.account.update.password');
    Route::post('/member/account/image', [AccountController::class, 'updateProfileImage'])->name('member.account.update.image');

    // Subscriptions
    Route::get('/member/subscriptions', [SubscriptionController::class, 'mySubscriptions'])->name('member.subscriptions');
    Route::post('/membership/buy', [SubscriptionController::class, 'buy'])->name('membership.buy');
    Route::get('/membership/buy/free', [SubscriptionController::class, 'buy'])->name('membership.buy.free');
    Route::get('/membership/confirm/{planId}', [SubscriptionController::class, 'confirm'])->name('subscription.confirm');
Route::post('/membership/stripe/initiate', [SubscriptionController::class, 'initiateStripePayment'])->name('membership.stripe.initiate');
    Route::get('/membership/stripe/success', [SubscriptionController::class, 'stripeSuccess'])->name('membership.stripe.success');

    // Support tickets
    Route::prefix('member/support')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('member.tickets.index');
        Route::get('/create', [SupportTicketController::class, 'create'])->name('member.tickets.create');
        Route::post('/store', [SupportTicketController::class, 'store'])->name('member.tickets.store');
        Route::get('/{ticketNumber}', [SupportTicketController::class, 'show'])->name('member.tickets.show');
        Route::post('/{ticketNumber}/reply', [SupportTicketController::class, 'reply'])->name('member.tickets.reply');
        Route::post('/{ticketNumber}/close', [SupportTicketController::class, 'close'])->name('member.tickets.close');
    });

    // Setup guide
    Route::get('/member/setup-guide', [SetupGuideController::class, 'index'])->name('member.setup.guide');

    // Download App
    Route::get('/member/download-app', [MemberAuthController::class, 'downloadApp'])->name('member.download.app');
});
// ── Customer Portal (alias routes for member system) ──────────────────────────
Route::get('/customer/login', [MemberAuthController::class, 'memberLoginPage'])->name('customer.login')->middleware('guest');
Route::get('/customer/register', [MemberAuthController::class, 'memberRegisterPage'])->name('customer.register')->middleware('guest');
Route::get('/customer/dashboard', [MemberAuthController::class, 'memberDashboard'])->name('customer.dashboard')->middleware(['auth', 'customer']);

// ── Reseller Portal ────────────────────────────────────────────────────────────
Route::prefix('reseller')->group(function () {

    // Guest-only auth
    Route::middleware('guest')->group(function () {
        Route::get('/login', [ResellerAuthController::class, 'loginPage'])->name('reseller.login');
        Route::post('/login', [ResellerAuthController::class, 'loginAttempt'])->name('reseller.login.attempt');
        Route::get('/register', [ResellerAuthController::class, 'registerPage'])->name('reseller.register');
        Route::post('/register', [ResellerAuthController::class, 'register'])->name('reseller.register.submit');
        Route::get('/forgot-password', [ResellerAuthController::class, 'forgotPasswordPage'])->name('reseller.forgot.password');
        Route::post('/forgot-password', [ResellerAuthController::class, 'forgotPassword'])->name('reseller.forgot.password.submit');
        Route::get('/reset-password/{token}', [ResellerAuthController::class, 'resetPasswordPage'])->name('reseller.reset.password');
        Route::post('/reset-password', [ResellerAuthController::class, 'resetPassword'])->name('reseller.reset.password.submit');
    });

    // Authenticated reseller routes
    Route::middleware(['auth', 'reseller'])->group(function () {
        Route::get('/logout', [ResellerAuthController::class, 'logout'])->name('reseller.logout');
        Route::get('/dashboard', [ResellerController::class, 'dashboard'])->name('reseller.dashboard');
        Route::get('/clients', [ResellerController::class, 'clients'])->name('reseller.clients');
        Route::post('/clients/add', [ResellerController::class, 'addClient'])->name('reseller.clients.add');
        Route::get('/account', [ResellerController::class, 'account'])->name('reseller.account');
        Route::put('/account/profile', [ResellerController::class, 'updateAccount'])->name('reseller.account.update');
        Route::put('/account/password', [ResellerController::class, 'updatePassword'])->name('reseller.account.password');
        Route::get('/credits', [ResellerController::class, 'credits'])->name('reseller.credits');
        Route::post('/credits/topup-request', [ResellerController::class, 'requestCreditTopup'])->name('reseller.credits.topup');
        Route::post('/credits/transfer', [ResellerController::class, 'transferCredits'])->name('reseller.credits.transfer');
        Route::get('/api-keys', [ResellerController::class, 'apiKeys'])->name('reseller.api.keys');
        Route::post('/api-keys/create', [ResellerController::class, 'createApiToken'])->name('reseller.api.keys.create');
        Route::post('/api-keys/revoke', [ResellerController::class, 'revokeApiToken'])->name('reseller.api.keys.revoke');
    });
});

require __DIR__ . '/admin.php';
