@extends('backend.layouts.dashboard_layout')

@section('page-title', 'Settings')

@section('page-style')
    @yield('settings-style')
@endsection

@section('page-content')
    <div class="sl-wrap" id="settingsLayout">

        {{-- Mobile backdrop --}}
        <div class="sl-backdrop" id="settingsSidebarBackdrop" onclick="closeSettingsSidebar()"></div>

        {{-- Sidebar --}}
        <aside class="sl-sidebar" id="settingsSidebar">
            <div class="sl-sidebar-inner">
                <div class="sl-sidebar-title">
                    <span>Settings</span>
                    <button class="sl-sidebar-close" onclick="closeSettingsSidebar()" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @include('backend.includes.settings_nav')
            </div>
        </aside>

        {{-- Main content --}}
        <div class="sl-main">

            {{-- Top bar --}}
            <div class="sl-topbar">
                <button class="sl-menu-btn" onclick="toggleSettingsSidebar()" title="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav class="sl-breadcrumb" aria-label="breadcrumb">
                    <a href="{{ route('admin.appearance.site.setting') }}" class="sl-bc-home">Settings</a>
                    @hasSection('settings-title')
                        <span class="sl-bc-sep">/</span>
                        <span class="sl-bc-current">@yield('settings-title')</span>
                    @endif
                </nav>
            </div>

            {{-- Scrollable page body --}}
            <div class="sl-body">
                <div class="sl-page">

                    @hasSection('settings-title')
                        <h1 class="sl-page-title">@yield('settings-title')</h1>
                        @hasSection('settings-description')
                            <p class="sl-page-desc">@yield('settings-description')</p>
                        @endif
                    @endif

                    @yield('settings-content')

                </div>
            </div>

        </div>
    </div>
@endsection

@section('page-script')
    @yield('settings-script')
    <script>
        const settingsLayout = document.getElementById('settingsLayout');
        const isMobile = () => window.innerWidth < 768;

        function toggleSettingsSidebar() {
            if (isMobile()) {
                settingsLayout.classList.toggle('sidebar-open');
            } else {
                settingsLayout.classList.toggle('sidebar-collapsed');
            }
        }

        function closeSettingsSidebar() {
            settingsLayout.classList.remove('sidebar-open');
        }

        window.addEventListener('resize', () => {
            if (!isMobile()) settingsLayout.classList.remove('sidebar-open');
            else settingsLayout.classList.remove('sidebar-collapsed');
        });
    </script>
@endsection
