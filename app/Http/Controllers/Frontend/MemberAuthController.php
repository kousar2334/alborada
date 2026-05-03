<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Ad;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\SavedAd;
use App\Models\User;
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
            $user->phone = $request['phone'];
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
        // Find user by email or phone
        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->username)
                ->orWhere('phone', $request->username);
        })
            ->where('type', config('settings.user_type.member'))
            ->first();

        // Check if user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'login_error' => 'No account found with this email/phone'
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
        $activeStatus = config('settings.general_status.active');

        $totalListings  = Ad::where('user_id', $userId)->count();
        $activeListings = Ad::where('user_id', $userId)->where('status', $activeStatus)->count();
        $totalFavourites = SavedAd::where('user_id', $userId)->count();

        $totalMessages = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->count();

        $unreadMessages = ChatMessage::whereHas('chat', function ($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
        })->where('sender_id', '!=', $userId)->where('is_read', false)->count();

        $recentListings = Ad::where('user_id', $userId)
            ->with(['categoryInfo', 'cityInfo'])
            ->latest()
            ->take(5)
            ->get();

        return view('frontend.pages.member.dashboard.index', compact(
            'totalListings',
            'activeListings',
            'totalFavourites',
            'totalMessages',
            'unreadMessages',
            'recentListings'
        ));
    }
}
