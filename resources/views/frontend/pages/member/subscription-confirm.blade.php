@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Confirm Subscription') }} - {{ get_setting('site_name') }}</title>
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
