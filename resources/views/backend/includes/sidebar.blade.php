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

                {{-- Dashboard --}}
                @can('View Dashboard')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ Request::routeIs(['admin.dashboard']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>{{ __tr('Dashboard') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Members --}}
                @can('Manage Members')
                    <li class="nav-item">
                        <a href="{{ route('admin.members.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.members.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>{{ __tr('Members') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Media --}}
                @can('Manage Media')
                    <li class="nav-item">
                        <a href="{{ route('admin.media.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.media.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-photo-video"></i>
                            <p>{{ __tr('Media') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Blogs --}}
                @can('Manage Blog')
                    <li
                        class="nav-item {{ Request::routeIs(['admin.blogs.categories.edit', 'admin.blogs.comment.list', 'admin.blogs.edit', 'admin.blogs.list', 'admin.blogs.create', 'admin.blogs.categories.list']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['admin.blogs.categories.edit', 'admin.blogs.comment.list', 'admin.blogs.edit', 'admin.blogs.list', 'admin.blogs.create', 'admin.blogs.categories.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-blog"></i>
                            <p>
                                {{ __tr('Blogs') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('Create New Blog')
                                <li class="nav-item">
                                    <a href="{{ route('admin.blogs.create') }}"
                                        class="nav-link {{ Request::routeIs(['admin.blogs.create']) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __tr('Write New Blog') }}</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('admin.blogs.list') }}"
                                    class="nav-link {{ Request::routeIs(['admin.blogs.list']) ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __tr('All Blogs') }}</p>
                                </a>
                            </li>
                            @can('Manage Blog Category')
                                <li class="nav-item">
                                    <a href="{{ route('admin.blogs.categories.list') }}"
                                        class="nav-link {{ Request::routeIs(['admin.blogs.categories.edit', 'admin.blogs.categories.list']) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __tr('Categories') }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Pages --}}
                @can('Manage Pages')
                    <li
                        class="nav-item {{ Request::routeIs(['admin.page.edit', 'admin.page.list', 'admin.page.create']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['admin.page.edit', 'admin.page.list', 'admin.page.create']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                {{ __tr('Pages') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.page.list') }}"
                                    class="nav-link {{ Request::routeIs(['admin.page.list']) ? 'active' : '' }}">
                                    <i class="fa fa-minus nav-icon"></i>
                                    <p>{{ __tr('All Page') }}</p>
                                </a>
                            </li>
                            @can('Create New Page')
                                <li class="nav-item">
                                    <a href="{{ route('admin.page.create') }}"
                                        class="nav-link {{ Request::routeIs(['admin.page.create']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Create New Page') }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Appearances --}}
                @can('Manage Appearances')
                    <li
                        class="nav-item {{ Request::routeIs(['admin.home.builder', 'admin.appearance.site.setting', 'admin.appearance.site.setting.*', 'admin.appearance.menu.builder']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['admin.home.builder', 'admin.appearance.site.setting', 'admin.appearance.site.setting.*', 'admin.appearance.menu.builder']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-desktop"></i>
                            <p>
                                {{ __tr('Appearances') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('Manage Menu')
                                <li class="nav-item">
                                    <a href="{{ route('admin.appearance.menu.builder') }}"
                                        class="nav-link {{ Request::routeIs(['admin.appearance.menu.builder']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Menus') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Home Builder')
                                <li class="nav-item">
                                    <a href="{{ route('admin.home.builder') }}"
                                        class="nav-link {{ Request::routeIs(['admin.home.builder']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Home Builder') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Site Settings')
                                <li class="nav-item">
                                    <a href="{{ route('admin.appearance.site.setting') }}"
                                        class="nav-link {{ Request::routeIs(['admin.appearance.site.setting']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Site Setting') }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Admin Users / Roles / Permissions --}}
                @canany(['User List', 'Role List View', 'Permission List View'])
                    <li
                        class="nav-item {{ Request::routeIs(['admin.users.list', 'admin.users.permission.list', 'admin.users.role.list']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['admin.users.list', 'admin.users.permission.list', 'admin.users.role.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                {{ __tr('Users') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('User List')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.list') }}"
                                        class="nav-link {{ Request::routeIs(['admin.users.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Users') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Role List View')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.role.list') }}"
                                        class="nav-link {{ Request::routeIs(['admin.users.role.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Roles') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Permission List View')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.permission.list') }}"
                                        class="nav-link {{ Request::routeIs(['admin.users.permission.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Permissions') }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Languages --}}
                @can('Manage Language')
                    <li class="nav-item">
                        <a href="{{ route('admin.system.settings.language.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.system.settings.language.list', 'admin.system.settings.language.translation']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-language"></i>
                            <p>{{ __tr('Languages') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- System --}}
                @canany(['Update Environment', 'Update SMTP', 'Manage Social Login'])
                    <li class="nav-item">
                        <a href="{{ route('admin.system.settings.environment') }}"
                            class="nav-link {{ Request::routeIs(['admin.system.settings.social.login', 'admin.system.settings.environment', 'admin.system.settings.smtp']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>{{ __tr('System') }}</p>
                        </a>
                    </li>
                @endcanany

            </ul>
        </nav>
    </div>
</aside>
