@extends('backend.layouts.dashboard_layout')

@section('page-title', 'Settings')

@section('page-style')
    @yield('settings-style')
@endsection

@section('page-content')
    <div class="settings-layout" id="settingsLayout">

        {{-- Mobile backdrop --}}
        <div class="settings-sidebar-backdrop" id="settingsSidebarBackdrop"
            onclick="closeSettingsSidebar()"></div>

        {{-- Left: Settings Navigation Sidebar --}}
        <aside class="settings-sidebar" id="settingsSidebar">
            @include('backend.includes.settings_nav')
        </aside>

        {{-- Right: Settings Content --}}
        <div class="settings-content-area">

            {{-- Sticky toolbar --}}
            <div class="settings-toolbar">
                <button class="settings-menu-toggle" id="settingsMenuToggle"
                    onclick="toggleSettingsSidebar()" title="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="settings-toolbar-breadcrumb">
                    <span>Settings</span>
                    @hasSection('settings-title')
                        <span class="sep">/</span>
                        <span class="current">@yield('settings-title')</span>
                    @endif
                </div>
            </div>

            {{-- Scrollable content area --}}
            <div class="settings-content-scroll">
                <div class="settings-content-inner">

                    @hasSection('settings-title')
                        <div class="settings-section-header">
                            <h2 class="settings-section-title">@yield('settings-title')</h2>
                            @hasSection('settings-description')
                                <p class="settings-section-desc">@yield('settings-description')</p>
                            @endif
                        </div>
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
            if (!isMobile()) {
                settingsLayout.classList.remove('sidebar-open');
            } else {
                settingsLayout.classList.remove('sidebar-collapsed');
            }
        });
    </script>
@endsection
