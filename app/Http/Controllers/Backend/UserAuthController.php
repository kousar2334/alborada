<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * 
     * Will redirect admin login page
     */
    public function login(): View
    {
        return view('backend.auth.login');
    }

    /**
     * Will attempt admin login
     * 
     * @param UserLoginRequest $request
     */
    public function loginAttempt(UserLoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request['email'])
            ->where('type', config('settings.user_type.admin'))
            ->first();

        if ($user == null) {
            throw ValidationException::withMessages(
                [
                    'login_error' => 'No account found associate this email'
                ]
            );
        }

        if ($user != null && $user->status != config('settings.general_status.active')) {
            throw ValidationException::withMessages(
                [
                    'login_error' => 'Your account is not active. Please contact with administration'
                ]
            );
        }


        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        throw ValidationException::withMessages(
            [
                'login_error' => 'Login Credentials Does not Match'
            ]
        );
    }
    /**
     * Will logout admin
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('admin.auth.login');
    }
    /**
     * Will redirect user profile page
     * 
     * 
     */
    public function profile(): View
    {
        return view('backend.auth.profile');
    }
    /**
     * Will update profile
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function profileUpdate(Request $request): JsonResponse
    {
        $request->validate(
            [
                'name' => 'required|max:150',
                'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            ]
        );
        try {
            DB::beginTransaction();
            $user = User::findOrFail(auth()->user()->id);
            $user->name = x_clean($request['name']);
            $user->email = x_clean($request['email']);
            $user->image = x_clean($request['image']);
            $user->save();
            DB::commit();
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Profile update failed'
                ]
            );
        } catch (\Error $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Profile update failed'
                ]
            );
        }
    }
    /**
     * Will update user password
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function passwordUpdate(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required|current_password',
                'password' => 'nullable|min:6|confirmed',
            ]
        );
        try {

            DB::beginTransaction();
            $user = User::findOrFail(auth()->user()->id);
            $user->password = Hash::make($request['password']);
            $user->save();
            DB::commit();
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Password update failed',
                ]
            );
        } catch (\Error $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Password update failed',
                ]
            );
        }
    }
}
