@php
    $links = [['title' => 'Payment Settings', 'route' => '', 'active' => true]];
    $stripeEnabled = get_setting('stripe_enabled', 0);
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Payment Settings
@endsection
@section('page-style')
    <style>
        .ps-hero {
            background: linear-gradient(135deg, #635bff 0%, #0ea5e9 100%);
            border-radius: 12px;
            padding: 28px 32px;
            color: #fff;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .ps-hero-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, .18);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            flex-shrink: 0;
        }

        .ps-hero-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .ps-hero-sub {
            font-size: .9rem;
            opacity: .85;
            margin: 0;
        }

        .ps-status-badge {
            margin-left: auto;
            flex-shrink: 0;
        }

        .ps-status-badge .badge {
            font-size: .85rem;
            padding: 7px 16px;
            border-radius: 20px;
        }

        /* Steps sidebar */
        .ps-guide-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            position: sticky;
            top: 70px;
        }

        .ps-guide-header {
            background: linear-gradient(135deg, #635bff 0%, #0ea5e9 100%);
            padding: 16px 20px;
            color: #fff;
        }

        .ps-guide-header-title {
            font-size: .9rem;
            font-weight: 700;
            margin: 0 0 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ps-guide-header-sub {
            font-size: .78rem;
            opacity: .85;
            margin: 0;
        }

        .ps-steps {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .ps-step {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .ps-step:last-child {
            border-bottom: none;
        }

        .ps-step-num {
            width: 32px;
            height: 32px;
            background: #635bff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .95rem;
            flex-shrink: 0;
        }

        .ps-step-title {
            font-weight: 700;
            font-size: .9rem;
            margin-bottom: 5px;
            color: #111827;
        }

        .ps-step-body {
            font-size: .82rem;
            color: #6b7280;
            line-height: 1.5;
        }

        .ps-step-body code {
            background: #f3f4f6;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: .8rem;
            color: #374151;
        }

        .ps-step-body a {
            color: #635bff;
        }

        /* Form card */
        .ps-form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .ps-form-header {
            background: #f8f9fb;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 24px;
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
            width: 40px;
            height: 40px;
            background: #ede9fe;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #635bff;
            font-size: 1.1rem;
        }

        .ps-form-title {
            font-weight: 700;
            font-size: 1rem;
            color: #111827;
            margin: 0;
        }

        .ps-form-body {
            padding: 24px;
        }

        .ps-field-label {
            font-size: .82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            margin-bottom: 6px;
            display: block;
        }

        .ps-field-hint {
            font-size: .8rem;
            color: #9ca3af;
            margin-top: 5px;
        }

        .ps-input-reveal {
            position: relative;
        }

        .ps-input-reveal input {
            padding-right: 40px;
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
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ps-footer-note {
            font-size: .8rem;
            color: #9ca3af;
        }

        .ps-webhook-box {
            background: #f0f7ff;
            border: 1px solid #bae0ff;
            border-radius: 8px;
            padding: 12px 14px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .ps-webhook-url {
            font-family: monospace;
            font-size: .82rem;
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
            font-size: .78rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .ps-copy-btn:hover {
            background: #e0f0ff;
        }

        .ps-divider {
            border: none;
            border-top: 1px solid #f1f3f5;
            margin: 20px 0;
        }

        .ps-security-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: .82rem;
            color: #92400e;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 24px;
        }

        .ps-security-note i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .ps-guide-footer {
            background: #fffbeb;
            border-top: 1px solid #fde68a;
            padding: 12px 20px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
            font-size: .78rem;
            color: #92400e;
        }

        .ps-guide-footer i {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ps-guide-link {
            display: block;
            padding: 10px 20px;
            background: #f8f9fb;
            border-top: 1px solid #e5e7eb;
            font-size: .82rem;
            color: #635bff;
            text-align: center;
            text-decoration: none;
        }

        .ps-guide-link:hover {
            background: #ede9fe;
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="Payment Settings" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Hero --}}
            <div class="ps-hero">
                <div class="ps-hero-icon">
                    <i class="fab fa-stripe"></i>
                </div>
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
                        <span class="badge badge-secondary"><i
                                class="fas fa-pause-circle mr-1"></i>{{ __tr('Disabled') }}</span>
                    @endif
                </div>
            </div>

            {{-- Settings Form + Guide --}}
            <div class="row">

                {{-- Left: Form --}}
                <div class="col-lg-7 mb-4">
                    <form action="{{ route('admin.payment.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="stripe">

                        <div class="ps-form-card">

                            {{-- Card header --}}
                            <div class="ps-form-header">
                                <div class="ps-form-header-left">
                                    <div class="ps-form-icon">
                                        <i class="fab fa-stripe-s"></i>
                                    </div>
                                    <h3 class="ps-form-title">{{ __tr('Stripe Configuration') }}</h3>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="stripe_enabled"
                                        name="stripe_enabled" value="1" {{ $stripeEnabled ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="stripe_enabled">
                                        {{ __tr('Enable Stripe') }}
                                    </label>
                                </div>
                            </div>

                            {{-- Card body --}}
                            <div class="ps-form-body">

                                {{-- Publishable Key --}}
                                <div class="form-group">
                                    <label class="ps-field-label" for="stripe_public_key">
                                        {{ __tr('Publishable Key') }}
                                    </label>
                                    <input type="text" id="stripe_public_key" name="stripe_public_key"
                                        class="form-control" value="{{ get_setting('stripe_public_key') }}"
                                        placeholder="pk_live_...">
                                    <p class="ps-field-hint">{{ __tr('Safe to expose in client-side code.') }}</p>
                                </div>

                                {{-- Secret Key --}}
                                <div class="form-group">
                                    <label class="ps-field-label" for="stripe_secret_key">
                                        {{ __tr('Secret Key') }}
                                    </label>
                                    <div class="ps-input-reveal">
                                        <input type="password" id="stripe_secret_key" name="stripe_secret_key"
                                            class="form-control" value="{{ get_setting('stripe_secret_key') }}"
                                            placeholder="sk_live_..." autocomplete="new-password">
                                        <button type="button" class="ps-reveal-btn" data-target="stripe_secret_key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <p class="ps-field-hint">
                                        {{ __tr('Keep this secret. Never expose it in client-side code.') }}</p>
                                </div>

                                <hr class="ps-divider">

                                {{-- Webhook Secret --}}
                                <div class="form-group">
                                    <label class="ps-field-label" for="stripe_webhook_secret">
                                        {{ __tr('Webhook Signing Secret') }}
                                    </label>
                                    <div class="ps-input-reveal">
                                        <input type="password" id="stripe_webhook_secret" name="stripe_webhook_secret"
                                            class="form-control" value="{{ get_setting('stripe_webhook_secret') }}"
                                            placeholder="whsec_..." autocomplete="new-password">
                                        <button type="button" class="ps-reveal-btn" data-target="stripe_webhook_secret">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="ps-webhook-box">
                                        <span class="ps-webhook-url"
                                            id="webhook-url-text">{{ url('/stripe/webhook') }}</span>
                                        <button type="button" class="ps-copy-btn" id="copy-webhook-btn">
                                            <i class="fas fa-copy mr-1"></i>{{ __tr('Copy') }}
                                        </button>
                                    </div>
                                    <p class="ps-field-hint">
                                        {{ __tr('Add the URL above as a webhook endpoint in your Stripe dashboard to receive payment events.') }}
                                    </p>
                                </div>

                                <hr class="ps-divider">

                                {{-- Currency --}}
                                <div class="form-group mb-0">
                                    <label class="ps-field-label" for="stripe_currency">
                                        {{ __tr('Currency') }}
                                    </label>
                                    <input type="text" id="stripe_currency" name="stripe_currency" class="form-control"
                                        style="max-width:140px;" value="{{ get_setting('stripe_currency', 'usd') }}"
                                        placeholder="usd" maxlength="10">
                                    <p class="ps-field-hint">{{ __tr('ISO currency code in lowercase — e.g.') }}
                                        <code>usd</code>, <code>eur</code>, <code>gbp</code>.
                                    </p>
                                </div>

                            </div>

                            {{-- Card footer --}}
                            <div class="ps-form-footer">
                                <span class="ps-footer-note">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __tr('Changes take effect immediately after saving.') }}
                                </span>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> {{ __tr('Save Settings') }}
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- Right: Setup Guide --}}
                <div class="col-lg-5 mb-4">
                    <div class="ps-guide-card">

                        <div class="ps-guide-header">
                            <p class="ps-guide-header-title">
                                <i class="fas fa-book-open"></i>
                                {{ __tr('Setup Guide') }}
                            </p>
                            <p class="ps-guide-header-sub">
                                {{ __tr('Follow these steps to connect Stripe to your site.') }}</p>
                        </div>

                        <div class="ps-steps">

                            <div class="ps-step">
                                <div class="ps-step-num">1</div>
                                <div>
                                    <p class="ps-step-title">{{ __tr('Create a Stripe account') }}</p>
                                    <p class="ps-step-body mb-0">
                                        {{ __tr('Visit') }}
                                        <a href="https://dashboard.stripe.com/register" target="_blank"
                                            rel="noopener">dashboard.stripe.com</a>
                                        {{ __tr('and sign up or log in to your existing account.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ps-step">
                                <div class="ps-step-num">2</div>
                                <div>
                                    <p class="ps-step-title">{{ __tr('Copy your API keys') }}</p>
                                    <p class="ps-step-body mb-0">
                                        {{ __tr('Go to') }} <strong>{{ __tr('Developers → API keys') }}</strong>.
                                        {{ __tr('Copy your') }} <code>pk_live_...</code> {{ __tr('and') }}
                                        <code>sk_live_...</code> {{ __tr('keys.') }}
                                        {{ __tr('Use') }} <code>pk_test_</code> / <code>sk_test_</code>
                                        {{ __tr('for testing.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ps-step">
                                <div class="ps-step-num">3</div>
                                <div>
                                    <p class="ps-step-title">{{ __tr('Add a webhook endpoint') }}</p>
                                    <p class="ps-step-body mb-0">
                                        {{ __tr('Go to') }}
                                        <strong>{{ __tr('Developers → Webhooks → Add endpoint') }}</strong>.
                                        {{ __tr('Paste your webhook URL, select') }} <code>payment_intent.succeeded</code>,
                                        {{ __tr('then copy the signing secret') }} (<code>whsec_...</code>)
                                        {{ __tr('into the Webhook Secret field.') }}
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="ps-guide-footer">
                            <i class="fas fa-shield-alt"></i>
                            <span>
                                <strong>{{ __tr('Security:') }}</strong>
                                {{ __tr('Never share your Secret Key or Webhook Secret. Use test keys during development.') }}
                            </span>
                        </div>

                        <a href="https://stripe.com/docs/payments" target="_blank" rel="noopener" class="ps-guide-link">
                            <i class="fas fa-external-link-alt mr-1"></i>{{ __tr('View Stripe Docs') }}
                        </a>

                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

            // Reveal/hide password fields
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

            // Copy webhook URL
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
