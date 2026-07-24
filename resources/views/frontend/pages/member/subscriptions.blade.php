@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('My Subscriptions') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')
    <div class="my-listings-header">
        <h1>{{ __tr('My Subscriptions') }}</h1>
        <div class="btn-wrapper">
            <a href="{{ route('pricing.plans') }}" class="cmn-btn">
                {{ __tr('Upgrade Plan') }}
            </a>
        </div>
    </div>

    {{-- Active Subscription Banner --}}
    @if ($activeSubscription)
        <div class="sub-active-banner">
            <div class="sub-active-banner-row">
                <div>
                    <div class="sub-plan-label">{{ __tr('Active Plan') }}</div>
                    <div class="sub-plan-name">{{ $activeSubscription->plan->title ?? 'N/A' }}</div>
                    <div class="sub-plan-expires">
                        {{ __tr('Expires:') }} {{ $activeSubscription->expires_at?->format('M d, Y') }}
                        ({{ $activeSubscription->expires_at?->diffForHumans() }})
                    </div>
                </div>
                <div class="sub-limits-col">
                    <div class="sub-limits-label">{{ __tr('IPTV Details') }}</div>
                    <div class="sub-limits-detail">
                        <i class="fas fa-plug"></i> {{ $activeSubscription->plan->max_connections ?? 1 }} {{ __tr('Connection(s)') }}<br>
                        <i class="fas fa-film"></i> {{ $activeSubscription->plan->streaming_quality ?? 'HD' }}<br>
                        @if($activeSubscription->plan->catchup_days)
                        <i class="fas fa-history"></i> {{ $activeSubscription->plan->catchup_days }}d {{ __tr('Catch-up') }}<br>
                        @endif
                        @if($activeSubscription->plan->dvr_enabled)
                        <i class="fas fa-record-vinyl"></i> {{ __tr('DVR Enabled') }}<br>
                        @endif
                    </div>
                </div>
            </div>
            @if($activeSubscription->iptv_username || $activeSubscription->iptv_mac)
            <div class="mt-3 pt-3 sub-banner-divider">
                <div class="row">
                    @if($activeSubscription->iptv_device_type === 'mag')
                    <div class="col-md-6 mb-2">
                        <small class="text-uppercase sub-banner-meta">MAC Address</small><br>
                        <code class="sub-banner-code">{{ $activeSubscription->iptv_mac }}</code>
                    </div>
                    @else
                    <div class="col-md-6 mb-2">
                        <small class="text-uppercase sub-banner-meta">Username</small><br>
                        <code class="sub-banner-code">{{ $activeSubscription->iptv_username }}</code>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-uppercase sub-banner-meta">Password</small><br>
                        <code class="sub-banner-code">{{ $activeSubscription->iptv_password }}</code>
                    </div>
                    @endif
                </div>
                <a href="{{ route('member.setup.guide') }}" class="cmn-btn cmn-btn-sm mt-2">
                    <i class="fas fa-tv mr-1"></i> {{ __tr('Setup Guide') }}
                </a>
            </div>
            @endif
        </div>
    @else
        <div class="sub-warning-banner">
            <i class="fas fa-exclamation-triangle sub-warning-icon"></i>
            <div>
                <strong class="sub-warning-title">{{ __tr('No active subscription') }}</strong>
                <p class="sub-warning-text">
                    <a href="{{ route('pricing.plans') }}" class="sub-warning-link">{{ __tr('Choose a plan') }}</a>
                    {{ __tr('to unlock posting limits and premium features.') }}
                </p>
            </div>
        </div>
    @endif

    {{-- Subscription History Table --}}
    <div class="dashboard-card p-0">
        <div class="card-header">
            <h3 class="card-title">{{ __tr('Subscription History') }}</h3>
            <span class="sub-history-count">{{ $subscriptions->total() }} {{ __tr('total') }}</span>
        </div>

        @if ($subscriptions->count())
            <div class="sub-table-wrap">
                <table class="sub-table">
                    <thead>
                        <tr>
                            <th>{{ __tr('Plan') }}</th>
                            <th>{{ __tr('Transaction ID') }}</th>
                            <th>{{ __tr('Amount') }}</th>
                            <th>{{ __tr('Method') }}</th>
                            <th>{{ __tr('Status') }}</th>
                            <th>{{ __tr('Start') }}</th>
                            <th>{{ __tr('Expires') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $sub)
                            <tr>
                                <td class="td-plan">{{ $sub->plan->title ?? 'N/A' }}</td>
                                <td class="td-txn">{{ $sub->transaction_id }}</td>
                                <td class="td-amount">
                                    @if ($sub->amount > 0)
                                        {{ format_amount($sub->amount) }}
                                    @else
                                        <span class="sub-free-badge">{{ __tr('Free') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sub->payment_method === 'stripe')
                                        <span class="sub-method-badge sub-method-stripe">Stripe</span>
                                    @elseif ($sub->payment_method === 'credits')
                                        <span class="sub-method-badge sub-method-credits">Credits</span>
                                    @else
                                        <span class="sub-method-badge sub-method-trial">{{ ucfirst($sub->payment_method ?? 'Trial') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="sub-status-badge sub-status-{{ $sub->status }}">
                                        {{ $sub->status }}
                                        @if ($sub->status === 'active' && $sub->expires_at?->isPast())
                                            ({{ __tr('expired') }})
                                        @endif
                                    </span>
                                </td>
                                <td class="td-date">{{ $sub->starts_at?->format('M d, Y') ?? '—' }}</td>
                                <td class="td-date">{{ $sub->expires_at?->format('M d, Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($subscriptions->hasPages())
                <div class="pagination-wrapper">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        @else
            <div class="empty-listings">
                <div class="icon"><i class="fas fa-credit-card"></i></div>
                <h3>{{ __tr('No subscriptions yet') }}</h3>
                <p>{{ __tr('Subscribe to a plan to start streaming IPTV.') }}</p>
                <a href="{{ route('pricing.plans') }}" class="cmn-btn">
                    <i class="fas fa-tag"></i> {{ __tr('View Plans') }}
                </a>
            </div>
        @endif
    </div>
@endsection
