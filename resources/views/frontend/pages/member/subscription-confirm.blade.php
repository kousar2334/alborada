@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Confirm Subscription') }} - {{ get_setting('site_name') }}</title>
    @if ($stripeEnabled)
        <script src="https://js.stripe.com/v3/"></script>
    @endif
@endsection

@section('dashboard-content')
    <div class="my-listings-header">
        <h1>{{ __tr('Confirm Subscription') }}</h1>
        <div class="btn-wrapper">
            <a href="{{ route('pricing.plans') }}" class="cmn-btn btn-outline">
                <i class="fas fa-arrow-left"></i> {{ __tr('Back to Plans') }}
            </a>
        </div>
    </div>

    <div class="sc-layout">

        {{-- Order Summary --}}
        <div class="sc-sidebar">
            <div class="dashboard-card sc-order-card">
                <div class="sc-order-header">
                    <span class="sc-order-badge"><i class="fas fa-receipt"></i></span>
                    <h3 class="sc-summary-title">{{ __tr('Order Summary') }}</h3>
                </div>

                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Plan') }}</span>
                    <strong class="sc-summary-value">{{ $plan->translation('title') }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Duration') }}</span>
                    <strong class="sc-summary-value">{{ $plan->duration_days }} {{ __tr('days') }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Connections') }}</span>
                    <strong class="sc-summary-value">{{ $plan->max_connections }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Quality') }}</span>
                    <strong class="sc-summary-value">{{ $plan->streaming_quality }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Catch-up TV') }}</span>
                    <strong class="sc-summary-value">
                        @if ($plan->catchup_days > 0)
                            {{ $plan->catchup_days }} {{ __tr('days') }}
                        @else
                            {{ __tr('Not included') }}
                        @endif
                    </strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('DVR') }}</span>
                    <strong
                        class="sc-summary-value">{{ $plan->dvr_enabled ? __tr('Included') : __tr('Not included') }}</strong>
                </div>

                <div class="sc-total-row">
                    <span class="sc-total-label">{{ __tr('Total Due') }}</span>
                    <span class="sc-total-value">{{ format_amount($plan->effective_price) }}</span>
                </div>

                <div class="sc-secure-note">
                    <i class="fas fa-shield-alt sc-secure-icon"></i>
                    <span>{{ __tr('Secure, encrypted payment') }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Section --}}
        <div class="sc-main">

            @if (!$stripeEnabled && !$bankTransferEnabled)
                <div class="sc-empty-state">
                    <div class="sc-empty-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4 class="sc-empty-title">{{ __tr('No Payment Method Available') }}</h4>
                    <p class="sc-empty-text">{{ __tr('Please contact the administrator to enable a payment method.') }}</p>
                </div>
            @endif

            @if ($stripeEnabled)
                <div class="dashboard-card sc-pay-card">
                    <div class="sc-pay-header">
                        <span class="sc-pay-icon"><i class="fab fa-stripe-s"></i></span>
                        <div>
                            <h4 class="sc-pay-title">{{ __tr('Pay with Card') }}</h4>
                            <p class="sc-pay-sub">{{ __tr('Your card details are processed securely by Stripe.') }}</p>
                        </div>
                    </div>

                    <div class="sc-pay-body">
                        <div id="sc-stripe-error" class="sc-stripe-error sc-hidden"></div>

                        <div class="sc-field-group">
                            <label class="sc-field-label">{{ __tr('Card Number') }}</label>
                            <div class="sc-card-number-wrap">
                                <div id="sc-card-number" class="sc-card-element"></div>
                                <i id="sc-brand-icon" class="sc-brand-icon sc-hidden"></i>
                            </div>
                        </div>

                        <div class="sc-field-row">
                            <div class="sc-field-group sc-field-col">
                                <label class="sc-field-label">{{ __tr('Expiry Date') }}</label>
                                <div id="sc-card-expiry" class="sc-card-element"></div>
                            </div>
                            <div class="sc-field-group sc-field-col">
                                <label class="sc-field-label">{{ __tr('CVC') }}</label>
                                <div id="sc-card-cvc" class="sc-card-element"></div>
                            </div>
                        </div>

                        <button id="sc-pay-btn" class="sc-pay-btn" type="button">
                            <span id="sc-pay-btn-text">
                                <i class="fas fa-lock sc-btn-lock"></i>
                                {{ __tr('Pay') }} {{ format_amount($plan->effective_price) }}
                            </span>
                            <span id="sc-pay-btn-loading" class="sc-btn-loading sc-hidden">
                                <i class="fas fa-spinner fa-spin"></i> {{ __tr('Processing...') }}
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            @if ($bankTransferEnabled)
                <div class="dashboard-card sc-pay-card sc-bank-card">
                    <div class="sc-pay-header">
                        <span class="sc-pay-icon"><i class="fas fa-university"></i></span>
                        <div>
                            <h4 class="sc-pay-title">{{ __tr('Pay by Bank Transfer') }}</h4>
                            <p class="sc-pay-sub">{{ __tr('Transfer the total, then submit your reference and receipt below.') }}</p>
                        </div>
                    </div>

                    <div class="sc-pay-body">
                        @if (!empty($bankTransferInstructions))
                            <div class="sc-bank-instructions">
                                {!! nl2br(e($bankTransferInstructions)) !!}
                            </div>
                        @endif

                        <form action="{{ route('membership.bank.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="membership_id" value="{{ $plan->id }}">

                            <div class="sc-field-group">
                                <label class="sc-field-label" for="bank_transaction_number">{{ __tr('Transfer Reference / Transaction Number') }}</label>
                                <input type="text" id="bank_transaction_number" name="bank_transaction_number"
                                    class="form-control" required maxlength="191">
                            </div>

                            <div class="sc-field-group">
                                <label class="sc-field-label" for="bank_slip">{{ __tr('Upload Payment Slip') }} <span class="sc-field-hint">({{ __tr('JPG, PNG or PDF, max 5MB') }})</span></label>
                                <input type="file" id="bank_slip" name="bank_slip" class="form-control"
                                    accept=".jpg,.jpeg,.png,.pdf" required>
                            </div>

                            <button type="submit" class="sc-pay-btn">
                                <i class="fas fa-paper-plane sc-btn-lock"></i>
                                {{ __tr('Submit Bank Transfer') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}');
                @endforeach
            });
        </script>
    @endif

    @if ($stripeEnabled)
        <script>
            (function() {
                var stripe = Stripe('{{ $stripePublicKey }}');
                var elements = stripe.elements();

                var elementStyle = {
                    base: {
                        fontSize: '15px',
                        color: '#1f2937',
                        fontFamily: 'inherit',
                        '::placeholder': {
                            color: '#9ca3af'
                        }
                    },
                    invalid: {
                        color: '#e53e3e'
                    }
                };

                var cardNumber = elements.create('cardNumber', {
                    style: elementStyle
                });
                var cardExpiry = elements.create('cardExpiry', {
                    style: elementStyle
                });
                var cardCvc = elements.create('cardCvc', {
                    style: elementStyle
                });

                cardNumber.mount('#sc-card-number');
                cardExpiry.mount('#sc-card-expiry');
                cardCvc.mount('#sc-card-cvc');

                cardNumber.on('change', function(event) {
                    handleError(event);
                    updateBrand(event.brand);
                });

                [cardExpiry, cardCvc].forEach(function(el) {
                    el.on('change', handleError);
                });

                var brandIconMap = {
                    visa: 'fab fa-cc-visa',
                    mastercard: 'fab fa-cc-mastercard',
                    amex: 'fab fa-cc-amex',
                    discover: 'fab fa-cc-discover',
                    diners: 'fab fa-cc-diners-club',
                    jcb: 'fab fa-cc-jcb',
                    unionpay: 'fas fa-credit-card',
                };

                function updateBrand(brand) {
                    var icon = document.getElementById('sc-brand-icon');
                    var cls = brandIconMap[brand];
                    if (cls) {
                        icon.className = 'sc-brand-icon ' + cls;
                    } else {
                        icon.className = 'sc-brand-icon sc-hidden';
                    }
                }

                function handleError(event) {
                    var errorEl = document.getElementById('sc-stripe-error');
                    if (event.error) {
                        errorEl.textContent = event.error.message;
                        errorEl.classList.remove('sc-hidden');
                    } else {
                        errorEl.classList.add('sc-hidden');
                    }
                }

                document.getElementById('sc-pay-btn').addEventListener('click', function() {
                    setLoading(true);

                    fetch('{{ route('membership.stripe.initiate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                membership_id: {{ $plan->id }}
                            })
                        })
                        .then(function(res) {
                            return res.json();
                        })
                        .then(function(data) {
                            if (data.error) {
                                showError(data.error);
                                setLoading(false);
                                return;
                            }
                            return stripe.confirmCardPayment(data.client_secret, {
                                payment_method: {
                                    card: cardNumber
                                }
                            });
                        })
                        .then(function(result) {
                            if (!result) return;
                            if (result.error) {
                                showError(result.error.message);
                                setLoading(false);
                            } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                                window.location.href =
                                    '{{ route('membership.stripe.success') }}?payment_intent=' +
                                    result.paymentIntent.id;
                            }
                        })
                        .catch(function() {
                            showError('{{ __tr('An unexpected error occurred. Please try again.') }}');
                            setLoading(false);
                        });
                });

                function setLoading(loading) {
                    document.getElementById('sc-pay-btn').disabled = loading;
                    document.getElementById('sc-pay-btn-text').classList.toggle('sc-hidden', loading);
                    document.getElementById('sc-pay-btn-loading').classList.toggle('sc-hidden', !loading);
                }

                function showError(msg) {
                    var errorEl = document.getElementById('sc-stripe-error');
                    errorEl.textContent = msg;
                    errorEl.classList.remove('sc-hidden');
                }
            })();
        </script>
    @endif
@endsection
