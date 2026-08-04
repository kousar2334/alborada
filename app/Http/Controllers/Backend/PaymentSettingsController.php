<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        return view('backend.modules.payment-settings.index');
    }

    /**
     * Each card on the settings page posts its own `section`, so saving Stripe
     * must not reset the bank-transfer fields (and vice versa).
     */
    public function update(Request $request)
    {
        $section = $request->input('section') === 'bank_transfer' ? 'bank_transfer' : 'stripe';

        $settings = $section === 'bank_transfer'
            ? $this->bankTransferSettings($request)
            : $this->stripeSettings($request);

        foreach ($settings as $key => $value) {
            set_setting($key, $value);
        }

        toastNotification('success', __tr('Payment settings updated successfully'));
        return back();
    }

    /**
     * Verify the keys stored for the active mode by calling Stripe.
     */
    public function testStripeConnection()
    {
        if (StripeService::secretKey() === '') {
            return response()->json([
                'ok'      => false,
                'message' => __tr('Enter and save the Secret Key for ') . StripeService::mode() . __tr(' mode first.'),
            ]);
        }

        try {
            $result = (new StripeService())->testConnection();
        } catch (\Throwable $e) {
            $result = ['ok' => false, 'message' => $e->getMessage()];
        }

        return response()->json($result + ['mode' => StripeService::mode()]);
    }

    private function stripeSettings(Request $request): array
    {
        // Prefixes are enforced so a live key can never end up in the sandbox slot
        // (which would charge real cards while the dashboard reads "Sandbox").
        $request->validate([
            'stripe_enabled'             => 'nullable|in:0,1',
            'stripe_mode'                => 'nullable|in:test,live',
            'stripe_test_public_key'     => 'nullable|string|max:255|starts_with:pk_test_',
            'stripe_test_secret_key'     => 'nullable|string|max:255|starts_with:sk_test_,rk_test_',
            'stripe_test_webhook_secret' => 'nullable|string|max:255|starts_with:whsec_',
            'stripe_live_public_key'     => 'nullable|string|max:255|starts_with:pk_live_',
            'stripe_live_secret_key'     => 'nullable|string|max:255|starts_with:sk_live_,rk_live_',
            'stripe_live_webhook_secret' => 'nullable|string|max:255|starts_with:whsec_',
            'stripe_currency'            => 'nullable|string|max:10|alpha',
        ], [
            'stripe_test_public_key.starts_with' => __tr('The sandbox Publishable Key must start with pk_test_.'),
            'stripe_test_secret_key.starts_with' => __tr('The sandbox Secret Key must start with sk_test_ (or rk_test_).'),
            'stripe_live_public_key.starts_with' => __tr('The live Publishable Key must start with pk_live_.'),
            'stripe_live_secret_key.starts_with' => __tr('The live Secret Key must start with sk_live_ (or rk_live_).'),
            'stripe_test_webhook_secret.starts_with' => __tr('Webhook signing secrets start with whsec_.'),
            'stripe_live_webhook_secret.starts_with' => __tr('Webhook signing secrets start with whsec_.'),
        ]);

        return [
            'stripe_enabled'             => $request->input('stripe_enabled', 0),
            'stripe_mode'                => $request->input('stripe_mode', StripeService::MODE_TEST),
            'stripe_test_public_key'     => trim((string) $request->input('stripe_test_public_key', '')),
            'stripe_test_secret_key'     => trim((string) $request->input('stripe_test_secret_key', '')),
            'stripe_test_webhook_secret' => trim((string) $request->input('stripe_test_webhook_secret', '')),
            'stripe_live_public_key'     => trim((string) $request->input('stripe_live_public_key', '')),
            'stripe_live_secret_key'     => trim((string) $request->input('stripe_live_secret_key', '')),
            'stripe_live_webhook_secret' => trim((string) $request->input('stripe_live_webhook_secret', '')),
            'stripe_currency'            => strtolower(trim((string) $request->input('stripe_currency', 'usd'))) ?: 'usd',
        ];
    }

    private function bankTransferSettings(Request $request): array
    {
        $request->validate([
            'bank_transfer_enabled'      => 'nullable|in:0,1',
            'bank_transfer_instructions' => 'nullable|string|max:5000',
        ]);

        return [
            'bank_transfer_enabled'      => $request->input('bank_transfer_enabled', 0),
            'bank_transfer_instructions' => $request->input('bank_transfer_instructions', ''),
        ];
    }
}
