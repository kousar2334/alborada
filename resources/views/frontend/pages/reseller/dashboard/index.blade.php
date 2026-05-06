@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>Reseller Dashboard - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="dash-page-subtitle">
            @if(auth()->user()->company_name)
                {{ auth()->user()->company_name }} &mdash;
            @endif
            Here's an overview of your reseller account.
        </p>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Clients</span>
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-value">{{ $totalClients }}</div>
            <div class="stat-change positive">
                <i class="fas fa-circle-check"></i> {{ $activeClients }} active
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Active Subscriptions</span>
                <div class="stat-icon green"><i class="fas fa-satellite-dish"></i></div>
            </div>
            <div class="stat-value">{{ $activeSubscriptions }}</div>
            <div class="stat-change positive">
                <i class="fas fa-signal"></i> live lines
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Revenue</span>
                <div class="stat-icon cyan"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <div class="stat-value">{{ get_setting('currency_symbol', '$') }}{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-change">
                <i class="fas fa-chart-line"></i> from active plans
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid">
        {{-- Recent Clients --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Clients</h3>
                <a href="{{ route('reseller.clients') }}" class="view-all">View All →</a>
            </div>
            <div class="card-body">
                @if ($recentClients->count())
                    <div class="activity-list">
                        @foreach ($recentClients as $client)
                            @php $sub = $client->subscriptions->first(); @endphp
                            <div class="activity-item">
                                <div class="activity-icon" style="background:rgba(0,212,106,0.12);color:#00d46a;">
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
                                    @if($sub)
                                        <span class="badge-status active">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge-status inactive">
                                            No sub
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-activity">
                        <i class="fas fa-users empty-activity-icon"></i>
                        No clients yet.
                        <a href="{{ route('reseller.clients') }}" class="link-primary-bold">Add your first client!</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="quick-actions">
                <a href="{{ route('reseller.clients') }}" class="action-btn" style="background:var(--green);color:#000;">
                    <i class="fas fa-user-plus"></i> Add Client
                </a>
                <a href="{{ route('reseller.clients') }}" class="action-btn secondary">
                    <i class="fas fa-users"></i> All Clients
                </a>
                <a href="{{ route('reseller.account') }}" class="action-btn secondary">
                    <i class="fas fa-user-gear"></i> My Profile
                </a>
                <a href="{{ route('contact') }}" class="action-btn secondary">
                    <i class="fas fa-headset"></i> Support
                </a>
            </div>

            <div style="margin-top:24px;padding:16px;background:rgba(0,212,106,0.06);border:1px solid rgba(0,212,106,0.15);border-radius:12px;">
                <div style="font-size:0.75rem;color:#00d46a;font-weight:600;letter-spacing:.5px;margin-bottom:8px;">RESELLER API</div>
                <code style="font-size:0.72rem;color:var(--muted);display:block;word-break:break-all;">GET /api/reseller/lines</code>
                <p style="font-size:0.75rem;color:var(--muted-2);margin-top:6px;margin-bottom:0;">Contact support to enable API access for your account.</p>
            </div>
        </div>
    </div>

@endsection
