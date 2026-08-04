@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Payment Settings'))
@section('settings-description', __tr('Configure your payment gateway and Stripe API keys.'))

@php
    use App\Services\StripeService;

    $stripeEnabled = get_setting('stripe_enabled', 0);
    $stripeMode    = StripeService::mode();
    $isTestMode    = $stripeMode === StripeService::MODE_TEST;
    $stripeReady   = StripeService::isReady();
@endphp

@section('page-style')
    @parent
    <style>
        .ps-hero {
            background: linear-gradient(135deg, #635bff 0%, #0ea5e9 100%);
            border-radius: 10px;
            padding: 24px 28px;
            color: #fff;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .ps-hero-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, .18);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            flex-shrink: 0;
        }

        .ps-hero-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 3px;
        }

        .ps-hero-sub {
            font-size: .875rem;
            opacity: .85;
            margin: 0;
        }

        .ps-status-badge {
            margin-left: auto;
            flex-shrink: 0;
        }

        .ps-status-badge .badge {
            font-size: .82rem;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .ps-guide-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            position: sticky;
            top: 24px;
        }

        .ps-guide-header {
            background: linear-gradient(135deg, #635bff 0%, #0ea5e9 100%);
            padding: 14px 18px;
            color: #fff;
        }

        .ps-guide-header-title {
            font-size: .88rem;
            font-weight: 700;
            margin: 0 0 2px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .ps-guide-header-sub {
            font-size: .76rem;
            opacity: .85;
            margin: 0;
        }

        .ps-steps {
            padding: 12px 18px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .ps-step {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .ps-step:last-child {
            border-bottom: none;
        }

        .ps-step-num {
            width: 28px;
            height: 28px;
            background: #635bff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .88rem;
            flex-shrink: 0;
        }

        .ps-step-title {
            font-weight: 700;
            font-size: .875rem;
            margin-bottom: 4px;
            color: #111827;
        }

        .ps-step-body {
            font-size: .8rem;
            color: #6b7280;
            line-height: 1.5;
        }

        .ps-step-body code {
            background: #f3f4f6;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: .78rem;
            color: #374151;
        }

        .ps-step-body a {
            color: #635bff;
        }

        .ps-form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .ps-form-header {
            background: #f8f9fb;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ps-form-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ps-form-icon {
            width: 38px;
            height: 38px;
            background: #ede9fe;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #635bff;
            font-size: 1rem;
        }

        .ps-form-title {
            font-weight: 700;
            font-size: .95rem;
            color: #111827;
            margin: 0;
        }

        .ps-form-body {
            padding: 22px;
        }

        .ps-field-label {
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            margin-bottom: 5px;
            display: block;
        }

        .ps-field-hint {
            font-size: .78rem;
            color: #9ca3af;
            margin-top: 4px;
        }

        .ps-input-reveal {
            position: relative;
        }

        .ps-input-reveal input {
            padding-right: 38px;
        }

        .ps-reveal-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .ps-reveal-btn:hover {
            color: #635bff;
        }

        .ps-form-footer {
            background: #f8f9fb;
            border-top: 1px solid #e5e7eb;
            padding: 14px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ps-footer-note {
            font-size: .78rem;
            color: #9ca3af;
        }

        .ps-webhook-box {
            background: #f0f7ff;
            border: 1px solid #bae0ff;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .ps-webhook-url {
            font-family: monospace;
            font-size: .8rem;
            color: #1d4ed8;
            word-break: break-all;
        }

        .ps-copy-btn {
            flex-shrink: 0;
            background: #fff;
            border: 1px solid #bae0ff;
            color: #1d4ed8;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: .76rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .ps-copy-btn:hover {
            background: #e0f0ff;
        }

        .ps-divider {
            border: none;
            border-top: 1px solid #f1f3f5;
            margin: 18px 0;
        }

        .ps-guide-footer {
            background: #fffbeb;
            border-top: 1px solid #fde68a;
            padding: 12px 18px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
            font-size: .76rem;
            color: #92400e;
        }

        .ps-guide-footer i {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ps-guide-link {
            display: block;
            padding: 10px 18px;
            background: #f8f9fb;
            border-top: 1px solid #e5e7eb;
            font-size: .8rem;
            color: #635bff;
            text-align: center;
            text-decoration: none;
        }

        .ps-guide-link:hover {
            background: #ede9fe;
            color: #4f46e5;
            text-decoration: none;
        }

        .ps-currency-input {
            max-width: 140px;
        }

        /* ── Sandbox / live mode switch ─────────────────────────────────────── */
        .ps-mode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .ps-mode-option {
            position: relative;
            display: block;
            margin: 0;
            cursor: pointer;
        }

        .ps-mode-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .ps-mode-box {
            border: 2px solid #e5e7eb;
            border-radius: 9px;
            padding: 13px 15px;
            display: flex;
            gap: 11px;
            align-items: flex-start;
            transition: border-color .15s, background .15s;
        }

        .ps-mode-option:hover .ps-mode-box {
            border-color: #c7d2fe;
        }

        .ps-mode-option input:checked+.ps-mode-box {
            border-color: #635bff;
            background: #f5f3ff;
        }

        .ps-mode-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .9rem;
        }

        .ps-mode-icon-test {
            background: #fef3c7;
            color: #b45309;
        }

        .ps-mode-icon-live {
            background: #d1fae5;
            color: #047857;
        }

        .ps-mode-name {
            font-weight: 700;
            font-size: .875rem;
            color: #111827;
            margin: 0 0 2px;
        }

        .ps-mode-desc {
            font-size: .76rem;
            color: #6b7280;
            margin: 0;
            line-height: 1.45;
        }

        .ps-key-panel {
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            margin-top: 16px;
            overflow: hidden;
        }

        .ps-key-panel-header {
            padding: 10px 15px;
            background: #f8f9fb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .82rem;
            font-weight: 700;
            color: #374151;
        }

        .ps-key-panel-body {
            padding: 15px;
        }

        .ps-key-panel-active {
            border-color: #635bff;
        }

        .ps-key-panel-active .ps-key-panel-header {
            background: #ede9fe;
            color: #4f46e5;
        }

        .ps-active-chip {
            margin-left: auto;
            background: #635bff;
            color: #fff;
            border-radius: 20px;
            padding: 2px 9px;
            font-size: .68rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .ps-inactive-chip {
            margin-left: auto;
            color: #9ca3af;
            font-size: .72rem;
        }

        .ps-sandbox-alert {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: .8rem;
            display: flex;
            gap: 9px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .ps-test-result {
            margin-top: 10px;
            font-size: .8rem;
            border-radius: 6px;
            padding: 9px 12px;
        }

        .ps-test-result-ok {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .ps-test-result-fail {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .ps-hidden {
            display: none;
        }

        .ps-footer-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .ps-test-cards {
            font-size: .76rem;
            color: #6b7280;
            margin: 8px 0 0;
        }
    </style>
@endsection

@section('settings-content')

    {{-- Hero banner --}}
    <div class="ps-hero">
        <div class="ps-hero-icon"><i class="fab fa-stripe"></i></div>
        <div>
            <p class="ps-hero-title">{{ __tr('Stripe Payment Gateway') }}</p>
            <p class="ps-hero-sub">
                {{ __tr('Accept card payments securely via Stripe. Enable the gateway and enter your API keys to start collecting payments.') }}
            </p>
        </div>
        <div class="ps-status-badge">
            @if ($stripeEnabled)
                @if ($isTestMode)
                    <span class="badge badge-warning"><i class="fas fa-flask mr-1"></i>{{ __tr('Sandbox') }}</span>
                @else
                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>{{ __tr('Live') }}</span>
                @endif
            @else
                <span class="badge badge-secondary"><i class="fas fa-pause-circle mr-1"></i>{{ __tr('Disabled') }}</span>
            @endif
        </div>
    </div>

    @if ($stripeEnabled && $isTestMode)
        <div class="ps-sandbox-alert">
            <i class="fas fa-flask"></i>
            <span>
                <strong>{{ __tr('Stripe is running in sandbox (test) mode.') }}</strong>
                {{ __tr('Payments use test cards only and no money moves. Switch to Live mode below when you are ready to take real payments.') }}
            </span>
        </div>
    @elseif ($stripeEnabled && !$stripeReady)
        <div class="ps-sandbox-alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span>
                <strong>{{ __tr('Stripe is enabled but not configured.') }}</strong>
                {{ __tr('Add the Publishable and Secret keys for the active mode — checkout will not offer card payment until you do.') }}
            </span>
        </div>
    @endif

    <div class="row">

        {{-- Left: form --}}
        <div class="col-lg-7 mb-4">
            <form action="{{ route('admin.payment.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="section" value="stripe">
                <div class="ps-form-card">
                    <div class="ps-form-header">
                        <div class="ps-form-header-left">
                            <div class="ps-form-icon"><i class="fab fa-stripe-s"></i></div>
                            <h3 class="ps-form-title">{{ __tr('Stripe Configuration') }}</h3>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="stripe_enabled" name="stripe_enabled"
                                value="1" {{ $stripeEnabled ? 'checked' : '' }}>
                            <label class="custom-control-label" for="stripe_enabled">{{ __tr('Enable Stripe') }}</label>
                        </div>
                    </div>
                    <div class="ps-form-body">

                        {{-- Sandbox / live switch --}}
                        <div class="form-group">
                            <label class="ps-field-label">{{ __tr('Gateway Mode') }}</label>
                            <div class="ps-mode-grid">
                                <label class="ps-mode-option">
                                    <input type="radio" name="stripe_mode" value="test" class="ps-mode-radio"
                                        {{ $isTestMode ? 'checked' : '' }}>
                                    <span class="ps-mode-box">
                                        <span class="ps-mode-icon ps-mode-icon-test"><i class="fas fa-flask"></i></span>
                                        <span>
                                            <span class="ps-mode-name d-block">{{ __tr('Sandbox (Test)') }}</span>
                                            <span
                                                class="ps-mode-desc d-block">{{ __tr('Use test keys and test cards. No real money is charged.') }}</span>
                                        </span>
                                    </span>
                                </label>
                                <label class="ps-mode-option">
                                    <input type="radio" name="stripe_mode" value="live" class="ps-mode-radio"
                                        {{ !$isTestMode ? 'checked' : '' }}>
                                    <span class="ps-mode-box">
                                        <span class="ps-mode-icon ps-mode-icon-live"><i class="fas fa-bolt"></i></span>
                                        <span>
                                            <span class="ps-mode-name d-block">{{ __tr('Live') }}</span>
                                            <span
                                                class="ps-mode-desc d-block">{{ __tr('Use live keys. Customers are charged for real.') }}</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <p class="ps-field-hint">
                                {{ __tr('Both key sets are stored — switching modes swaps which one is used for checkout, renewals and webhooks.') }}
                            </p>
                        </div>

                        {{-- Sandbox keys --}}
                        <div class="ps-key-panel {{ $isTestMode ? 'ps-key-panel-active' : '' }}" data-mode-panel="test">
                            <div class="ps-key-panel-header">
                                <i class="fas fa-flask"></i>{{ __tr('Sandbox (Test) Keys') }}
                                <span class="ps-active-chip {{ $isTestMode ? '' : 'ps-hidden' }}"
                                    data-chip="test">{{ __tr('In use') }}</span>
                                <span class="ps-inactive-chip {{ $isTestMode ? 'ps-hidden' : '' }}"
                                    data-chip-idle="test">{{ __tr('Stored, not in use') }}</span>
                            </div>
                            <div class="ps-key-panel-body">
                                <div class="form-group">
                                    <label class="ps-field-label"
                                        for="stripe_test_public_key">{{ __tr('Publishable Key') }}</label>
                                    <input type="text" id="stripe_test_public_key" name="stripe_test_public_key"
                                        class="form-control" value="{{ get_setting('stripe_test_public_key') }}"
                                        placeholder="pk_test_...">
                                </div>
                                <div class="form-group">
                                    <label class="ps-field-label"
                                        for="stripe_test_secret_key">{{ __tr('Secret Key') }}</label>
                                    <div class="ps-input-reveal">
                                        <input type="password" id="stripe_test_secret_key" name="stripe_test_secret_key"
                                            class="form-control" value="{{ get_setting('stripe_test_secret_key') }}"
                                            placeholder="sk_test_..." autocomplete="new-password">
                                        <button type="button" class="ps-reveal-btn" data-target="stripe_test_secret_key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="ps-field-label"
                                        for="stripe_test_webhook_secret">{{ __tr('Webhook Signing Secret') }}</label>
                                    <div class="ps-input-reveal">
                                        <input type="password" id="stripe_test_webhook_secret"
                                            name="stripe_test_webhook_secret" class="form-control"
                                            value="{{ get_setting('stripe_test_webhook_secret') }}" placeholder="whsec_..."
                                            autocomplete="new-password">
                                        <button type="button" class="ps-reveal-btn"
                                            data-target="stripe_test_webhook_secret">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <p class="ps-test-cards">
                                        {{ __tr('Test card:') }} <code>4242 4242 4242 4242</code> —
                                        {{ __tr('any future expiry, any CVC.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Live keys --}}
                        <div class="ps-key-panel {{ !$isTestMode ? 'ps-key-panel-active' : '' }}" data-mode-panel="live">
                            <div class="ps-key-panel-header">
                                <i class="fas fa-bolt"></i>{{ __tr('Live Keys') }}
                                <span class="ps-active-chip {{ !$isTestMode ? '' : 'ps-hidden' }}"
                                    data-chip="live">{{ __tr('In use') }}</span>
                                <span class="ps-inactive-chip {{ !$isTestMode ? 'ps-hidden' : '' }}"
                                    data-chip-idle="live">{{ __tr('Stored, not in use') }}</span>
                            </div>
                            <div class="ps-key-panel-body">
                                <div class="form-group">
                                    <label class="ps-field-label"
                                        for="stripe_live_public_key">{{ __tr('Publishable Key') }}</label>
                                    <input type="text" id="stripe_live_public_key" name="stripe_live_public_key"
                                        class="form-control" value="{{ get_setting('stripe_live_public_key') }}"
                                        placeholder="pk_live_...">
                                </div>
                                <div class="form-group">
                                    <label class="ps-field-label"
                                        for="stripe_live_secret_key">{{ __tr('Secret Key') }}</label>
                                    <div class="ps-input-reveal">
                                        <input type="password" id="stripe_live_secret_key" name="stripe_live_secret_key"
                                            class="form-control" value="{{ get_setting('stripe_live_secret_key') }}"
                                            placeholder="sk_live_..." autocomplete="new-password">
                                        <button type="button" class="ps-reveal-btn" data-target="stripe_live_secret_key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="ps-field-label"
                                        for="stripe_live_webhook_secret">{{ __tr('Webhook Signing Secret') }}</label>
                                    <div class="ps-input-reveal">
                                        <input type="password" id="stripe_live_webhook_secret"
                                            name="stripe_live_webhook_secret" class="form-control"
                                            value="{{ get_setting('stripe_live_webhook_secret') }}" placeholder="whsec_..."
                                            autocomplete="new-password">
                                        <button type="button" class="ps-reveal-btn"
                                            data-target="stripe_live_webhook_secret">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="ps-divider">

                        <div class="form-group">
                            <label class="ps-field-label">{{ __tr('Webhook Endpoint URL') }}</label>
                            <div class="ps-webhook-box">
                                <span class="ps-webhook-url" id="webhook-url-text">{{ url('/stripe/webhook') }}</span>
                                <button type="button" class="ps-copy-btn" id="copy-webhook-btn">
                                    <i class="fas fa-copy mr-1"></i>{{ __tr('Copy') }}
                                </button>
                            </div>
                            <p class="ps-field-hint">
                                {{ __tr('Register this URL in both your sandbox and live Stripe dashboards, then paste each signing secret above.') }}
                            </p>
                        </div>

                        <hr class="ps-divider">

                        <div class="form-group mb-0">
                            <label class="ps-field-label" for="stripe_currency">{{ __tr('Currency') }}</label>
                            <input type="text" id="stripe_currency" name="stripe_currency"
                                class="form-control ps-currency-input" value="{{ get_setting('stripe_currency', 'usd') }}"
                                placeholder="usd" maxlength="10">
                            <p class="ps-field-hint">{{ __tr('ISO currency code in lowercase — e.g.') }} <code>usd</code>,
                                <code>eur</code>, <code>gbp</code>.</p>
                        </div>

                        <div id="ps-test-result" class="ps-test-result ps-hidden"></div>
                    </div>
                    <div class="ps-form-footer">
                        <span class="ps-footer-note">
                            <i
                                class="fas fa-info-circle mr-1"></i>{{ __tr('Changes take effect immediately after saving.') }}
                        </span>
                        <div class="ps-footer-actions">
                            <button type="button" class="btn btn-outline-secondary" id="ps-test-stripe-btn">
                                <i class="fas fa-plug mr-1"></i>{{ __tr('Test Connection') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>{{ __tr('Save Settings') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right: setup guide --}}
        <div class="col-lg-5 mb-4">
            <div class="ps-guide-card">
                <div class="ps-guide-header">
                    <p class="ps-guide-header-title"><i class="fas fa-book-open"></i>{{ __tr('Setup Guide') }}</p>
                    <p class="ps-guide-header-sub">{{ __tr('Follow these steps to connect Stripe to your site.') }}</p>
                </div>
                <div class="ps-steps">
                    <div class="ps-step">
                        <div class="ps-step-num">1</div>
                        <div>
                            <p class="ps-step-title">{{ __tr('Create a Stripe account') }}</p>
                            <p class="ps-step-body mb-0">{{ __tr('Visit') }} <a
                                    href="https://dashboard.stripe.com/register" target="_blank"
                                    rel="noopener">dashboard.stripe.com</a> {{ __tr('and sign up or log in.') }}</p>
                        </div>
                    </div>
                    <div class="ps-step">
                        <div class="ps-step-num">2</div>
                        <div>
                            <p class="ps-step-title">{{ __tr('Copy your sandbox keys') }}</p>
                            <p class="ps-step-body mb-0">{{ __tr('With') }}
                                <strong>{{ __tr('Test mode') }}</strong> {{ __tr('on in Stripe, open') }}
                                <strong>{{ __tr('Developers → API keys') }}</strong> {{ __tr('and copy the') }}
                                <code>pk_test_...</code> {{ __tr('and') }} <code>sk_test_...</code>
                                {{ __tr('pair into the Sandbox panel.') }}</p>
                        </div>
                    </div>
                    <div class="ps-step">
                        <div class="ps-step-num">3</div>
                        <div>
                            <p class="ps-step-title">{{ __tr('Add a webhook endpoint') }}</p>
                            <p class="ps-step-body mb-0">{{ __tr('Go to') }}
                                <strong>{{ __tr('Developers → Webhooks → Add endpoint') }}</strong>.
                                {{ __tr('Paste your webhook URL, select') }} <code>payment_intent.succeeded</code>
                                {{ __tr('and') }} <code>payment_intent.payment_failed</code>,
                                {{ __tr('then copy the signing secret into the matching panel above. Do this once in test mode and once in live mode.') }}
                            </p>
                        </div>
                    </div>
                    <div class="ps-step">
                        <div class="ps-step-num">4</div>
                        <div>
                            <p class="ps-step-title">{{ __tr('Test, then go live') }}</p>
                            <p class="ps-step-body mb-0">
                                {{ __tr('Check out with card') }} <code>4242 4242 4242 4242</code>
                                {{ __tr('while in Sandbox. When it works end to end, turn off test mode in Stripe, paste your live keys, and switch the mode to') }}
                                <strong>{{ __tr('Live') }}</strong>.</p>
                        </div>
                    </div>
                </div>
                <div class="ps-guide-footer">
                    <i class="fas fa-shield-alt"></i>
                    <span><strong>{{ __tr('Security:') }}</strong>
                        {{ __tr('Never share your Secret Key or Webhook Secret. Keys are stored per mode, so live keys are never used while in sandbox.') }}</span>
                </div>
                <a href="https://stripe.com/docs/payments" target="_blank" rel="noopener" class="ps-guide-link">
                    <i class="fas fa-external-link-alt mr-1"></i>{{ __tr('View Stripe Docs') }}
                </a>
            </div>
        </div>

    </div>

    {{-- Bank Transfer (manual) --}}
    @php $bankEnabled = get_setting('bank_transfer_enabled', 0); @endphp
    <div class="row">
        <div class="col-lg-7 mb-4">
            <form action="{{ route('admin.payment.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="section" value="bank_transfer">
                <div class="ps-form-card">
                    <div class="ps-form-header">
                        <div class="ps-form-header-left">
                            <div class="ps-form-icon"><i class="fas fa-university"></i></div>
                            <h3 class="ps-form-title">{{ __tr('Bank Transfer (Manual)') }}</h3>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="bank_transfer_enabled"
                                name="bank_transfer_enabled" value="1" {{ $bankEnabled ? 'checked' : '' }}>
                            <label class="custom-control-label"
                                for="bank_transfer_enabled">{{ __tr('Enable Bank Transfer') }}</label>
                        </div>
                    </div>
                    <div class="ps-form-body">
                        <div class="form-group mb-0">
                            <label class="ps-field-label"
                                for="bank_transfer_instructions">{{ __tr('Payment Instructions') }}</label>
                            <textarea id="bank_transfer_instructions" name="bank_transfer_instructions" class="form-control" rows="6"
                                placeholder="{{ __tr('Bank name, account number, IBAN/SWIFT, and any reference the customer should include.') }}">{{ get_setting('bank_transfer_instructions') }}</textarea>
                            <p class="ps-field-hint">
                                {{ __tr('Shown to customers on the checkout page. They submit a reference and an uploaded slip, which you approve from Subscriptions.') }}
                            </p>
                        </div>
                    </div>
                    <div class="ps-form-footer">
                        <span class="ps-footer-note">
                            <i class="fas fa-info-circle mr-1"></i>{{ __tr('Bank transfers require manual approval.') }}
                        </span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>{{ __tr('Save Settings') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    @parent
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.ps-reveal-btn', function() {
                var targetId = $(this).data('target');
                var input = $('#' + targetId);
                var icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            $('#copy-webhook-btn').on('click', function() {
                var url = $('#webhook-url-text').text().trim();
                navigator.clipboard.writeText(url).then(function() {
                    var btn = $('#copy-webhook-btn');
                    btn.html('<i class="fas fa-check mr-1"></i>{{ __tr('Copied!') }}');
                    setTimeout(function() {
                        btn.html('<i class="fas fa-copy mr-1"></i>{{ __tr('Copy') }}');
                    }, 2000);
                });
            });

            // Highlight whichever key panel the selected mode will use. The saved
            // mode still decides what the server uses until the form is submitted.
            $('.ps-mode-radio').on('change', function() {
                var mode = $(this).val();
                $('[data-mode-panel]').each(function() {
                    var panelMode = $(this).data('mode-panel');
                    $(this).toggleClass('ps-key-panel-active', panelMode === mode);
                    $('[data-chip="' + panelMode + '"]').toggleClass('ps-hidden', panelMode !== mode);
                    $('[data-chip-idle="' + panelMode + '"]').toggleClass('ps-hidden', panelMode === mode);
                });
            });

            $('#ps-test-stripe-btn').on('click', function() {
                var btn = $(this);
                var box = $('#ps-test-result');

                btn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i>{{ __tr('Testing...') }}');

                $.post('{{ route('admin.payment.settings.test.stripe') }}', {
                        _token: '{{ csrf_token() }}'
                    })
                    .done(function(res) {
                        box.removeClass('ps-hidden ps-test-result-ok ps-test-result-fail')
                            .addClass(res.ok ? 'ps-test-result-ok' : 'ps-test-result-fail')
                            .html('<i class="fas fa-' + (res.ok ? 'check-circle' : 'times-circle') +
                                ' mr-1"></i>' + $('<div>').text(res.message).html());
                    })
                    .fail(function() {
                        box.removeClass('ps-hidden ps-test-result-ok')
                            .addClass('ps-test-result-fail')
                            .html(
                                '<i class="fas fa-times-circle mr-1"></i>{{ __tr('Could not reach the server.') }}'
                            );
                    })
                    .always(function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plug mr-1"></i>{{ __tr('Test Connection') }}');
                    });
            });
        })(jQuery);
    </script>
@endsection
