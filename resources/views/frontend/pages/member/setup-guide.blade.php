@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Setup Guide') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('dashboard-content')

    <div class="dashboard-header">
        <div>
            <h1 class="dash-page-title"><i class="fas fa-book-open me-2"></i>{{ __tr('Setup Guide') }}</h1>
            <p class="dash-page-subtitle">{{ __tr('Connect your IPTV subscription to your preferred app.') }}</p>
        </div>
    </div>

    @if (!$subscription)
        <div class="dashboard-card sg-no-sub">
            <i class="fas fa-circle-exclamation sg-no-sub-icon"></i>
            <div>
                <p class="mb-2 fw-semibold">{{ __tr("You don't have an active subscription.") }}</p>
                <a href="{{ route('pricing.plans') }}" class="cmn-btn" style="font-size:.85rem;padding:7px 20px;">
                    {{ __tr('View Plans') }}
                </a>
            </div>
        </div>
    @else
        {{-- Credentials Card --}}
        <div class="dashboard-card sg-card mb-4">
            <div class="sg-card-header">
                <i class="fas fa-key"></i>
                <span>{{ __tr('Your IPTV Credentials') }}</span>
            </div>

            <div class="sg-cred-grid">
                <div class="sg-cred-field">
                    <label class="sg-field-label">{{ __tr('Username') }}</label>
                    <div class="sg-cred-row">
                        <span id="cred-username" class="sg-cred-value">{{ $credentials['username'] ?? '' }}</span>
                        <button class="sg-copy-btn" onclick="copyText('cred-username')" title="{{ __tr('Copy') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="sg-cred-field">
                    <label class="sg-field-label">{{ __tr('Password') }}</label>
                    <div class="sg-cred-row">
                        <span id="cred-password" class="sg-cred-value">{{ $credentials['password'] ?? '' }}</span>
                        <button class="sg-copy-btn" onclick="copyText('cred-password')" title="{{ __tr('Copy') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                @if (!empty($credentials['m3u_url']))
                    <div class="sg-cred-field sg-cred-full">
                        <label class="sg-field-label">{{ __tr('M3U Playlist URL') }}</label>
                        <div class="sg-cred-row">
                            <span id="cred-m3u" class="sg-cred-value sg-cred-wrap">{{ $credentials['m3u_url'] }}</span>
                            <button class="sg-copy-btn" onclick="copyText('cred-m3u')" title="{{ __tr('Copy') }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="sg-cred-field sg-cred-full">
                        <label class="sg-field-label">{{ __tr('EPG / Guide URL') }}</label>
                        <div class="sg-cred-row">
                            <span id="cred-epg" class="sg-cred-value sg-cred-wrap">{{ $credentials['epg_url'] }}</span>
                            <button class="sg-copy-btn" onclick="copyText('cred-epg')" title="{{ __tr('Copy') }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="sg-plan-bar">
                <span class="sg-plan-chip">
                    <i class="fas fa-box"></i> {{ $subscription->plan->title ?? 'N/A' }}
                </span>
                <span class="sg-plan-chip">
                    <i class="fas fa-plug"></i> {{ $subscription->plan->max_connections ?? 1 }}
                    {{ __tr('Connection(s)') }}
                </span>
                <span class="sg-plan-chip">
                    <i class="fas fa-film"></i> {{ $subscription->plan->streaming_quality ?? 'HD' }}
                </span>
                <span class="sg-plan-chip">
                    <i class="fas fa-calendar-xmark"></i> {{ __tr('Expires') }}:
                    {{ $subscription->expires_at?->format('M d, Y') ?? 'N/A' }}
                </span>
            </div>
        </div>

        {{-- App Setup Tabs --}}
        <div class="dashboard-card sg-card mb-4">
            <div class="sg-card-header">
                <i class="fas fa-mobile-screen"></i>
                <span>{{ __tr('App Setup Instructions') }}</span>
            </div>

            <div class="sg-tabs-nav">
                <button class="sg-tab-btn active" data-target="smarters">IPTV Smarters</button>
                <button class="sg-tab-btn" data-target="tivimate">TiviMate</button>
                <button class="sg-tab-btn" data-target="xciptv">XCIPTV</button>
                <button class="sg-tab-btn" data-target="vlc">VLC / Other</button>
            </div>

            <div class="sg-tab-body">

                <div class="sg-pane active" id="sgpane-smarters">
                    <h6 class="sg-pane-title">
                        <i class="fas fa-tv"></i> IPTV Smarters Pro
                    </h6>
                    <ol class="sg-steps">
                        <li>Download <strong>IPTV Smarters Pro</strong> from the App Store / Google Play / Amazon.</li>
                        <li>Open the app and tap <strong>"Add User"</strong>.</li>
                        <li>Choose <strong>"Login with Xtream Codes API"</strong>.</li>
                        <li>Enter the following credentials:
                            <div class="sg-inline-creds">
                                <div class="sg-ic-row">
                                    <span>Username</span><samp>{{ $credentials['username'] ?? '' }}</samp>
                                </div>
                                <div class="sg-ic-row">
                                    <span>Password</span><samp>{{ $credentials['password'] ?? '' }}</samp>
                                </div>
                                <div class="sg-ic-row">
                                    <span>URL</span><samp>{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</samp>
                                </div>
                            </div>
                        </li>
                        <li>Tap <strong>Add User</strong> and wait for the channel list to load.</li>
                    </ol>
                </div>

                <div class="sg-pane" id="sgpane-tivimate">
                    <h6 class="sg-pane-title">
                        <i class="fas fa-tv"></i> TiviMate
                        <span class="sg-platform-tag">Android / Fire TV</span>
                    </h6>
                    <ol class="sg-steps">
                        <li>Install <strong>TiviMate</strong> from the Fire TV store or sideload the APK.</li>
                        <li>Open TiviMate and select <strong>"Add Playlist"</strong>.</li>
                        <li>Choose <strong>"Xtream Codes"</strong>.</li>
                        <li>Enter:
                            <div class="sg-inline-creds">
                                <div class="sg-ic-row"><span>Server
                                        URL</span><samp>{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</samp></div>
                                <div class="sg-ic-row">
                                    <span>Username</span><samp>{{ $credentials['username'] ?? '' }}</samp>
                                </div>
                                <div class="sg-ic-row">
                                    <span>Password</span><samp>{{ $credentials['password'] ?? '' }}</samp>
                                </div>
                            </div>
                        </li>
                        <li>Tap <strong>Next</strong> to import.</li>
                    </ol>
                    <p class="sg-tip"><i class="fas fa-lightbulb"></i> TiviMate Companion (premium) is recommended for EPG
                        and recordings.</p>
                </div>

                <div class="sg-pane" id="sgpane-xciptv">
                    <h6 class="sg-pane-title">
                        <i class="fas fa-tv"></i> XCIPTV Player
                    </h6>
                    <ol class="sg-steps">
                        <li>Install <strong>XCIPTV</strong> from Google Play or the official website.</li>
                        <li>Open the app and tap <strong>"+"</strong> to add an account.</li>
                        <li>Select <strong>"Xtream API"</strong>.</li>
                        <li>Fill in:
                            <div class="sg-inline-creds">
                                <div class="sg-ic-row">
                                    <span>Server</span><samp>{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</samp>
                                </div>
                                <div class="sg-ic-row">
                                    <span>Username</span><samp>{{ $credentials['username'] ?? '' }}</samp>
                                </div>
                                <div class="sg-ic-row">
                                    <span>Password</span><samp>{{ $credentials['password'] ?? '' }}</samp>
                                </div>
                            </div>
                        </li>
                        <li>Press <strong>Login</strong>. Channels will load automatically.</li>
                    </ol>
                </div>

                <div class="sg-pane" id="sgpane-vlc">
                    <h6 class="sg-pane-title">
                        <i class="fas fa-play"></i> VLC, Kodi, or any M3U Player
                    </h6>
                    <ol class="sg-steps">
                        <li>Copy your M3U Playlist URL:
                            <samp class="sg-url-block">{{ $credentials['m3u_url'] ?? '' }}</samp>
                        </li>
                        <li>In <strong>VLC</strong>: go to <em>Media → Open Network Stream</em>, paste the URL and click
                            Play.</li>
                        <li>In <strong>Kodi</strong>: install the <em>PVR IPTV Simple Client</em> addon and paste the M3U
                            URL in settings.</li>
                        <li>For EPG, use:
                            <samp class="sg-url-block">{{ $credentials['epg_url'] ?? '' }}</samp>
                        </li>
                    </ol>
                </div>

            </div>
        </div>

        <div class="sg-help-bar">
            <i class="fas fa-headset"></i>
            {{ __tr('Need help?') }}
            <a href="{{ route('member.tickets.create') }}">{{ __tr('Open a support ticket') }}</a>
            {{ __tr('and our team will assist you within 24 hours.') }}
        </div>
    @endif

@endsection
@section('dashboard-js')
    <script>
        function copyText(id) {
            const el = document.getElementById(id);
            navigator.clipboard.writeText(el.innerText.trim()).then(() => {
                toastr && toastr.success('Copied!');
            });
        }

        document.querySelectorAll('.sg-tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.sg-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.sg-pane').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('sgpane-' + btn.dataset.target).classList.add('active');
            });
        });
    </script>
@endsection
