<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        return view('backend.modules.payment-settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'stripe_enabled'    => 'nullable|in:0,1',
            'stripe_public_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
            'stripe_currency'   => 'nullable|string|max:10',
            'bank_transfer_enabled'      => 'nullable|in:0,1',
            'bank_transfer_instructions' => 'nullable|string|max:5000',
        ]);

        $settings = [
            'stripe_enabled'        => $request->input('stripe_enabled', 0),
            'stripe_public_key'     => $request->input('stripe_public_key', ''),
            'stripe_secret_key'     => $request->input('stripe_secret_key', ''),
            'stripe_webhook_secret' => $request->input('stripe_webhook_secret', ''),
            'stripe_currency'       => $request->input('stripe_currency', 'usd'),
            'bank_transfer_enabled'      => $request->input('bank_transfer_enabled', 0),
            'bank_transfer_instructions' => $request->input('bank_transfer_instructions', ''),
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        toastNotification('success', __tr('Payment settings updated successfully'));
        return back();
    }
}
