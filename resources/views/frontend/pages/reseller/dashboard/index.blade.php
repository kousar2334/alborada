@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>Reseller Dashboard - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <div>
            <h1 class="dash-page-title">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="dash-page-subtitle">
                @if (auth()->user()->company_name)
                    {{ auth()->user()->company_name }} &mdash;
                @endif
                {{ __tr('Reseller account overview') }}
            </p>
        </div>
        <a href="{{ route('reseller.credits') }}" class="reseller-credits-pill">
            <i class="fas fa-coins"></i> {{ number_format($reseller->credits, 0) }} {{ __tr('Credits') }}
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Total Clients') }}</span>
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-value">{{ $totalClients }}</div>
            <div class="stat-change positive">
                <i class="fas fa-circle-check"></i> {{ $activeClients }} {{ __tr('active') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Active Subscriptions') }}</span>
                <div class="stat-icon emerald"><i class="fas fa-satellite-dish"></i></div>
            </div>
            <div class="stat-value">{{ $activeSubscriptions }}</div>
            <div class="stat-change positive">
                <i class="fas fa-signal"></i> {{ __tr('live lines') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Total Revenue') }}</span>
                <div class="stat-icon cyan"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <div class="stat-value">{{ get_setting('currency_symbol', '$') }}{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-change">
                <i class="fas fa-chart-line"></i> {{ __tr('from active plans') }}
            </div>
        </div>

        <div class="stat-card stat-card-credits">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Credit Balance') }}</span>
                <div class="stat-icon emerald"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="stat-value stat-value-green">{{ number_format($reseller->credits, 0) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-coins"></i>
                <a href="{{ route('reseller.credits') }}" class="link-green-sm">{{ __tr('Manage wallet') }}</a>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid mb-4">
        {{-- Recent Clients --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Recent Clients') }}</h3>
                <a href="{{ route('reseller.clients') }}" class="view-all">{{ __tr('View All') }} →</a>
            </div>
            <div class="card-body">
                @if ($recentClients->count())
                    <div class="activity-list">
                        @foreach ($recentClients as $client)
                            @php $sub = $client->subscriptions->first(); @endphp
                            <div class="activity-item">
                                <div class="activity-icon emerald">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>{{ $client->name }}</h4>
                                    <p>{{ $client->email }}</p>
                                    <div class="activity-time">
                                        <i class="fas fa-clock icon-xxs"></i>
                                        {{ $client->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="activity-badge-wrap">
                                    <span class="badge-status {{ $sub ? 'active' : 'inactive' }}">
                                        {{ $sub ? __tr('Active') : __tr('No sub') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-activity">
                        <i class="fas fa-users empty-activity-icon"></i>
                        {{ __tr('No clients yet.') }}
                        <a href="{{ route('reseller.clients') }}"
                            class="link-primary-bold">{{ __tr('Add your first client!') }}</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions + Expiring Soon --}}
        <div class="content-grid-col">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __tr('Quick Actions') }}</h3>
                </div>
                <div class="quick-actions">
                    <a href="{{ route('reseller.clients') }}" class="action-btn primary">
                        <i class="fas fa-user-plus"></i> {{ __tr('Add Client') }}
                    </a>
                    <a href="{{ route('reseller.credits') }}" class="action-btn secondary">
                        <i class="fas fa-coins"></i> {{ __tr('Buy Credits') }}
                    </a>
                    <a href="{{ route('reseller.clients') }}" class="action-btn secondary">
                        <i class="fas fa-users"></i> {{ __tr('All Clients') }}
                    </a>
                    <a href="{{ route('reseller.account') }}" class="action-btn secondary">
                        <i class="fas fa-user-gear"></i> {{ __tr('My Profile') }}
                    </a>
                    <a href="{{ route('reseller.api.keys') }}" class="action-btn secondary">
                        <i class="fas fa-key"></i> {{ __tr('API Keys') }}
                    </a>
                    <a href="{{ route('contact') }}" class="action-btn secondary">
                        <i class="fas fa-headset"></i> {{ __tr('Support') }}
                    </a>
                </div>
            </div>

            @if ($expiringClients->count())
                <div class="dashboard-card expiring-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-hourglass-half"></i>{{ __tr('Expiring in 7 Days') }}
                        </h3>
                        <span class="expiring-count-badge">{{ $expiringClients->count() }}</span>
                    </div>
                    <div class="expiring-body">
                        @foreach ($expiringClients->take(5) as $expSub)
                            <div class="expiring-item">
                                <div>
                                    <div class="expiring-item-name">{{ $expSub->user?->name ?? '—' }}</div>
                                    <div class="expiring-item-email">{{ $expSub->user?->email }}</div>
                                </div>
                                <div class="expiring-item-date">
                                    {{ $expSub->expires_at?->format('M d') }}<br>
                                    <span class="expiring-item-relative">{{ $expSub->expires_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
