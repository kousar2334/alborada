@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Download App') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title">{{ __tr('Download the App') }}</h1>
        <p class="dash-page-subtitle">{{ __tr('Install 8K VIP IPTV and start watching in a few simple steps.') }}</p>
    </div>

    {{-- 8K VIP IPTV installation guide --}}
    <section class="dashboard-card vip-install-guide">
        <div class="vip-install-header">
            <div class="vip-install-icon"><i class="fas fa-tv"></i></div>
            <div>
                <p class="vip-install-eyebrow">{{ __tr('8K VIP IPTV') }}</p>
                <h2 class="vip-install-title">{{ __tr('App Installation Guide') }}</h2>
            </div>
        </div>

        <div class="vip-download-details">
            <div class="vip-detail-item">
                <span>{{ __tr('Our app') }}</span>
                <strong>8K Player VIP</strong>
            </div>
            <div class="vip-detail-item">
                <span>{{ __tr('Downloader code') }}</span>
                <strong>2213196</strong>
            </div>
            <a class="vip-download-link" href="https://aftv.news/439873" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-download"></i> {{ __tr('Open download link') }}
            </a>
        </div>

        <ol class="vip-install-steps">
            <li>
                <strong>{{ __tr('Download and install') }}</strong>
                <span>{{ __tr('Download the 8K VIP IPTV application, install it on your device, then open it.') }}</span>
            </li>
            <li>
                <strong>{{ __tr('Select Playlist') }}</strong>
                <span>{{ __tr('On the Playlist screen, select 8K VIP.') }}</span>
            </li>
            <li>
                <strong>{{ __tr('Edit Playlist') }}</strong>
                <span>{{ __tr('Tap Edit, then select XTREAM-CODES-API.') }}</span>
            </li>
            <li>
                <strong>{{ __tr('Enter your M3U details') }}</strong>
                <span>{{ __tr('Enter your M3U IPTV username and password from the customer dashboard. Your MAC address is filled in automatically.') }}</span>
            </li>
            <li>
                <strong>{{ __tr('Update Playlist') }}</strong>
                <span>{{ __tr('Tap Update Playlist and wait a few seconds for the update to finish successfully.') }}</span>
            </li>
            <li>
                <strong>{{ __tr('Connect') }}</strong>
                <span>{{ __tr('Return to the playlist screen, select 8K VIP again, and tap Connect.') }}</span>
            </li>
            <li>
                <strong>{{ __tr('Enjoy your IPTV') }}</strong>
                <span>{{ __tr('Your content will load automatically, including Live TV, Movies, Series, Sports, and Playlist.') }}</span>
            </li>
        </ol>
    </section>

    @php
        $deviceOrder = ['firestick', 'android', 'smart_tv', 'ios', 'desktop', 'other'];
        $deviceLabels = [
            'firestick' => 'Amazon Firestick / Fire TV',
            'android' => 'Android TV / Box',
            'smart_tv' => 'Smart TV',
            'ios' => 'iPhone / iPad',
            'desktop' => 'Windows / Mac',
            'other' => 'Other Devices',
        ];
        $deviceIcons = [
            'firestick' => 'fa-fire',
            'android' => 'fa-robot',
            'smart_tv' => 'fa-tv',
            'ios' => 'fa-apple',
            'desktop' => 'fa-desktop',
            'other' => 'fa-mobile-screen',
        ];
        $deviceColors = [
            'firestick' => '#ff9900',
            'android' => '#3ddc84',
            'smart_tv' => '#635bff',
            'ios' => '#aaaaaa',
            'desktop' => '#0078d4',
            'other' => '#00d46a',
        ];
    @endphp

    @if ($downloaderCodes->isEmpty())
        <div class="dashboard-card text-center p-5">
            <i class="fas fa-download empty-state-icon"></i>
            <p class="text-muted">
                {{ __tr('No download codes available yet. Check back soon or contact support.') }}</p>
            <a href="{{ route('member.tickets.create') }}" class="cmn-btn mt-3 d-inline-block">
                {{ __tr('Contact Support') }}
            </a>
        </div>
    @else
        {{-- Device tabs --}}
        <div class="device-tabs-row">
            @foreach ($deviceOrder as $dtype)
                @if (isset($downloaderCodes[$dtype]))
                    <button onclick="showDevice('{{ $dtype }}')" id="tab-{{ $dtype }}"
                        class="device-tab {{ $loop->first ? 'active' : '' }}">
                        <i class="fas {{ $deviceIcons[$dtype] ?? 'fa-mobile-screen' }}"></i>
                        {{ $deviceLabels[$dtype] ?? ucfirst($dtype) }}
                    </button>
                @endif
            @endforeach
        </div>

        {{-- Device panels --}}
        @foreach ($deviceOrder as $dtype)
            @if (isset($downloaderCodes[$dtype]))
                <div id="panel-{{ $dtype }}" class="device-panel {{ !$loop->first ? 'hidden' : '' }}">
                    <div class="device-panel-grid">
                        @foreach ($downloaderCodes[$dtype] as $code)
                            <div class="dashboard-card code-card">
                                <div class="code-card-header code-card-header-{{ $dtype }}">
                                    <div class="code-card-icon-wrap code-card-icon-{{ $dtype }}">
                                        <i
                                            class="fas {{ $deviceIcons[$dtype] ?? 'fa-mobile-screen' }} device-icon-{{ $dtype }}"></i>
                                    </div>
                                    <div>
                                        <div class="code-card-label">{{ $code->label }}</div>
                                        <div class="code-card-sublabel">{{ $deviceLabels[$dtype] ?? ucfirst($dtype) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="code-card-body">
                                    <div class="code-card-code-label">{{ __tr('Downloader Code') }}</div>
                                    <div class="code-card-code-row">
                                        <code id="code-{{ $code->id }}"
                                            class="code-display">{{ $code->code }}</code>
                                        <button onclick="copyToClipboard('code-{{ $code->id }}',this)"
                                            class="copy-btn">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    @if ($code->description)
                                        <p class="code-description">{{ $code->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    {{-- Back to Dashboard --}}
    <div class="mt-4">
        <a href="{{ route('member.dashboard') }}" class="dash-back-link">
            <i class="fas fa-arrow-left"></i> {{ __tr('Back to Dashboard') }}
        </a>
    </div>

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
                    btn.style.color = '#cc0000';
                }, 1500);
            });
        }

        function showDevice(dtype) {
            document.querySelectorAll('.device-panel').forEach(p => p.classList.add('hidden'));
            document.querySelectorAll('.device-tab').forEach(t => t.classList.remove('active'));
            const panel = document.getElementById('panel-' + dtype);
            const tab = document.getElementById('tab-' + dtype);
            if (panel) panel.classList.remove('hidden');
            if (tab) tab.classList.add('active');
        }
    </script>
@endsection
