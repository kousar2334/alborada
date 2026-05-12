@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>Reseller Dashboard - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="dash-page-title">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="dash-page-subtitle">
                @if(auth()->user()->company_name){{ auth()->user()->company_name }} &mdash; @endif
                {{ __tr('Reseller account overview') }}
            </p>
        </div>
        <a href="{{ route('reseller.credits') }}" style="background:rgba(0,212,106,.15);border:1px solid rgba(0,212,106,.3);color:#00d46a;padding:8px 18px;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;">
            <i class="fas fa-coins"></i> {{ number_format($reseller->credits, 0) }} {{ __tr('Credits') }}
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
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
                <div class="stat-icon" style="background:rgba(0,212,106,.12);color:#00d46a;"><i class="fas fa-satellite-dish"></i></div>
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

        <div class="stat-card" style="border:1px solid rgba(0,212,106,.2);">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Credit Balance') }}</span>
                <div class="stat-icon" style="background:rgba(0,212,106,.12);color:#00d46a;"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="stat-value" style="color:#00d46a;">{{ number_format($reseller->credits, 0) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-coins"></i>
                <a href="{{ route('reseller.credits') }}" style="color:#00d46a;text-decoration:none;font-size:.75rem;">{{ __tr('Manage wallet') }}</a>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid" style="margin-bottom:24px;">
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
                                <div class="activity-icon" style="background:rgba(0,212,106,.12);color:#00d46a;">
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
                        <a href="{{ route('reseller.clients') }}" class="link-primary-bold">{{ __tr('Add your first client!') }}</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions + Expiring Soon --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __tr('Quick Actions') }}</h3>
                </div>
                <div class="quick-actions">
                    <a href="{{ route('reseller.clients') }}" class="action-btn" style="background:#00d46a;color:#000;">
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

            @if($expiringClients->count())
                <div class="dashboard-card" style="border:1px solid rgba(249,115,22,.2);">
                    <div class="card-header">
                        <h3 class="card-title" style="color:#f97316;"><i class="fas fa-hourglass-half" style="margin-right:6px;"></i>{{ __tr('Expiring in 7 Days') }}</h3>
                        <span style="background:rgba(249,115,22,.15);color:#f97316;padding:2px 10px;border-radius:10px;font-size:.72rem;font-weight:700;">{{ $expiringClients->count() }}</span>
                    </div>
                    <div style="padding:12px 16px;">
                        @foreach($expiringClients->take(5) as $expSub)
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);">
                                <div>
                                    <div style="font-size:.85rem;font-weight:600;">{{ $expSub->user?->name ?? '—' }}</div>
                                    <div style="font-size:.75rem;color:var(--muted);">{{ $expSub->user?->email }}</div>
                                </div>
                                <div style="font-size:.75rem;color:#f97316;font-weight:600;text-align:right;">
                                    {{ $expSub->expires_at?->format('M d') }}<br>
                                    <span style="color:var(--muted);font-weight:400;">{{ $expSub->expires_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
