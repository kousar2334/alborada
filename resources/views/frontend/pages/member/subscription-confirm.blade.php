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

        .sc-method-icon.ssl {
            background: #e0f2fe;
        }

        .sc-method-icon.ssl i {
            color: #0369a1;
            font-size: 1.1rem;
        }

        .sc-method-icon.bank {
            background: #f0fdf4;
        }

        .sc-method-icon.bank i {
            color: #16a34a;
            font-size: 1.1rem;
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

        .sc-btn-bank {
            background: #16a34a !important;
            border-color: #16a34a !important;
        }

        .sc-bank-details {
            background: #f9fafb;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .sc-bank-details-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 12px;
        }

        .sc-bank-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .sc-bank-row:last-child {
            border-bottom: none;
        }

        .sc-bank-row-label {
            color: #6b7280;
        }

        .sc-bank-mono {
            font-family: monospace;
        }

        .sc-instructions {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 18px;
            font-size: 0.875rem;
            color: #92400e;
        }

        .sc-file-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--heading-color);
        }

        .sc-file-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            background: #fff;
        }

        .sc-file-hint {
            color: #9ca3af;
            margin-top: 4px;
            display: block;
        }

        .sc-file-error {
            color: #dc2626;
            font-size: 0.8rem;
        }

        .sc-file-required {
            color: #dc2626;
        }

        .sc-file-group {
            margin-bottom: 16px;
        }

        .sc-pending-note {
            margin-top: 12px;
            font-size: 0.8rem;
            color: #9ca3af;
            text-align: center;
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

            @if (!$sslEnabled && !$bankTransferEnabled)
                <div class="sub-warning-banner">
                    <i class="fas fa-exclamation-triangle sub-warning-icon"></i>
                    <div>
                        <strong class="sub-warning-title">{{ __tr('No payment method available') }}</strong>
                        <p class="sub-warning-text">{{ __tr('Please contact the administrator.') }}</p>
                    </div>
                </div>
            @endif

            {{-- SSLCommerz --}}
            @if ($sslEnabled)
                <div class="dashboard-card sc-payment-card">
                    <div class="sc-method-header">
                        <div class="sc-method-icon ssl">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h4 class="sc-method-title">{{ __tr('Online Payment') }}</h4>
                            <small class="sc-method-sub">{{ __tr('Pay securely via SSLCommerz') }}</small>
                        </div>
                    </div>
                    <p class="sc-method-desc">
                        {{ __tr('Supports credit/debit cards, mobile banking, and internet banking. Instant activation.') }}
                    </p>
                    <form action="{{ route('membership.initiate.payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="{{ $plan->id }}">
                        <button type="submit" class="cmn-btn sc-btn-full">
                            <i class="fas fa-lock me-2"></i>
                            {{ __tr('Pay') }} {{ format_amount($plan->price) }} {{ __tr('Online') }}
                        </button>
                    </form>
                </div>
            @endif

            {{-- Bank Transfer --}}
            @if ($bankTransferEnabled)
                <div class="dashboard-card sc-payment-card">
                    <div class="sc-method-header">
                        <div class="sc-method-icon bank">
                            <i class="fas fa-university"></i>
                        </div>
                        <div>
                            <h4 class="sc-method-title">{{ __tr('Bank Transfer') }}</h4>
                            <small class="sc-method-sub">{{ __tr('Manual verification required') }}</small>
                        </div>
                    </div>

                    {{-- Bank Details --}}
                    <div class="sc-bank-details">
                        <p class="sc-bank-details-label">{{ __tr('Bank Account Details') }}</p>
                        @if (get_setting('bank_name'))
                            <div class="sc-bank-row">
                                <span class="sc-bank-row-label">{{ __tr('Bank Name') }}</span>
                                <strong>{{ get_setting('bank_name') }}</strong>
                            </div>
                        @endif
                        @if (get_setting('bank_account_name'))
                            <div class="sc-bank-row">
                                <span class="sc-bank-row-label">{{ __tr('Account Name') }}</span>
                                <strong>{{ get_setting('bank_account_name') }}</strong>
                            </div>
                        @endif
                        @if (get_setting('bank_account_number'))
                            <div class="sc-bank-row">
                                <span class="sc-bank-row-label">{{ __tr('Account Number') }}</span>
                                <strong class="sc-bank-mono">{{ get_setting('bank_account_number') }}</strong>
                            </div>
                        @endif
                        @if (get_setting('bank_routing_number'))
                            <div class="sc-bank-row">
                                <span class="sc-bank-row-label">{{ __tr('Routing Number') }}</span>
                                <strong class="sc-bank-mono">{{ get_setting('bank_routing_number') }}</strong>
                            </div>
                        @endif
                        @if (get_setting('bank_branch'))
                            <div class="sc-bank-row">
                                <span class="sc-bank-row-label">{{ __tr('Branch') }}</span>
                                <strong>{{ get_setting('bank_branch') }}</strong>
                            </div>
                        @endif
                    </div>

                    @if (get_setting('bank_instructions'))
                        <div class="sc-instructions">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ get_setting('bank_instructions') }}
                        </div>
                    @endif

                    <form action="{{ route('membership.bank.payment') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="membership_id" value="{{ $plan->id }}">
                        <div class="sc-file-group">
                            <label class="sc-file-label">
                                {{ __tr('Bank Transaction Number') }}
                                <span class="sc-file-required">*</span>
                            </label>
                            <input type="text" name="bank_transaction_number" class="sc-file-input"
                                value="{{ old('bank_transaction_number') }}" placeholder="{{ __tr('e.g. TRN123456789') }}"
                                required>
                            <small class="sc-file-hint">
                                {{ __tr('Enter the transaction/reference number from your bank receipt.') }}
                            </small>
                            @error('bank_transaction_number')
                                <span class="sc-file-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="sc-file-group">
                            <label class="sc-file-label">
                                {{ __tr('Upload Payment Slip') }}
                                <span class="sc-file-required">*</span>
                            </label>
                            <input type="file" name="bank_slip" accept=".jpg,.jpeg,.png,.pdf" required
                                class="sc-file-input">
                            <small class="sc-file-hint">
                                {{ __tr('Accepted formats: JPG, PNG, PDF. Max 4MB.') }}
                            </small>
                            @error('bank_slip')
                                <span class="sc-file-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="cmn-btn sc-btn-full sc-btn-bank">
                            <i class="fas fa-paper-plane me-2"></i> {{ __tr('Submit Payment') }}
                        </button>
                    </form>

                    <p class="sc-pending-note">
                        <i class="fas fa-clock me-1"></i>
                        {{ __tr('Your subscription will be activated after admin verification.') }}
                    </p>
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
