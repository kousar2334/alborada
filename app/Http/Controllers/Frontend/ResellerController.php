<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResellerController extends Controller
{
    public function dashboard()
    {
        $reseller = Auth::user();

        $totalClients  = User::where('reseller_id', $reseller->id)->count();
        $activeClients = User::where('reseller_id', $reseller->id)
            ->where('status', config('settings.general_status.active'))
            ->count();

        $clientIds = User::where('reseller_id', $reseller->id)->pluck('id');

        $activeSubscriptions = UserSubscription::whereIn('user_id', $clientIds)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();

        $totalRevenue = UserSubscription::whereIn('user_id', $clientIds)
            ->where('status', 'active')
            ->sum('amount');

        $recentClients = User::where('reseller_id', $reseller->id)
            ->with(['subscriptions' => function ($q) {
                $q->where('status', 'active')->where('expires_at', '>', now())->latest()->limit(1);
            }])
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.pages.reseller.dashboard.index', compact(
            'reseller',
            'totalClients',
            'activeClients',
            'activeSubscriptions',
            'totalRevenue',
            'recentClients'
        ));
    }

    public function clients(Request $request)
    {
        $reseller = Auth::user();

        $clients = User::where('reseller_id', $reseller->id)
            ->with(['subscriptions' => function ($q) {
                $q->where('status', 'active')->where('expires_at', '>', now())->latest()->limit(1);
            }])
            ->latest()
            ->paginate(15);

        return view('frontend.pages.reseller.clients.index', compact('clients'));
    }

    public function addClient(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:8',
        ]);

        try {
            User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'phone'       => $request->phone,
                'type'        => config('settings.user_type.customer'),
                'status'      => config('settings.general_status.active'),
                'reseller_id' => Auth::id(),
                'password'    => Hash::make($request->password),
            ]);

            toastNotification('success', 'Client added successfully.', 'Success');
            return redirect()->route('reseller.clients');
        } catch (\Exception $e) {
            toastNotification('error', 'Failed to add client.', 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function account()
    {
        return view('frontend.pages.reseller.account', ['user' => Auth::user()]);
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
        ]);

        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->phone        = $request->phone;
        $user->company_name = $request->company_name;
        $user->save();

        toastNotification('success', 'Profile updated successfully.', 'Success');
        return redirect()->back();
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        toastNotification('success', 'Password updated successfully.', 'Success');
        return redirect()->back();
    }

    public function credits()
    {
        $reseller = Auth::user();
        $logs = $reseller->creditLogs()->with('client')->latest()->paginate(20);

        return view('frontend.pages.reseller.credits', compact('reseller', 'logs'));
    }

    public function apiKeys()
    {
        $reseller = Auth::user();
        $tokens   = $reseller->tokens()->latest()->get();

        return view('frontend.pages.reseller.api-keys', compact('reseller', 'tokens'));
    }

    public function createApiToken(Request $request)
    {
        $request->validate(['token_name' => 'required|string|max:100']);

        $token = Auth::user()->createToken($request->token_name)->plainTextToken;

        return redirect()->route('reseller.api.keys')
            ->with('new_token', $token)
            ->with('success', 'API token created. Copy it now — it will not be shown again.');
    }

    public function revokeApiToken(Request $request)
    {
        $request->validate(['token_id' => 'required|integer']);

        Auth::user()->tokens()->where('id', $request->token_id)->delete();

        return back()->with('success', 'API token revoked.');
    }
}
