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

        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        $resellers = $query->paginate(20)->withQueryString();
        $pendingCount = User::where('type', config('settings.user_type.reseller', 3))->where('status', 0)->count();

        return view('backend.modules.resellers.index', compact('resellers', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email',
            'password'          => 'required|confirmed|min:6|max:250',
            'company_name'      => 'nullable|string|max:255',
            'markup_percentage' => 'nullable|numeric|min:0|max:100',
            'credits'           => 'nullable|numeric|min:0',
            'status'            => 'required|in:0,1',
        ]);

        $reseller = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => \Illuminate\Support\Facades\Hash::make($request->password),
            'company_name'      => $request->company_name,
            'markup_percentage' => $request->markup_percentage ?? 0,
            'type'              => config('settings.user_type.reseller', 3),
            'status'            => $request->status,
        ]);

        if ((float) $request->credits > 0) {
            $reseller->addCredits((float) $request->credits, 'Initial credits on account creation');
        }

        toastNotification('success', __tr('Reseller created successfully'));
        return redirect()->route('admin.resellers.index');
    }

    public function edit(int $id)
    {
        $reseller = User::where('type', config('settings.user_type.reseller', 3))->findOrFail($id);
        return view('backend.modules.resellers.edit', compact('reseller'));
    }

    public function update(Request $request, int $id)
    {
        $reseller = User::where('type', config('settings.user_type.reseller', 3))->findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $reseller->id,
            'company_name'     => 'nullable|string|max:255',
            'markup_percentage' => 'required|numeric|min:0|max:100',
            'status'           => 'required|in:0,1',
        ]);

        $reseller->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'company_name'      => $request->company_name,
            'markup_percentage' => $request->markup_percentage,
            'status'            => $request->status,
        ]);

        toastNotification('success', __tr('Reseller updated successfully'));
        return redirect()->route('admin.resellers.index');
    }

    public function approve(int $id)
    {
        $reseller = User::where('type', config('settings.user_type.reseller', 3))->findOrFail($id);
        $reseller->update(['status' => 1]);

        toastNotification('success', __tr('Reseller approved: ') . $reseller->name);
        return back();
    }

    public function reject(int $id)
    {
        $reseller = User::where('type', config('settings.user_type.reseller', 3))->findOrFail($id);
        $reseller->update(['status' => 0]);

        toastNotification('warning', __tr('Reseller rejected: ') . $reseller->name);
        return back();
    }

    public function destroy(int $id)
    {
        $reseller = User::where('type', config('settings.user_type.reseller', 3))->findOrFail($id);

        // DB constraints handle the related data: clients keep their accounts
        // (users.reseller_id is set NULL on delete) and the reseller's credit
        // logs are removed (cascade).
        $name = $reseller->name;
        $reseller->delete();

        toastNotification('success', __tr('Reseller deleted: ') . $name);
        return redirect()->route('admin.resellers.index');
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
