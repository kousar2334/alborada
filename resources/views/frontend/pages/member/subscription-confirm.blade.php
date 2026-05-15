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
                    <span class="sc-summary-label">{{ __tr('Ad Postings') }}</span>
                    <strong class="sc-summary-value">{{ $plan->listing_quantity }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Featured Ads') }}</span>
                    <strong class="sc-summary-value">{{ $plan->featured_listing_quantity }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Gallery Images') }}</span>
                    <strong class="sc-summary-value">{{ $plan->gallery_image_quantity }}</strong>
                </div>

                <div class="sc-total-row">
                    <span class="sc-total-label">{{ __tr('Total Due') }}</span>
                    <span class="sc-total-value">{{ format_amount($plan->price) }}</span>
                </div>

                <div class="sc-secure-note">
                    <i class="fas fa-shield-alt sc-secure-icon"></i>
                    <span>{{ __tr('Secure, encrypted payment') }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Section --}}
        <div class="sc-main">

            @if (!$stripeEnabled)
                <div class="sc-empty-state">
                    <div class="sc-empty-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4 class="sc-empty-title">{{ __tr('No Payment Method Available') }}</h4>
                    <p class="sc-empty-text">{{ __tr('Please contact the administrator to enable a payment method.') }}</p>
                </div>
            @else
                <div class="dashboard-card sc-pay-card">
                    <div class="sc-pay-header">
                        <span class="sc-pay-icon"><i class="fab fa-stripe-s"></i></span>
                        <div>
                            <h4 class="sc-pay-title">{{ __tr('Pay with Card') }}</h4>
                            <p class="sc-pay-sub">{{ __tr('Your card details are processed securely by Stripe.') }}</p>
                        </div>
                        <div class="sc-card-logos">
                            <i class="fab fa-cc-visa sc-card-logo"></i>
                            <i class="fab fa-cc-mastercard sc-card-logo"></i>
                            <i class="fab fa-cc-amex sc-card-logo"></i>
                        </div>
                    </div>

                    <div class="sc-pay-body">
                        <div id="sc-stripe-error" class="sc-stripe-error sc-hidden"></div>

                        <div class="sc-field-group">
                            <label class="sc-field-label">{{ __tr('Card Information') }}</label>
                            <div id="sc-card-element" class="sc-card-element"></div>
                        </div>

                        <button id="sc-pay-btn" class="sc-pay-btn" type="button">
                            <span id="sc-pay-btn-text">
                                <i class="fas fa-lock sc-btn-lock"></i>
                                {{ __tr('Pay') }} {{ format_amount($plan->price) }}
                            </span>
                            <span id="sc-pay-btn-loading" class="sc-btn-loading sc-hidden">
                                <i class="fas fa-spinner fa-spin"></i> {{ __tr('Processing...') }}
                            </span>
                        </button>
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

                var cardElement = elements.create('card', {
                    style: {
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
                    }
                });
                cardElement.mount('#sc-card-element');

                cardElement.on('change', function(event) {
                    var errorEl = document.getElementById('sc-stripe-error');
                    if (event.error) {
                        errorEl.textContent = event.error.message;
                        errorEl.classList.remove('sc-hidden');
                    } else {
                        errorEl.classList.add('sc-hidden');
                    }
                });

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
                                    card: cardElement
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
