@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Setup Guide') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('dashboard-content')

    <div class="my-listings-header">
        <div>
            <h1>{{ __tr('Setup Guide') }}</h1>
            <p class="sg-page-sub">{{ __tr('Connect your IPTV subscription to your preferred app.') }}</p>
        </div>
    </div>

    @if (!$subscription)
        <div class="sg-empty-state">
            <div class="sg-empty-icon-wrap">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <h4 class="sg-empty-title">{{ __tr('No Active Subscription') }}</h4>
            <p class="sg-empty-sub">
                {{ __tr("You don't have an active subscription. Subscribe to a plan to get your IPTV credentials.") }}</p>
            <a href="{{ route('pricing.plans') }}" class="cmn-btn">
                <i class="fas fa-rocket"></i> {{ __tr('View Plans') }}
            </a>
        </div>
    @else
        {{-- Credentials Card --}}
        <div class="sg-cred-card">
            <div class="sg-cred-card-header">
                <div class="sg-cred-header-left">
                    <span class="sg-cred-icon"><i class="fas fa-key"></i></span>
                    <div>
                        <h3 class="sg-cred-title">{{ __tr('Your IPTV Credentials') }}</h3>
                        <p class="sg-cred-sub">{{ __tr('Use these to connect any IPTV app to your subscription.') }}</p>
                    </div>
                </div>
                <span class="sg-active-badge"><i class="fas fa-circle-dot"></i> {{ __tr('Active') }}</span>
            </div>

            <div class="sg-cred-body">
                <div class="sg-cred-grid">
                    <div class="sg-cred-field">
                        <label class="sg-field-label">{{ __tr('Username') }}</label>
                        <div class="sg-cred-box">
                            <span id="cred-username" class="sg-cred-value">{{ $credentials['username'] ?? '' }}</span>
                            <button class="sg-copy-btn" onclick="sgCopy('cred-username', this)"
                                title="{{ __tr('Copy') }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="sg-cred-field">
                        <label class="sg-field-label">{{ __tr('Password') }}</label>
                        <div class="sg-cred-box">
                            <span id="cred-password" class="sg-cred-value">{{ $credentials['password'] ?? '' }}</span>
                            <button class="sg-copy-btn" onclick="sgCopy('cred-password', this)"
                                title="{{ __tr('Copy') }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    @if (!empty($credentials['m3u_url']))
                        <div class="sg-cred-field sg-cred-full">
                            <label class="sg-field-label">{{ __tr('M3U Playlist URL') }}</label>
                            <div class="sg-cred-box">
                                <span id="cred-m3u"
                                    class="sg-cred-value sg-cred-wrap">{{ $credentials['m3u_url'] }}</span>
                                <button class="sg-copy-btn" onclick="sgCopy('cred-m3u', this)" title="{{ __tr('Copy') }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="sg-cred-field sg-cred-full">
                            <label class="sg-field-label">{{ __tr('EPG / Guide URL') }}</label>
                            <div class="sg-cred-box">
                                <span id="cred-epg"
                                    class="sg-cred-value sg-cred-wrap">{{ $credentials['epg_url'] }}</span>
                                <button class="sg-copy-btn" onclick="sgCopy('cred-epg', this)" title="{{ __tr('Copy') }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="sg-plan-bar">
                <span class="sg-plan-chip sg-chip-plan">
                    <i class="fas fa-box-open"></i> {{ $subscription->plan->title ?? 'N/A' }}
                </span>
                <span class="sg-plan-chip">
                    <i class="fas fa-plug"></i> {{ $subscription->plan->max_connections ?? 1 }}
                    {{ __tr('Connection(s)') }}
                </span>
                <span class="sg-plan-chip">
                    <i class="fas fa-film"></i> {{ $subscription->plan->streaming_quality ?? 'HD' }}
                </span>
                <span class="sg-plan-chip sg-chip-expiry">
                    <i class="fas fa-calendar-xmark"></i> {{ __tr('Expires') }}:
                    {{ $subscription->expires_at?->format('M d, Y') ?? 'N/A' }}
                </span>
            </div>
        </div>

        {{-- App Setup Tabs --}}
        <div class="sg-instructions-card">
            <div class="sg-inst-header">
                <span class="sg-inst-icon"><i class="fas fa-mobile-screen"></i></span>
                <h3 class="sg-inst-title">{{ __tr('App Setup Instructions') }}</h3>
            </div>

            <div class="sg-tabs-nav">
                <button class="sg-tab-btn active" data-target="smarters">
                    <i class="fas fa-tv sg-tab-icon"></i>
                    <span>IPTV Smarters</span>
                </button>
                <button class="sg-tab-btn" data-target="tivimate">
                    <i class="fas fa-satellite-dish sg-tab-icon"></i>
                    <span>TiviMate</span>
                </button>
                <button class="sg-tab-btn" data-target="xciptv">
                    <i class="fas fa-mobile-screen sg-tab-icon"></i>
                    <span>XCIPTV</span>
                </button>
                <button class="sg-tab-btn" data-target="vlc">
                    <i class="fas fa-play sg-tab-icon"></i>
                    <span>VLC / Other</span>
                </button>
            </div>

            <div class="sg-tab-body">

                {{-- IPTV Smarters --}}
                <div class="sg-pane active" id="sgpane-smarters">
                    <div class="sg-app-info">
                        <span class="sg-app-icon sg-app-smarters"><i class="fas fa-tv"></i></span>
                        <div>
                            <h4 class="sg-app-name">IPTV Smarters Pro</h4>
                            <p class="sg-app-platforms">
                                <span class="sg-platform-tag">iOS</span>
                                <span class="sg-platform-tag">Android</span>
                                <span class="sg-platform-tag">Amazon Fire TV</span>
                            </p>
                        </div>
                    </div>
                    <ol class="sg-steps">
                        <li class="sg-step">
                            <span class="sg-step-num">1</span>
                            <div class="sg-step-body">
                                Download <strong>IPTV Smarters Pro</strong> from the App Store, Google Play, or Amazon.
                            </div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">2</span>
                            <div class="sg-step-body">Open the app and tap <strong>"Add User"</strong>.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">3</span>
                            <div class="sg-step-body">Choose <strong>"Login with Xtream Codes API"</strong>.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">4</span>
                            <div class="sg-step-body">
                                Enter the following credentials:
                                <div class="sg-inline-creds">
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Username</span>
                                        <samp class="sg-ic-val">{{ $credentials['username'] ?? '' }}</samp>
                                    </div>
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Password</span>
                                        <samp class="sg-ic-val">{{ $credentials['password'] ?? '' }}</samp>
                                    </div>
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">URL</span>
                                        <samp
                                            class="sg-ic-val">{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</samp>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">5</span>
                            <div class="sg-step-body">Tap <strong>Add User</strong> and wait for the channel list to load.
                            </div>
                        </li>
                    </ol>
                </div>

                {{-- TiviMate --}}
                <div class="sg-pane" id="sgpane-tivimate">
                    <div class="sg-app-info">
                        <span class="sg-app-icon sg-app-tivimate"><i class="fas fa-satellite-dish"></i></span>
                        <div>
                            <h4 class="sg-app-name">TiviMate</h4>
                            <p class="sg-app-platforms">
                                <span class="sg-platform-tag">Android TV</span>
                                <span class="sg-platform-tag">Amazon Fire TV</span>
                            </p>
                        </div>
                    </div>
                    <ol class="sg-steps">
                        <li class="sg-step">
                            <span class="sg-step-num">1</span>
                            <div class="sg-step-body">Install <strong>TiviMate</strong> from the Fire TV store or sideload
                                the APK.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">2</span>
                            <div class="sg-step-body">Open TiviMate and select <strong>"Add Playlist"</strong>.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">3</span>
                            <div class="sg-step-body">Choose <strong>"Xtream Codes"</strong>.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">4</span>
                            <div class="sg-step-body">
                                Enter the following:
                                <div class="sg-inline-creds">
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Server URL</span>
                                        <samp
                                            class="sg-ic-val">{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</samp>
                                    </div>
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Username</span>
                                        <samp class="sg-ic-val">{{ $credentials['username'] ?? '' }}</samp>
                                    </div>
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Password</span>
                                        <samp class="sg-ic-val">{{ $credentials['password'] ?? '' }}</samp>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">5</span>
                            <div class="sg-step-body">Tap <strong>Next</strong> to import your channels.</div>
                        </li>
                    </ol>
                    <div class="sg-tip">
                        <i class="fas fa-lightbulb"></i>
                        TiviMate Companion (premium) is recommended for EPG and recordings.
                    </div>
                </div>

                {{-- XCIPTV --}}
                <div class="sg-pane" id="sgpane-xciptv">
                    <div class="sg-app-info">
                        <span class="sg-app-icon sg-app-xciptv"><i class="fas fa-mobile-screen"></i></span>
                        <div>
                            <h4 class="sg-app-name">XCIPTV Player</h4>
                            <p class="sg-app-platforms">
                                <span class="sg-platform-tag">Android</span>
                                <span class="sg-platform-tag">Android TV</span>
                            </p>
                        </div>
                    </div>
                    <ol class="sg-steps">
                        <li class="sg-step">
                            <span class="sg-step-num">1</span>
                            <div class="sg-step-body">Install <strong>XCIPTV</strong> from Google Play or the official
                                website.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">2</span>
                            <div class="sg-step-body">Open the app and tap <strong>"+"</strong> to add an account.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">3</span>
                            <div class="sg-step-body">Select <strong>"Xtream API"</strong>.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">4</span>
                            <div class="sg-step-body">
                                Fill in the following:
                                <div class="sg-inline-creds">
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Server</span>
                                        <samp
                                            class="sg-ic-val">{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</samp>
                                    </div>
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Username</span>
                                        <samp class="sg-ic-val">{{ $credentials['username'] ?? '' }}</samp>
                                    </div>
                                    <div class="sg-ic-row">
                                        <span class="sg-ic-label">Password</span>
                                        <samp class="sg-ic-val">{{ $credentials['password'] ?? '' }}</samp>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">5</span>
                            <div class="sg-step-body">Press <strong>Login</strong>. Channels will load automatically.</div>
                        </li>
                    </ol>
                </div>

                {{-- VLC / Other --}}
                <div class="sg-pane" id="sgpane-vlc">
                    <div class="sg-app-info">
                        <span class="sg-app-icon sg-app-vlc"><i class="fas fa-play"></i></span>
                        <div>
                            <h4 class="sg-app-name">VLC, Kodi, or any M3U Player</h4>
                            <p class="sg-app-platforms">
                                <span class="sg-platform-tag">Any Device</span>
                            </p>
                        </div>
                    </div>
                    <ol class="sg-steps">
                        <li class="sg-step">
                            <span class="sg-step-num">1</span>
                            <div class="sg-step-body">
                                Copy your M3U Playlist URL:
                                <samp class="sg-url-block">{{ $credentials['m3u_url'] ?? '' }}</samp>
                            </div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">2</span>
                            <div class="sg-step-body">In <strong>VLC</strong>: go to <em>Media → Open Network Stream</em>,
                                paste the URL and click Play.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">3</span>
                            <div class="sg-step-body">In <strong>Kodi</strong>: install the <em>PVR IPTV Simple Client</em>
                                addon and paste the M3U URL in settings.</div>
                        </li>
                        <li class="sg-step">
                            <span class="sg-step-num">4</span>
                            <div class="sg-step-body">
                                For EPG / TV Guide, use:
                                <samp class="sg-url-block">{{ $credentials['epg_url'] ?? '' }}</samp>
                            </div>
                        </li>
                    </ol>
                </div>

            </div>
        </div>

        {{-- Help Footer --}}
        <div class="sg-help-card">
            <span class="sg-help-icon"><i class="fas fa-headset"></i></span>
            <div>
                <p class="sg-help-title">{{ __tr('Need help getting set up?') }}</p>
                <p class="sg-help-text">
                    {{ __tr('Our support team is available around the clock.') }}
                    <a href="{{ route('member.tickets.create') }}"
                        class="sg-help-link">{{ __tr('Open a support ticket') }}</a>
                    {{ __tr('and we\'ll assist you within 24 hours.') }}
                </p>
            </div>
        </div>
    @endif

@endsection
@section('dashboard-js')
    <script>
        function sgCopy(id, btn) {
            const el = document.getElementById(id);
            navigator.clipboard.writeText(el.innerText.trim()).then(() => {
                const icon = btn.querySelector('i');
                icon.className = 'fas fa-check';
                btn.classList.add('sg-copy-success');
                setTimeout(() => {
                    icon.className = 'fas fa-copy';
                    btn.classList.remove('sg-copy-success');
                }, 1800);
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
