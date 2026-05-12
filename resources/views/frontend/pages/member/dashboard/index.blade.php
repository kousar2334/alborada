@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Dashboard') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')

    {{-- ── Welcome Header ── --}}
    <div class="dashboard-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="dash-page-title">{{ __tr('Welcome back') }}, {{ auth()->user()->name }}!</h1>
            <p class="dash-page-subtitle">{{ __tr('Your Alborada Box dashboard — manage your streaming service below.') }}</p>
        </div>
        @if($activeSubscription)
            <span style="background:rgba(0,212,106,0.12);color:#00d46a;padding:6px 16px;border-radius:20px;font-size:.8rem;font-weight:600;border:1px solid rgba(0,212,106,0.3);">
                <i class="fas fa-circle-check"></i> {{ __tr('Active') }}
            </span>
        @else
            <a href="{{ route('pricing.plans') }}" style="background:var(--primary-color);color:#fff;padding:8px 20px;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;">
                <i class="fas fa-plus"></i> {{ __tr('Subscribe Now') }}
            </a>
        @endif
    </div>

    {{-- ── IPTV Stats Grid ── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Subscription') }}</span>
                <div class="stat-icon" style="background:rgba(204,0,0,.12);color:#cc0000;"><i class="fas fa-satellite-dish"></i></div>
            </div>
            <div class="stat-value" style="font-size:1.1rem;">{{ $activeSubscription?->plan?->title ?? __tr('None') }}</div>
            <div class="stat-change {{ $activeSubscription ? 'positive' : '' }}">
                <i class="fas fa-{{ $activeSubscription ? 'circle-check' : 'circle-xmark' }}"></i>
                {{ $activeSubscription ? __tr('Active plan') : __tr('No active plan') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Days Remaining') }}</span>
                <div class="stat-icon" style="background:rgba(0,212,106,.12);color:#00d46a;"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="stat-value">{{ $activeSubscription ? $daysRemaining : '—' }}</div>
            <div class="stat-change {{ $daysRemaining <= 7 && $activeSubscription ? 'negative' : 'positive' }}">
                @if($activeSubscription)
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
                <div class="stat-icon" style="background:rgba(99,91,255,.12);color:#635bff;"><i class="fas fa-film"></i></div>
            </div>
            <div class="stat-value" style="font-size:1.3rem;">{{ $activeSubscription?->plan?->streaming_quality ?? '—' }}</div>
            <div class="stat-change positive">
                @if($activeSubscription?->plan?->dvr_enabled)
                    <i class="fas fa-record-vinyl"></i> {{ __tr('DVR Enabled') }}
                @else
                    <i class="fas fa-play"></i> {{ __tr('Streaming') }}
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Open Tickets') }}</span>
                <div class="stat-icon" style="background:rgba(249,115,22,.12);color:#f97316;"><i class="fas fa-headset"></i></div>
            </div>
            <div class="stat-value">{{ $openTickets }}</div>
            <div class="stat-change">
                <i class="fas fa-ticket"></i> {{ __tr('support requests') }}
            </div>
        </div>
    </div>

    {{-- ── IPTV Credentials Card ── --}}
    @if($activeSubscription && $activeSubscription->xtream_username)
        <div class="dashboard-card" style="margin-bottom:24px;background:linear-gradient(135deg,#0a0a0a 0%,#1a0000 100%);border:1px solid rgba(204,0,0,.25);">
            <div class="card-header" style="border-bottom:1px solid rgba(204,0,0,.2);">
                <h3 class="card-title" style="color:#fff;"><i class="fas fa-key" style="color:#cc0000;margin-right:8px;"></i>{{ __tr('Your IPTV Connection Details') }}</h3>
                <span style="font-size:.75rem;color:#00d46a;font-weight:600;">
                    <i class="fas fa-circle" style="font-size:.5rem;"></i> {{ __tr('LIVE') }}
                </span>
            </div>
            <div style="padding:20px;">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:16px;">
                    <div class="cred-field">
                        <div class="cred-label">{{ __tr('Username') }}</div>
                        <div class="cred-value-row">
                            <code id="cred-user" class="cred-code">{{ $activeSubscription->xtream_username }}</code>
                            <button onclick="copyToClipboard('cred-user',this)" class="copy-btn" title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="cred-field">
                        <div class="cred-label">{{ __tr('Password') }}</div>
                        <div class="cred-value-row">
                            <code id="cred-pass" class="cred-code">{{ $activeSubscription->xtream_password }}</code>
                            <button onclick="copyToClipboard('cred-pass',this)" class="copy-btn" title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    @if($xtreamBaseUrl)
                        <div class="cred-field">
                            <div class="cred-label">{{ __tr('Server URL') }}</div>
                            <div class="cred-value-row">
                                <code id="cred-url" class="cred-code">{{ $xtreamBaseUrl }}</code>
                                <button onclick="copyToClipboard('cred-url',this)" class="copy-btn" title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                        <div class="cred-field">
                            <div class="cred-label">{{ __tr('M3U Playlist URL') }}</div>
                            <div class="cred-value-row">
                                <code id="cred-m3u" class="cred-code" style="font-size:.65rem;">{{ $xtreamBaseUrl }}/get.php?username={{ $activeSubscription->xtream_username }}&password={{ $activeSubscription->xtream_password }}&type=m3u_plus</code>
                                <button onclick="copyToClipboard('cred-m3u',this)" class="copy-btn" title="{{ __tr('Copy') }}"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                    @endif
                </div>
                @if($activeSubscription?->plan?->catchup_days)
                    <div style="font-size:.78rem;color:rgba(255,255,255,.5);margin-bottom:12px;">
                        <i class="fas fa-history" style="color:#00d46a;"></i> {{ $activeSubscription->plan->catchup_days }}-{{ __tr('day catch-up TV available') }}
                    </div>
                @endif
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="{{ route('member.setup.guide') }}" class="cmn-btn cmn-btn-sm">
                        <i class="fas fa-tv"></i> {{ __tr('Setup Guide') }}
                    </a>
                    <a href="{{ route('member.download.app') }}" class="cmn-btn cmn-btn-sm" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:#fff;">
                        <i class="fas fa-download"></i> {{ __tr('Download App') }}
                    </a>
                </div>
            </div>
        </div>
    @elseif(!$activeSubscription)
        <div class="sub-warning-banner" style="margin-bottom:24px;">
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

    {{-- ── Quick Actions ── --}}
    <div class="content-grid" style="margin-bottom:24px;">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Quick Actions') }}</h3>
            </div>
            <div class="quick-actions">
                <a href="{{ route('member.support.create') }}?type=buffering" class="action-btn" style="background:rgba(204,0,0,.1);border:1px solid rgba(204,0,0,.3);color:#cc0000;">
                    <i class="fas fa-wifi"></i> {{ __tr('Report Buffering') }}
                </a>
                <a href="{{ route('member.support.create') }}" class="action-btn secondary">
                    <i class="fas fa-ticket"></i> {{ __tr('Open Ticket') }}
                    @if($openTickets > 0)
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
                @if($recentInvoices->count())
                    <div class="activity-list">
                        @foreach($recentInvoices as $inv)
                            <div class="activity-item">
                                <div class="activity-icon" style="background:rgba(0,212,106,.1);color:#00d46a;">
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

    {{-- ── Featured Content / Movie Previews ── --}}
    @if($featuredContent->count())
        <div class="dashboard-card" style="margin-bottom:24px;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-film" style="color:#cc0000;margin-right:8px;"></i>{{ __tr("What's Streaming") }}</h3>
                <span style="font-size:.75rem;color:var(--muted);">{{ __tr('Movies, Series & Live Events') }}</span>
            </div>
            <div style="display:flex;gap:16px;overflow-x:auto;padding:16px;scrollbar-width:thin;scrollbar-color:rgba(204,0,0,.4) transparent;">
                @foreach($featuredContent as $item)
                    <div style="min-width:180px;max-width:180px;flex-shrink:0;border-radius:12px;overflow:hidden;background:#111;border:1px solid rgba(255,255,255,.08);position:relative;">
                        @if($item->badge_text)
                            <span style="position:absolute;top:8px;left:8px;background:#cc0000;color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:4px;z-index:2;">{{ $item->badge_text }}</span>
                        @endif
                        @if($item->thumbnail)
                            <img src="{{ asset(getFilePath($item->thumbnail)) }}" alt="{{ $item->title }}" style="width:100%;height:110px;object-fit:cover;">
                        @else
                            <div style="width:100%;height:110px;background:linear-gradient(135deg,#1a0000,#0a0a0a);display:flex;align-items:center;justify-content:center;">
                                <i class="fas {{ $item->type_icon }}" style="font-size:2rem;color:rgba(204,0,0,.5);"></i>
                            </div>
                        @endif
                        <div style="padding:10px;">
                            <div style="font-size:.7rem;color:#cc0000;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">{{ $item->type_label }}</div>
                            <div style="font-size:.85rem;font-weight:600;color:#fff;line-height:1.3;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->title }}</div>
                            @if($item->genre)
                                <div style="font-size:.7rem;color:rgba(255,255,255,.45);">{{ $item->genre }}</div>
                            @endif
                            @if($item->youtube_embed_id)
                                <a href="https://www.youtube.com/watch?v={{ $item->youtube_embed_id }}" target="_blank" rel="noopener"
                                   style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:.7rem;color:#cc0000;font-weight:600;text-decoration:none;">
                                    <i class="fas fa-play"></i> {{ __tr('Preview') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Downloader Codes (if no subscription / always show) ── --}}
    @if($downloaderCodes->count())
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-download" style="color:#00d46a;margin-right:8px;"></i>{{ __tr('Download the App') }}</h3>
                <a href="{{ route('member.download.app') }}" class="view-all">{{ __tr('All Devices') }} →</a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;padding:16px;">
                @foreach($downloaderCodes->take(4) as $code)
                    <div style="background:#111;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:14px;display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:8px;background:rgba(0,212,106,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas {{ \App\Models\AppDownloaderCode::deviceTypeIcon($code->device_type) }}" style="color:#00d46a;font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:rgba(255,255,255,.5);margin-bottom:2px;">{{ $code->label }}</div>
                            <div style="font-size:1rem;font-weight:700;color:#fff;letter-spacing:1px;">{{ $code->code }}</div>
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
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i>'; btn.style.color = ''; }, 1500);
    });
}
</script>
<style>
.cred-field { display:flex;flex-direction:column;gap:4px; }
.cred-label { font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:600; }
.cred-value-row { display:flex;align-items:center;gap:8px; }
.cred-code { background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#fff;padding:6px 10px;border-radius:6px;font-size:.8rem;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block; }
.copy-btn { background:rgba(204,0,0,.15);border:1px solid rgba(204,0,0,.3);color:#cc0000;padding:6px 10px;border-radius:6px;cursor:pointer;flex-shrink:0;transition:all .2s; }
.copy-btn:hover { background:rgba(204,0,0,.3); }
</style>
@endsection
