<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AdController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Backend\AdvertisementController;
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

Route::get('/', function () {
    return view('frontend.pages.home-iptv');
})->name('home');
Route::get('/contact', [ContactController::class, 'contactPage'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'sendMessage'])->name('contact.send');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/track/open/{campaign}/{subscriber}', [NewsletterController::class, 'trackOpen'])->name('newsletter.track.open');

// Advertisement tracking (public, lightweight)
Route::post('/ad/impression', [AdvertisementController::class, 'trackImpression'])->name('ad.track.impression');
Route::post('/ad/click', [AdvertisementController::class, 'trackClick'])->name('ad.track.click');

// Language Switcher (frontend public route)
Route::get('/language/switch/{code}', [LanguageController::class, 'setSessionLanguage'])->name('frontend.language.switch');
Route::get('/pricing-plans', [PageController::class, 'pricingPlans'])->name('pricing.plans');

// Static pages
Route::get('/page/{permalink}', [PageController::class, 'pagePreview'])->name('frontend.page.single.preview');

// Blog
Route::get('/blog', [BlogController::class, 'blogList'])->name('frontend.blog.list');
Route::get('/blog/{permalink}', [BlogController::class, 'blogDetails'])->name('frontend.new.details');
Route::post('/blog/{permalink}/comment', [BlogController::class, 'storeComment'])->name('frontend.blog.comment.store');


// SSLCommerz callbacks (no auth middleware — called by gateway)
Route::post('/membership/ssl-success', [SubscriptionController::class, 'sslSuccess'])->name('subscription.ssl.success');
Route::post('/membership/ssl-fail', [SubscriptionController::class, 'sslFail'])->name('subscription.ssl.fail');
Route::post('/membership/ssl-cancel', [SubscriptionController::class, 'sslCancel'])->name('subscription.ssl.cancel');
Route::post('/membership/ssl-ipn', [SubscriptionController::class, 'sslIpn'])->name('subscription.ssl.ipn');

//Listing Routes
Route::get('/post/ad', [AdController::class, 'addPostPage'])->name('ad.post.page');
Route::post('/post/ad', [AdController::class, 'storeAd'])->name('ad.store');
Route::get('/ad/subcategories', [AdController::class, 'getSubcategories'])->name('ad.subcategories');
Route::get('/ad/custom-fields', [AdController::class, 'getCustomFields'])->name('ad.custom.fields');
Route::get('/ad/countries', [AdController::class, 'getCountries'])->name('ad.countries');
Route::get('/ad/states', [AdController::class, 'getStates'])->name('ad.states');
Route::get('/ad/cities', [AdController::class, 'getCities'])->name('ad.cities');
Route::get('ads/{category_slug?}', [AdController::class, 'adListingPage'])->name('ad.listing.page');
Route::get('/ads/details/{slug}', [AdController::class, 'adDetailsPage'])->name('ad.details.page');

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

Route::group(['middleware' => ['auth']], function () {
    Route::get('/member/logout', [MemberAuthController::class, 'memberLogout'])->name('member.logout');
    Route::get('/member/dashboard', [MemberAuthController::class, 'memberDashboard'])->name('member.dashboard');
    Route::get('/member/my-listings', [AdController::class, 'myListings'])->name('member.my.listings');
    Route::get('/member/ad/edit/{uid}', [AdController::class, 'editAd'])->name('member.ad.edit');
    Route::post('/member/ad/update/{uid}', [AdController::class, 'updateAd'])->name('member.ad.update');

    // Favorites
    Route::post('/ad/favorite/toggle', [AdController::class, 'toggleFavourite'])->name('ad.favorite.toggle');
    Route::get('/member/favourites', [AdController::class, 'myFavourites'])->name('member.favourites');

    // Reports
    Route::post('/ad/report', [AdController::class, 'reportAd'])->name('ad.report');

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
    Route::get('/membership/confirm/{planId}', [SubscriptionController::class, 'confirm'])->name('subscription.confirm');
    Route::post('/membership/bank-payment', [SubscriptionController::class, 'bankPayment'])->name('membership.bank.payment');
    Route::post('/membership/initiate-payment', [SubscriptionController::class, 'initiatePayment'])->name('membership.initiate.payment');
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
    });
});

require __DIR__ . '/admin.php';
