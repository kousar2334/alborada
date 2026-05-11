<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ResellerManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('type', config('settings.user_type.reseller', 3))
            ->withCount('resellerClients')
            ->latest();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        $resellers = $query->paginate(20)->withQueryString();

        return view('backend.modules.resellers.index', compact('resellers'));
    }

    public function topUpCredits(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required|integer|exists:users,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $reseller = User::findOrFail($request->reseller_id);
        $reseller->addCredits((float) $request->amount, $request->description ?: 'Admin top-up');

        toastNotification('success', __tr('Credits added to ') . $reseller->name);
        return back();
    }

    public function creditLogs(int $id)
    {
        $reseller = User::findOrFail($id);
        $logs = $reseller->creditLogs()->with('client')->latest()->paginate(30);

        return view('backend.modules.resellers.credit-logs', compact('reseller', 'logs'));
    }
}
