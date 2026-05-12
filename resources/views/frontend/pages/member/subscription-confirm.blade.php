@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Confirm Subscription') }} - {{ get_setting('site_name') }}</title>
    <style>
        .sc-layout {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
        }

        .sc-sidebar {
            flex: 0 0 340px;
            max-width: 340px;
            padding-right: 24px;
            margin-bottom: 24px;
        }

        .sc-main {
            flex: 1;
            min-width: 280px;
            margin-bottom: 24px;
        }

        .sc-summary-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: var(--heading-color);
        }

        .sc-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .sc-summary-row:last-child {
            border-bottom: none;
            padding: 14px 0 0;
            margin-top: 4px;
        }

        .sc-summary-label {
            color: #6b7280;
        }

        .sc-total-label {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--heading-color);
        }

        .sc-total-value {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--main-color);
        }

        .sc-payment-card {
            margin-bottom: 20px;
        }

        .sc-payment-card:last-child {
            margin-bottom: 0;
        }

        .sc-method-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .sc-method-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sc-method-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: var(--heading-color);
        }

        .sc-method-sub {
            color: #6b7280;
        }

        .sc-method-desc {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 18px;
        }

        .sc-btn-full {
            width: 100%;
        }

    </style>
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

        {{-- Plan Summary --}}
        <div class="sc-sidebar">
            <div class="dashboard-card">
                <h3 class="sc-summary-title">{{ __tr('Order Summary') }}</h3>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Plan') }}</span>
                    <strong>{{ $plan->translation('title') }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Duration') }}</span>
                    <strong>{{ $plan->duration_days }} {{ __tr('days') }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Ad Posting') }}</span>
                    <strong>{{ $plan->listing_quantity }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Featured') }}</span>
                    <strong>{{ $plan->featured_listing_quantity }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-summary-label">{{ __tr('Gallery Images') }}</span>
                    <strong>{{ $plan->gallery_image_quantity }}</strong>
                </div>
                <div class="sc-summary-row">
                    <span class="sc-total-label">{{ __tr('Total') }}</span>
                    <span class="sc-total-value">{{ format_amount($plan->price) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Methods --}}
        <div class="sc-main">

            @if (!$stripeEnabled)
                <div class="sub-warning-banner">
                    <i class="fas fa-exclamation-triangle sub-warning-icon"></i>
                    <div>
                        <strong class="sub-warning-title">{{ __tr('No payment method available') }}</strong>
                        <p class="sub-warning-text">{{ __tr('Please contact the administrator.') }}</p>
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
@endsection
