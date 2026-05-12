@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Download App') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title"><i class="fas fa-download" style="color:#cc0000;margin-right:10px;"></i>{{ __tr('Download the App') }}</h1>
        <p class="dash-page-subtitle">{{ __tr('Install Alborada Box on any device using the codes below.') }}</p>
    </div>

    {{-- How-to Banner --}}
    <div class="dashboard-card" style="background:linear-gradient(135deg,#0a0a0a,#1a0000);border:1px solid rgba(204,0,0,.2);margin-bottom:24px;">
        <div style="padding:20px;display:flex;align-items:flex-start;gap:16px;">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(204,0,0,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-circle-info" style="color:#cc0000;font-size:1.4rem;"></i>
            </div>
            <div>
                <h3 style="color:#fff;font-size:1rem;margin-bottom:6px;font-weight:700;">{{ __tr('How to Install') }}</h3>
                <ol style="color:rgba(255,255,255,.65);font-size:.875rem;line-height:1.8;margin:0;padding-left:18px;">
                    <li>{{ __tr('Open the') }} <strong style="color:#fff;">Downloader</strong> {{ __tr('app on your device (available free on all platforms).') }}</li>
                    <li>{{ __tr('Enter the code shown below for your device type.') }}</li>
                    <li>{{ __tr('The Alborada Box app will install automatically.') }}</li>
                    <li>{{ __tr('Open the app and log in with your IPTV credentials from the Dashboard.') }}</li>
                </ol>
            </div>
        </div>
    </div>

    @php
        $deviceOrder = ['firestick', 'android', 'smart_tv', 'ios', 'desktop', 'other'];
        $deviceLabels = [
            'firestick' => 'Amazon Firestick / Fire TV',
            'android'   => 'Android TV / Box',
            'smart_tv'  => 'Smart TV',
            'ios'       => 'iPhone / iPad',
            'desktop'   => 'Windows / Mac',
            'other'     => 'Other Devices',
        ];
        $deviceIcons = [
            'firestick' => 'fa-fire',
            'android'   => 'fa-robot',
            'smart_tv'  => 'fa-tv',
            'ios'       => 'fa-apple',
            'desktop'   => 'fa-desktop',
            'other'     => 'fa-mobile-screen',
        ];
        $deviceColors = [
            'firestick' => '#ff9900',
            'android'   => '#3ddc84',
            'smart_tv'  => '#635bff',
            'ios'       => '#aaaaaa',
            'desktop'   => '#0078d4',
            'other'     => '#00d46a',
        ];
    @endphp

    @if($downloaderCodes->isEmpty())
        <div class="dashboard-card" style="text-align:center;padding:48px 20px;">
            <i class="fas fa-download" style="font-size:3rem;color:rgba(255,255,255,.1);margin-bottom:16px;display:block;"></i>
            <p style="color:var(--muted);">{{ __tr('No download codes available yet. Check back soon or contact support.') }}</p>
            <a href="{{ route('member.support.create') }}" class="cmn-btn" style="margin-top:12px;display:inline-block;">
                {{ __tr('Contact Support') }}
            </a>
        </div>
    @else
        {{-- Device tabs --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
            @foreach($deviceOrder as $dtype)
                @if(isset($downloaderCodes[$dtype]))
                    <button onclick="showDevice('{{ $dtype }}')" id="tab-{{ $dtype }}"
                        class="device-tab {{ $loop->first ? 'active' : '' }}"
                        style="display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:20px;border:1px solid rgba(255,255,255,.1);background:{{ $loop->first ? 'rgba(204,0,0,.15)' : 'transparent' }};color:{{ $loop->first ? '#cc0000' : 'rgba(255,255,255,.6)' }};font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s;">
                        <i class="fas {{ $deviceIcons[$dtype] ?? 'fa-mobile-screen' }}"></i>
                        {{ $deviceLabels[$dtype] ?? ucfirst($dtype) }}
                    </button>
                @endif
            @endforeach
        </div>

        {{-- Device panels --}}
        @foreach($deviceOrder as $dtype)
            @if(isset($downloaderCodes[$dtype]))
                <div id="panel-{{ $dtype }}" class="device-panel {{ !$loop->first ? 'hidden' : '' }}">
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                        @foreach($downloaderCodes[$dtype] as $code)
                            <div class="dashboard-card" style="padding:0;overflow:hidden;">
                                <div style="background:rgba({{ $dtype === 'firestick' ? '255,153,0' : ($dtype === 'android' ? '61,220,132' : '204,0,0') }},.08);padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:12px;">
                                    <div style="width:40px;height:40px;border-radius:10px;background:rgba({{ $dtype === 'firestick' ? '255,153,0' : ($dtype === 'android' ? '61,220,132' : '204,0,0') }},.15);display:flex;align-items:center;justify-content:center;">
                                        <i class="fas {{ $deviceIcons[$dtype] ?? 'fa-mobile-screen' }}" style="color:{{ $deviceColors[$dtype] ?? '#cc0000' }};font-size:1.1rem;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;color:#fff;font-size:.9rem;">{{ $code->label }}</div>
                                        <div style="font-size:.75rem;color:rgba(255,255,255,.45);">{{ $deviceLabels[$dtype] ?? ucfirst($dtype) }}</div>
                                    </div>
                                </div>
                                <div style="padding:20px;">
                                    <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:8px;">{{ __tr('Downloader Code') }}</div>
                                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                                        <code id="code-{{ $code->id }}" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:#fff;padding:10px 16px;border-radius:8px;font-size:1.4rem;font-weight:700;letter-spacing:2px;flex:1;text-align:center;">{{ $code->code }}</code>
                                        <button onclick="copyToClipboard('code-{{ $code->id }}',this)" style="background:rgba(204,0,0,.15);border:1px solid rgba(204,0,0,.3);color:#cc0000;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:.85rem;">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    @if($code->description)
                                        <p style="font-size:.8rem;color:rgba(255,255,255,.5);line-height:1.6;margin:0;">{{ $code->description }}</p>
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
    <div style="margin-top:24px;">
        <a href="{{ route('member.dashboard') }}" style="color:rgba(255,255,255,.5);font-size:.85rem;text-decoration:none;">
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
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i>'; btn.style.color = '#cc0000'; }, 1500);
    });
}

function showDevice(dtype) {
    document.querySelectorAll('.device-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.device-tab').forEach(t => {
        t.style.background = 'transparent';
        t.style.color = 'rgba(255,255,255,.6)';
        t.style.borderColor = 'rgba(255,255,255,.1)';
    });
    const panel = document.getElementById('panel-' + dtype);
    const tab   = document.getElementById('tab-' + dtype);
    if (panel) panel.classList.remove('hidden');
    if (tab) {
        tab.style.background = 'rgba(204,0,0,.15)';
        tab.style.color = '#cc0000';
        tab.style.borderColor = 'rgba(204,0,0,.3)';
    }
}
</script>
<style>
.hidden { display:none !important; }
</style>
@endsection
