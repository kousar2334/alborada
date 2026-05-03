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
        $section = $request->input('section');

        if ($section === 'ssl') {
            $request->validate([
                'sslcommerz_store_id'       => 'nullable|string|max:200',
                'sslcommerz_store_password' => 'nullable|string|max:200',
                'sslcommerz_currency'       => 'nullable|string|max:10',
            ]);

            set_setting('ssl_enabled', $request->has('ssl_enabled') ? 1 : 0);
            set_setting('sslcommerz_store_id', $request->input('sslcommerz_store_id', ''));
            set_setting('sslcommerz_store_password', $request->input('sslcommerz_store_password', ''));
            set_setting('sslcommerz_sandbox', $request->has('sslcommerz_sandbox') ? 1 : 0);
            set_setting('sslcommerz_currency', $request->input('sslcommerz_currency', 'BDT'));

            toastNotification('success', 'SSLCommerz settings saved successfully', 'Success');
        } else {
            $request->validate([
                'bank_name'            => 'nullable|string|max:200',
                'bank_account_name'    => 'nullable|string|max:200',
                'bank_account_number'  => 'nullable|string|max:100',
                'bank_routing_number'  => 'nullable|string|max:100',
                'bank_branch'          => 'nullable|string|max:200',
                'bank_instructions'    => 'nullable|string|max:1000',
            ]);

            set_setting('bank_transfer_enabled', $request->has('bank_transfer_enabled') ? 1 : 0);
            set_setting('bank_name', $request->input('bank_name', ''));
            set_setting('bank_account_name', $request->input('bank_account_name', ''));
            set_setting('bank_account_number', $request->input('bank_account_number', ''));
            set_setting('bank_routing_number', $request->input('bank_routing_number', ''));
            set_setting('bank_branch', $request->input('bank_branch', ''));
            set_setting('bank_instructions', $request->input('bank_instructions', ''));

            toastNotification('success', 'Bank transfer settings saved successfully', 'Success');
        }

        return redirect()->back();
    }
}
