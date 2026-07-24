@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Dashboard') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')

    {{-- ── Welcome Header ── --}}
    <div class="dashboard-header">
        <div>
            <h1 class="dash-page-title">{{ __tr('Welcome back') }}, {{ auth()->user()->name }}!</h1>
            <p class="dash-page-subtitle">
                {{ __tr('Your Moissanite Visions dashboard — manage your streaming service below.') }}
            </p>
        </div>
        @if ($activeSubscription)
            <span class="dash-status-active">
                <i class="fas fa-circle-check"></i> {{ __tr('Active') }}
            </span>
        @else
            <a href="{{ route('pricing.plans') }}" class="dash-subscribe-btn">
                <i class="fas fa-plus"></i> {{ __tr('Subscribe Now') }}
            </a>
        @endif
    </div>

    {{-- ── Stats Grid ── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Subscription') }}</span>
                <div class="stat-icon red"><i class="fas fa-satellite-dish"></i></div>
            </div>
            <div class="stat-value stat-value-sm">{{ $activeSubscription?->plan?->title ?? __tr('None') }}</div>
            <div class="stat-change {{ $activeSubscription ? 'positive' : '' }}">
                <i class="fas fa-{{ $activeSubscription ? 'circle-check' : 'circle-xmark' }}"></i>
                {{ $activeSubscription ? __tr('Active plan') : __tr('No active plan') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Days Remaining') }}</span>
                <div class="stat-icon emerald"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="stat-value">{{ $activeSubscription ? $daysRemaining : '—' }}</div>
            <div class="stat-change {{ $daysRemaining <= 7 && $activeSubscription ? 'negative' : 'positive' }}">
                @if ($activeSubscription)
                    <i class="fas fa-clock"></i>
                    {{ $activeSubscription->expires_at?->format('M d, Y') }}
                @else
                    <i class="fas fa-clock"></i> {{ __tr('N/A') }}
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Connections') }}</span>
                <div class="stat-icon blue"><i class="fas fa-plug"></i></div>
            </div>
            <div class="stat-value">{{ $activeSubscription?->plan?->max_connections ?? '—' }}</div>
            <div class="stat-change">
                <i class="fas fa-devices"></i> {{ __tr('simultaneous devices') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Quality') }}</span>
                <div class="stat-icon indigo"><i class="fas fa-film"></i></div>
            </div>
            <div class="stat-value stat-value-md">{{ $activeSubscription?->plan?->streaming_quality ?? '—' }}</div>
            <div class="stat-change positive">
                @if ($activeSubscription?->plan?->dvr_enabled)
                    <i class="fas fa-record-vinyl"></i> {{ __tr('DVR Enabled') }}
                @else
                    <i class="fas fa-play"></i> {{ __tr('Streaming') }}
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Open Tickets') }}</span>
                <div class="stat-icon orange"><i class="fas fa-headset"></i></div>
            </div>
            <div class="stat-value">{{ $openTickets }}</div>
            <div class="stat-change">
                <i class="fas fa-ticket-alt"></i> {{ __tr('support requests') }}
            </div>
        </div>
    </div>

    {{-- ── Credentials Card ── --}}
    @if ($activeSubscription && ($activeSubscription->iptv_username || $activeSubscription->iptv_mac))
        @php($isMag = $activeSubscription->iptv_device_type === 'mag')
        <div class="dashboard-card cred-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-key card-title-icon"></i>{{ __tr('Your IPTV Connection Details') }}
                </h3>
                <span class="dash-live-badge">
                    <i class="fas fa-circle"></i> {{ __tr('LIVE') }}
                </span>
            </div>
            <div class="cred-card-body">
                <div class="cred-grid">
                    @if ($isMag)
                        <div class="cred-field">
                            <div class="cred-label">{{ __tr('MAC Address') }}</div>
                            <div class="cred-value-row">
                                <code id="cred-mac" class="cred-code">{{ $activeSubscription->iptv_mac }}</code>
                                <button onclick="copyToClipboard('cred-mac',this)" class="copy-btn"
                                    title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                        @if ($activeSubscription->iptv_m3u_url)
                            <div class="cred-field">
                                <div class="cred-label">{{ __tr('Portal URL') }}</div>
                                <div class="cred-value-row">
                                    <code id="cred-portal"
                                        class="cred-code cred-code-xs">{{ $activeSubscription->iptv_m3u_url }}</code>
                                    <button onclick="copyToClipboard('cred-portal',this)" class="copy-btn"
                                        title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="cred-field">
                            <div class="cred-label">{{ __tr('Username') }}</div>
                            <div class="cred-value-row">
                                <code id="cred-user" class="cred-code">{{ $activeSubscription->iptv_username }}</code>
                                <button onclick="copyToClipboard('cred-user',this)" class="copy-btn"
                                    title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                        <div class="cred-field">
                            <div class="cred-label">{{ __tr('Password') }}</div>
                            <div class="cred-value-row">
                                <code id="cred-pass" class="cred-code">{{ $activeSubscription->iptv_password }}</code>
                                <button onclick="copyToClipboard('cred-pass',this)" class="copy-btn"
                                    title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                        @if ($activeSubscription->iptv_m3u_url)
                            <div class="cred-field">
                                <div class="cred-label">{{ __tr('M3U Playlist URL') }}</div>
                                <div class="cred-value-row">
                                    <code id="cred-m3u"
                                        class="cred-code cred-code-xs">{{ $activeSubscription->iptv_m3u_url }}</code>
                                    <button onclick="copyToClipboard('cred-m3u',this)" class="copy-btn"
                                        title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                @if ($activeSubscription?->plan?->catchup_days)
                    <div class="cred-catchup-note">
                        <i class="fas fa-history icon-green"></i>
                        {{ $activeSubscription->plan->catchup_days }}-{{ __tr('day catch-up TV available') }}
                    </div>
                @endif
                <div class="cred-actions">
                    <a href="{{ route('member.setup.guide') }}" class="cmn-btn cmn-btn-sm">
                        <i class="fas fa-tv"></i> {{ __tr('Setup Guide') }}
                    </a>
                    <a href="{{ route('member.download.app') }}" class="cmn-btn cmn-btn-sm cmn-btn-ghost">
                        <i class="fas fa-download"></i> {{ __tr('Download App') }}
                    </a>
                </div>
            </div>
        </div>
    @elseif(!$activeSubscription)
        <div class="sub-warning-banner">
            <i class="fas fa-satellite-dish sub-warning-icon"></i>
            <div>
                <strong class="sub-warning-title">{{ __tr('No active subscription') }}</strong>
                <p class="sub-warning-text">
                    <a href="{{ route('pricing.plans') }}" class="sub-warning-link">{{ __tr('Choose a plan') }}</a>
                    {{ __tr('to get your IPTV credentials and start streaming.') }}
                </p>
            </div>
        </div>
    @endif

    {{-- ── Quick Actions + Recent Invoices ── --}}
    <div class="content-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Quick Actions') }}</h3>
            </div>
            <div class="quick-actions">
                <a href="{{ route('member.tickets.create') }}?type=buffering" class="action-btn danger">
                    <i class="fas fa-wifi"></i> {{ __tr('Report Buffering') }}
                </a>
                <a href="{{ route('member.tickets.create') }}" class="action-btn secondary">
                    <i class="fas fa-ticket-alt"></i> {{ __tr('Open Ticket') }}
                    @if ($openTickets > 0)
                        <span class="msg-unread-badge">{{ $openTickets }}</span>
                    @endif
                </a>
                <a href="{{ route('member.subscriptions') }}" class="action-btn secondary">
                    <i class="fas fa-receipt"></i> {{ __tr('Billing') }}
                </a>
                <a href="{{ route('pricing.plans') }}" class="action-btn secondary">
                    <i class="fas fa-arrow-up"></i> {{ __tr('Upgrade Plan') }}
                </a>
                <a href="{{ route('member.download.app') }}" class="action-btn secondary">
                    <i class="fas fa-download"></i> {{ __tr('Download App') }}
                </a>
                <a href="{{ route('member.setup.guide') }}" class="action-btn secondary">
                    <i class="fas fa-tv"></i> {{ __tr('Setup Guide') }}
                </a>
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Recent Invoices') }}</h3>
                <a href="{{ route('member.subscriptions') }}" class="view-all">{{ __tr('View All') }} →</a>
            </div>
            <div class="card-body">
                @if ($recentInvoices->count())
                    <div class="activity-list">
                        @foreach ($recentInvoices as $inv)
                            <div class="activity-item">
                                <div class="activity-icon emerald">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>{{ $inv->invoice_number }}</h4>
                                    <p>{{ format_amount($inv->total_amount) }} &mdash; {{ ucfirst($inv->status) }}</p>
                                    <div class="activity-time">
                                        <i class="fas fa-clock icon-xxs"></i>
                                        {{ $inv->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="activity-badge-wrap">
                                    <span class="badge-status {{ $inv->status === 'paid' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-activity">
                        <i class="fas fa-file-invoice empty-activity-icon"></i>
                        {{ __tr('No invoices yet.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Featured Content ── --}}
    @if ($featuredContent->count())
        <div class="dashboard-card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-film card-title-icon"></i>{{ __tr("What's Streaming") }}
                </h3>
                <span class="card-subtitle">{{ __tr('Movies, Series & Live Events') }}</span>
            </div>
            <div class="featured-scroll">
                @foreach ($featuredContent as $item)
                    <div class="content-item">
                        @if ($item->badge_text)
                            <span class="content-badge">{{ $item->badge_text }}</span>
                        @endif
                        @if ($item->thumbnail)
                            <img src="{{ asset(getFilePath($item->thumbnail)) }}" alt="{{ $item->title }}"
                                class="content-thumb">
                        @else
                            <div class="content-thumb-placeholder">
                                <i class="fas {{ $item->type_icon }} content-thumb-icon"></i>
                            </div>
                        @endif
                        <div class="content-body">
                            <div class="content-type">{{ $item->type_label }}</div>
                            <div class="content-title">{{ $item->title }}</div>
                            @if ($item->genre)
                                <div class="content-genre">{{ $item->genre }}</div>
                            @endif
                            @if ($item->youtube_embed_id)
                                <a href="https://www.youtube.com/watch?v={{ $item->youtube_embed_id }}" target="_blank"
                                    rel="noopener" class="content-preview-link">
                                    <i class="fas fa-play"></i> {{ __tr('Preview') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Downloader Codes ── --}}
    @if ($downloaderCodes->count())
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-download card-title-icon-green"></i>{{ __tr('Download the App') }}
                </h3>
                <a href="{{ route('member.download.app') }}" class="view-all">{{ __tr('All Devices') }} →</a>
            </div>
            <div class="downloader-grid">
                @foreach ($downloaderCodes->take(4) as $code)
                    <div class="downloader-item">
                        <div class="downloader-icon">
                            <i class="fas {{ \App\Models\AppDownloaderCode::deviceTypeIcon($code->device_type) }}"></i>
                        </div>
                        <div>
                            <div class="downloader-label">{{ $code->label }}</div>
                            <div class="downloader-code-val">{{ $code->code }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection

@section('dashboard-js')
    <script>
        function copyToClipboard(elId, btn) {
            const text = document.getElementById(elId).textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.style.color = '#00d46a';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i>';
                    btn.style.color = '';
                }, 1500);
            });
        }
    </script>
@endsection
