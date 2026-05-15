<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class ResellerAuthController extends Controller
{
    public function loginPage()
    {
        return view('frontend.auth.reseller.login');
    }

    public function loginAttempt(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('type', config('settings.user_type.reseller'))->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login_error' => 'No reseller account found with this email.',
            ]);
        }

        if ($user->status != config('settings.general_status.active')) {
            throw ValidationException::withMessages([
                'login_error' => 'Your account is pending approval. Please contact support.',
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login_error' => 'Invalid password.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        toastNotification('success', 'Welcome back, ' . $user->name . '!', 'Success');
        return redirect()->route('reseller.dashboard');
    }

    public function registerPage()
    {
        return view('frontend.auth.reseller.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'company_name' => 'required|string|max:255',
            'password'     => 'required|min:8|confirmed',
        ]);

        try {
            User::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'company_name' => $request->company_name,
                'type'         => config('settings.user_type.reseller'),
                'status'       => config('settings.general_status.in_active'),
                'password'     => Hash::make($request->password),
            ]);

            toastNotification('success', 'Registration submitted! Your account is pending admin approval.', 'Success');
            return redirect()->route('reseller.login');
        } catch (\Exception $e) {
            toastNotification('error', 'Registration failed. Please try again.', 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        toastNotification('success', 'Logged out successfully.', 'Success');
        return redirect()->route('reseller.login');
    }

    public function forgotPasswordPage()
    {
        return view('frontend.auth.reseller.forgot-password');
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(['email' => $request->email]);

        if ($status === Password::RESET_LINK_SENT) {
            toastNotification('success', __('passwords.sent'), 'Success');
            return back();
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordPage(Request $request, string $token)
    {
        return view('frontend.auth.reseller.reset-password', [
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
            return redirect()->route('reseller.login');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
