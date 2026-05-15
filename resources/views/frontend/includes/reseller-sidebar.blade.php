<aside class="dashboard-sidebar" id="resellerSidebar">

    <button class="sidebar-close-btn" onclick="toggleSidebar()">
        <i class="fa-solid fa-xmark"></i>
    </button>

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
            <div class="sidebar-user-role">
                <i class="fa-solid fa-store sidebar-role-icon"></i> Reseller
            </div>
            @if (auth()->user()->company_name)
                <div class="sidebar-user-company">
                    {{ auth()->user()->company_name }}
                </div>
            @endif
        </div>
    </div>

    <nav>
        <ul class="sidebar-menu">
            <li class="mt-2">
                <a href="{{ route('reseller.dashboard') }}"
                    class="{{ Request::routeIs('reseller.dashboard') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('reseller.clients') }}"
                    class="{{ Request::routeIs(['reseller.clients', 'reseller.clients.add']) ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-users"></i></span>
                    My Clients
                </a>
            </li>
            <li>
                <a href="{{ route('reseller.api.keys') }}"
                    class="{{ Request::routeIs(['reseller.api.keys']) ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-key"></i></span>
                    API Keys
                </a>
            </li>

            <li>
                <a href="{{ route('reseller.tickets.index') }}"
                    class="{{ Request::routeIs('reseller.tickets.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-headset"></i></span>
                    Support
                </a>
            </li>
            <li>
                <a href="{{ route('reseller.account') }}"
                    class="{{ Request::routeIs('reseller.account') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-user-gear"></i></span>
                    Account
                </a>
            </li>
            <li>
                <a href="{{ route('reseller.logout') }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>
