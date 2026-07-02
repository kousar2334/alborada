<aside class="dashboard-sidebar" id="dashboardSidebar">

    {{-- Mobile close button --}}
    <button class="sidebar-close-btn" onclick="toggleSidebar()">
        <i class="fa-solid fa-xmark"></i>
    </button>

    {{-- User profile block --}}
    <div class="sidebar-user">
        @if (auth()->user()->image)
            <img src="{{ asset(getFilePath(auth()->user()->image)) }}" alt="{{ auth()->user()->name }}"
                class="sidebar-user-img">
        @else
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-role">{{ __tr('Moissanite Radiance Member') }}</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav>
        <ul class="sidebar-menu">
            <li class="mt-2">
                <a href="{{ route('member.dashboard') }}"
                    class="{{ Request::routeIs('member.dashboard') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    {{ __tr('Dashboard') }}
                </a>
            </li>
            <li>
                <a href="{{ route('member.subscriptions') }}"
                    class="{{ Request::routeIs('member.subscriptions') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-satellite-dish"></i></span>
                    {{ __tr('My Subscription') }}
                </a>
            </li>
            <li>
                <a href="{{ route('member.download.app') }}"
                    class="{{ Request::routeIs('member.download.app') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-download"></i></span>
                    {{ __tr('Download App') }}
                </a>
            </li>
            <li>
                <a href="{{ route('member.setup.guide') }}"
                    class="{{ Request::routeIs('member.setup.guide') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-tv"></i></span>
                    {{ __tr('Setup Guide') }}
                </a>
            </li>
            <li>
                <a href="{{ route('member.tickets.index') }}"
                    class="{{ Request::routeIs(['member.tickets.index', 'member.tickets.create', 'member.tickets.show']) ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-headset"></i></span>
                    {{ __tr('Support') }}
                </a>
            </li>
            <li>
                <a href="{{ route('member.subscriptions') }}#invoices" class="">
                    <span class="sidebar-icon"><i class="fa-solid fa-receipt"></i></span>
                    {{ __tr('Billing & Invoices') }}
                </a>
            </li>
            <li>
                <a href="{{ route('member.account') }}"
                    class="{{ Request::routeIs('member.account') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-user-gear"></i></span>
                    {{ __tr('My Account') }}
                </a>
            </li>
        </ul>

        <hr class="sidebar-divider">

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('member.logout') }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                    {{ __tr('Logout') }}
                </a>
            </li>
        </ul>
    </nav>
</aside>
