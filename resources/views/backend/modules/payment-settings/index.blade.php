@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Payment Settings'))
@section('settings-description', __tr('Configure your payment gateway and Stripe API keys.'))

@php $stripeEnabled = get_setting('stripe_enabled', 0); @endphp

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
                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>{{ __tr('Active') }}</span>
            @else
                <span class="badge badge-secondary"><i class="fas fa-pause-circle mr-1"></i>{{ __tr('Disabled') }}</span>
            @endif
        </div>
    </div>

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
                        <div class="form-group">
                            <label class="ps-field-label" for="stripe_public_key">{{ __tr('Publishable Key') }}</label>
                            <input type="text" id="stripe_public_key" name="stripe_public_key" class="form-control"
                                value="{{ get_setting('stripe_public_key') }}" placeholder="pk_live_...">
                            <p class="ps-field-hint">{{ __tr('Safe to expose in client-side code.') }}</p>
                        </div>
                        <div class="form-group">
                            <label class="ps-field-label" for="stripe_secret_key">{{ __tr('Secret Key') }}</label>
                            <div class="ps-input-reveal">
                                <input type="password" id="stripe_secret_key" name="stripe_secret_key" class="form-control"
                                    value="{{ get_setting('stripe_secret_key') }}" placeholder="sk_live_..."
                                    autocomplete="new-password">
                                <button type="button" class="ps-reveal-btn" data-target="stripe_secret_key">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="ps-field-hint">{{ __tr('Keep this secret. Never expose it in client-side code.') }}
                            </p>
                        </div>
                        <hr class="ps-divider">
                        <div class="form-group">
                            <label class="ps-field-label"
                                for="stripe_webhook_secret">{{ __tr('Webhook Signing Secret') }}</label>
                            <div class="ps-input-reveal">
                                <input type="password" id="stripe_webhook_secret" name="stripe_webhook_secret"
                                    class="form-control" value="{{ get_setting('stripe_webhook_secret') }}"
                                    placeholder="whsec_..." autocomplete="new-password">
                                <button type="button" class="ps-reveal-btn" data-target="stripe_webhook_secret">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="ps-webhook-box">
                                <span class="ps-webhook-url" id="webhook-url-text">{{ url('/stripe/webhook') }}</span>
                                <button type="button" class="ps-copy-btn" id="copy-webhook-btn">
                                    <i class="fas fa-copy mr-1"></i>{{ __tr('Copy') }}
                                </button>
                            </div>
                            <p class="ps-field-hint">
                                {{ __tr('Add the URL above as a webhook endpoint in your Stripe dashboard.') }}</p>
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
                    </div>
                    <div class="ps-form-footer">
                        <span class="ps-footer-note">
                            <i
                                class="fas fa-info-circle mr-1"></i>{{ __tr('Changes take effect immediately after saving.') }}
                        </span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>{{ __tr('Save Settings') }}
                        </button>
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
                            <p class="ps-step-title">{{ __tr('Copy your API keys') }}</p>
                            <p class="ps-step-body mb-0">{{ __tr('Go to') }}
                                <strong>{{ __tr('Developers → API keys') }}</strong>. {{ __tr('Copy your') }}
                                <code>pk_live_...</code> {{ __tr('and') }} <code>sk_live_...</code>
                                {{ __tr('keys. Use test keys for development.') }}</p>
                        </div>
                    </div>
                    <div class="ps-step">
                        <div class="ps-step-num">3</div>
                        <div>
                            <p class="ps-step-title">{{ __tr('Add a webhook endpoint') }}</p>
                            <p class="ps-step-body mb-0">{{ __tr('Go to') }}
                                <strong>{{ __tr('Developers → Webhooks → Add endpoint') }}</strong>.
                                {{ __tr('Paste your webhook URL, select') }} <code>payment_intent.succeeded</code>,
                                {{ __tr('then copy the signing secret into the field above.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="ps-guide-footer">
                    <i class="fas fa-shield-alt"></i>
                    <span><strong>{{ __tr('Security:') }}</strong>
                        {{ __tr('Never share your Secret Key or Webhook Secret. Use test keys during development.') }}</span>
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
        })(jQuery);
    </script>
@endsection
