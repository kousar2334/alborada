<?php

namespace App\Http\Controllers\Frontend;

use App\Models\AppDownloaderCode;
use App\Models\FeaturedContent;
use App\Models\Invoice;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemberLoginRequest;
use App\Http\Requests\MemberRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MemberAuthController extends Controller
{
    //
    public function memberLoginPage()
    {
        return view('frontend.auth.login');
    }


    public function memberRegisterPage()
    {
        return view('frontend.auth.register');
    }

    public function memberRegister(MemberRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->type = config('settings.user_type.member');
            $user->status = config('settings.general_status.active');
            $user->password = Hash::make($request['password']);
            $user->save();
            toastNotification('success', 'Registration Completed', 'Success');
            return to_route('member.login');
        } catch (\Exception $e) {
            toastNotification('error', 'Registration failed', 'Error');
            return redirect()->back();
        }
    }


    public function loginAttempt(MemberLoginRequest $request): RedirectResponse
    {
        $user = User::where('type', config('settings.user_type.member'))
            ->where(function ($q) use ($request) {
                $q->where('email', $request->username);
            })
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login_error' => 'No account found with this email'
            ]);
        }

        // Check if user is active
        if ($user->status != config('settings.general_status.active')) {
            throw ValidationException::withMessages([
                'login_error' => 'Your account is not active. Please contact administration'
            ]);
        }

        // Verify password manually
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login_error' => 'Invalid password'
            ]);
        }

        // Login the user
        Auth::login($user);
        $request->session()->regenerate();

        toastNotification('success', 'Login Successfully', 'Success');
        return redirect()->route('member.dashboard');
    }


    public function memberLogout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        toastNotification('success', 'Logout Successfully', 'Success');
        return to_route('member.login');
    }

    public function forgotPasswordPage()
    {
        return view('frontend.auth.forgot-password');
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(['email' => $request->email]);

        if ($status === Password::RESET_LINK_SENT) {
            toastNotification('success', __('passwords.sent'), 'Success');
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordPage(Request $request, string $token)
    {
        return view('frontend.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                    ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            toastNotification('success', 'Password reset successfully.', 'Success');
            return to_route('member.login');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function socialLogin(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);
        return Socialite::driver($provider)->redirect();
    }

    public function socialCallback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            toastNotification('error', 'Social login failed. Please try again.', 'Error');
            return to_route('member.login');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'             => $socialUser->getEmail(),
                'password'          => Hash::make(str()->random(24)),
                'type'              => config('settings.user_type.member'),
                'status'            => config('settings.general_status.active'),
                'social_provider'   => $provider,
                'social_id'         => $socialUser->getId(),
            ]);
        }

        if ($user->status != config('settings.general_status.active')) {
            toastNotification('error', 'Your account is not active. Please contact administration.', 'Error');
            return to_route('member.login');
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        toastNotification('success', 'Login Successfully', 'Success');
        return to_route('member.dashboard');
    }

    public function memberDashboard(Request $request)
    {
        $userId = Auth::id();

        $activeSubscription = UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->with('plan')
            ->latest()
            ->first();

        $daysRemaining = $activeSubscription && $activeSubscription->expires_at
            ? max(0, (int) now()->diffInDays($activeSubscription->expires_at, false))
            : 0;



        $openTickets = SupportTicket::where('user_id', $userId)
            ->whereIn('status', ['NEW', 'IN_PROGRESS', 'RE_OPEN'])
            ->count();

        $recentInvoices = Invoice::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $downloaderCodes = AppDownloaderCode::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $featuredContent = FeaturedContent::where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $xtreamBaseUrl = rtrim(get_setting('xtream_base_url', ''), '/');

        return view('frontend.pages.member.dashboard.index', compact(
            'activeSubscription',
            'daysRemaining',
            'openTickets',
            'recentInvoices',
            'downloaderCodes',
            'featuredContent',
            'xtreamBaseUrl'
        ));
    }

    public function downloadApp()
    {
        $downloaderCodes = AppDownloaderCode::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('device_type');

        return view('frontend.pages.member.download-app', compact('downloaderCodes'));
    }
}
