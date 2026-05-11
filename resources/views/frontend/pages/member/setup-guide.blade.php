@extends('frontend.layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h2 class="mb-1">Setup Guide</h2>
            <p class="text-muted mb-4">Connect your IPTV subscription to your preferred app.</p>

            @if(!$subscription)
            <div class="alert alert-warning">
                You don't have an active subscription.
                <a href="{{ route('pricing.plans') }}">View plans</a> to get started.
            </div>
            @else

            {{-- Credentials box --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Your IPTV Credentials</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase small text-muted letter-spacing-1">Username</label>
                            <div class="d-flex align-items-center">
                                <code id="cred-username" class="flex-grow-1 bg-light p-2 rounded">{{ $credentials['username'] ?? 'N/A' }}</code>
                                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyText('cred-username')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase small text-muted letter-spacing-1">Password</label>
                            <div class="d-flex align-items-center">
                                <code id="cred-password" class="flex-grow-1 bg-light p-2 rounded">{{ $credentials['password'] ?? 'N/A' }}</code>
                                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyText('cred-password')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(!empty($credentials['m3u_url']))
                    <div class="mb-3">
                        <label class="text-uppercase small text-muted letter-spacing-1">M3U Playlist URL</label>
                        <div class="d-flex align-items-center">
                            <code id="cred-m3u" class="flex-grow-1 bg-light p-2 rounded" style="word-break:break-all;font-size:12px;">{{ $credentials['m3u_url'] }}</code>
                            <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyText('cred-m3u')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="text-uppercase small text-muted letter-spacing-1">EPG / Guide URL</label>
                        <div class="d-flex align-items-center">
                            <code id="cred-epg" class="flex-grow-1 bg-light p-2 rounded" style="word-break:break-all;font-size:12px;">{{ $credentials['epg_url'] }}</code>
                            <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyText('cred-epg')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <hr>
                    <p class="mb-0 small text-muted">
                        Plan: <strong>{{ $subscription->plan->title ?? 'N/A' }}</strong> &bull;
                        Connections: <strong>{{ $subscription->plan->max_connections ?? 1 }}</strong> &bull;
                        Quality: <strong>{{ $subscription->plan->streaming_quality ?? 'HD' }}</strong> &bull;
                        Expires: <strong>{{ $subscription->expires_at?->format('M d, Y') ?? 'N/A' }}</strong>
                    </p>
                </div>
            </div>

            {{-- App tabs --}}
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="appTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-smarters" data-toggle="tab" href="#smarters" role="tab">IPTV Smarters</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-tivimate" data-toggle="tab" href="#tivimate" role="tab">TiviMate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-xciptv" data-toggle="tab" href="#xciptv" role="tab">XCIPTV</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-vlc" data-toggle="tab" href="#vlc" role="tab">VLC / Other</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">

                    {{-- Smarters --}}
                    <div class="tab-pane fade show active" id="smarters" role="tabpanel">
                        <h5>IPTV Smarters Pro</h5>
                        <ol>
                            <li>Download <strong>IPTV Smarters Pro</strong> from the App Store / Google Play / Amazon.</li>
                            <li>Open the app and tap <strong>"Add User"</strong>.</li>
                            <li>Choose <strong>"Login with Xtream Codes API"</strong>.</li>
                            <li>Enter the following:
                                <ul>
                                    <li><strong>Username:</strong> <code>{{ $credentials['username'] ?? '' }}</code></li>
                                    <li><strong>Password:</strong> <code>{{ $credentials['password'] ?? '' }}</code></li>
                                    <li><strong>URL:</strong> <code>{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</code></li>
                                </ul>
                            </li>
                            <li>Tap <strong>Add User</strong> and wait for the channel list to load.</li>
                        </ol>
                    </div>

                    {{-- TiviMate --}}
                    <div class="tab-pane fade" id="tivimate" role="tabpanel">
                        <h5>TiviMate (Android / Fire TV)</h5>
                        <ol>
                            <li>Install <strong>TiviMate</strong> from the Fire TV store or sideload the APK.</li>
                            <li>Open TiviMate and select <strong>"Add Playlist"</strong>.</li>
                            <li>Choose <strong>"Xtream Codes"</strong>.</li>
                            <li>Enter:
                                <ul>
                                    <li><strong>Server URL:</strong> <code>{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</code></li>
                                    <li><strong>Username:</strong> <code>{{ $credentials['username'] ?? '' }}</code></li>
                                    <li><strong>Password:</strong> <code>{{ $credentials['password'] ?? '' }}</code></li>
                                </ul>
                            </li>
                            <li>Tap <strong>Next</strong> to import. Once loaded, enjoy your channels.</li>
                        </ol>
                        <p class="text-muted small">TiviMate Companion (premium) is recommended for EPG and recordings.</p>
                    </div>

                    {{-- XCIPTV --}}
                    <div class="tab-pane fade" id="xciptv" role="tabpanel">
                        <h5>XCIPTV Player</h5>
                        <ol>
                            <li>Install <strong>XCIPTV</strong> from Google Play or the official website.</li>
                            <li>Open the app and tap <strong>"+"</strong> to add an account.</li>
                            <li>Select <strong>"Xtream API"</strong>.</li>
                            <li>Fill in:
                                <ul>
                                    <li><strong>Server:</strong> <code>{{ rtrim(get_setting('xtream_base_url', ''), '/') }}</code></li>
                                    <li><strong>Username:</strong> <code>{{ $credentials['username'] ?? '' }}</code></li>
                                    <li><strong>Password:</strong> <code>{{ $credentials['password'] ?? '' }}</code></li>
                                </ul>
                            </li>
                            <li>Press <strong>Login</strong>. Channels will load automatically.</li>
                        </ol>
                    </div>

                    {{-- VLC / M3U --}}
                    <div class="tab-pane fade" id="vlc" role="tabpanel">
                        <h5>VLC, Kodi, or any M3U player</h5>
                        <ol>
                            <li>Copy your M3U Playlist URL:<br>
                                <code style="word-break:break-all;">{{ $credentials['m3u_url'] ?? '' }}</code>
                            </li>
                            <li>In VLC: <strong>Media → Open Network Stream</strong>, paste the URL and click Play.</li>
                            <li>In Kodi: install the <strong>PVR IPTV Simple Client</strong> addon, paste the M3U URL in settings.</li>
                            <li>For EPG, use the EPG URL:<br>
                                <code style="word-break:break-all;">{{ $credentials['epg_url'] ?? '' }}</code>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>

            <div class="mt-4 text-center">
                <p>Need help? <a href="{{ route('member.tickets.create') }}">Open a support ticket</a> and our team will assist you.</p>
            </div>

            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyText(id) {
    const el = document.getElementById(id);
    navigator.clipboard.writeText(el.innerText).then(() => {
        toastr && toastr.success('Copied!');
    });
}
</script>
@endpush
@endsection
