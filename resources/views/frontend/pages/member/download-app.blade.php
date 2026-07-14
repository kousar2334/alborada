@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Download App') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title">{{ __tr('Download the App') }}</h1>
        <p class="dash-page-subtitle">{{ __tr('Install Moissanite Visions on any device using the codes below.') }}</p>
    </div>

    {{-- How-to Banner --}}
    <div class="dashboard-card howto-card">
        <div class="howto-body">
            <div class="howto-icon-wrap">
                <i class="fas fa-circle-info howto-icon"></i>
            </div>
            <div>
                <h3 class="howto-title">{{ __tr('How to Install') }}</h3>
                <ol class="howto-list">
                    <li>{{ __tr('Open the') }} <strong class="text-white fw-bold">Downloader</strong>
                        {{ __tr('app on your device (available free on all platforms).') }}</li>
                    <li>{{ __tr('Enter the code shown below for your device type.') }}</li>
                    <li>{{ __tr('The Moissanite Visions app will install automatically.') }}</li>
                    <li>{{ __tr('Open the app and log in with your IPTV credentials from the Dashboard.') }}</li>
                </ol>
            </div>
        </div>
    </div>

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
