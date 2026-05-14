<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        @if (get_setting('site_dark_logo') != null)
            <img src="{{ asset(getFilePath(get_setting('site_dark_logo'))) }}" alt="{{ get_setting('site_name') }}"
                class="brand-image" style="opacity: .8">
        @else
            <span class="brand-text font-weight-light">{{ get_setting('site_name') }}</span>
        @endif
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset(getFilePath(auth()->user()->image)) }}" class="img-circle elevation-2"
                    alt="{{ auth()->user()->name }}">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                @foreach (config('admin_nav') as $item)
                    @php
                        $user = auth()->user();

                        if (isset($item['permission'])) {
                            $canAccess = $user->can($item['permission']);
                        } elseif (isset($item['any_permissions'])) {
                            $canAccess = $user->canAny($item['any_permissions']);
                        } else {
                            $canAccess = true;
                        }

                        $isActive = Request::routeIs($item['active_routes']);
                        $hasChildren = !empty($item['children']);
                    @endphp

                    @if ($canAccess)
                        @if ($hasChildren)
                            <li class="nav-item {{ $isActive ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ $isActive ? 'active' : '' }}">
                                    <i class="nav-icon {{ $item['icon'] }}"></i>
                                    <p>
                                        {{ __tr($item['label']) }}
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach ($item['children'] as $child)
                                        @php
                                            if (isset($child['permission'])) {
                                                $canAccessChild = $user->can($child['permission']);
                                            } elseif (isset($child['any_permissions'])) {
                                                $canAccessChild = $user->canAny($child['any_permissions']);
                                            } else {
                                                $canAccessChild = true;
                                            }

                                            $isChildActive = Request::routeIs($child['active_routes']);
                                        @endphp

                                        @if ($canAccessChild)
                                            <li class="nav-item">
                                                <a href="{{ route($child['route']) }}"
                                                    class="nav-link {{ $isChildActive ? 'active' : '' }}">
                                                    <i class="{{ $child['icon'] }} nav-icon"></i>
                                                    <p>{{ __tr($child['label']) }}</p>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route($item['route']) }}"
                                    class="nav-link {{ $isActive ? 'active' : '' }}">
                                    <i class="nav-icon {{ $item['icon'] }}"></i>
                                    <p>{{ __tr($item['label']) }}</p>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach

            </ul>
        </nav>
    </div>
</aside>
