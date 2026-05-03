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

                {{-- Ads Management --}}
                @canany([
                    'Manage Ads',
                    'Manage Ad Categories',
                    'Manage Custom Fields',
                    'Manage Conditions',
                    'Manage
                    Tags',
                    'Manage Ad Reports',
                    'Manage Report Reasons',
                    ])
                    <li
                        class="nav-item {{ Request::routeIs(['classified.ads.report.reasons.list', 'classified.ads.report.reasons.edit', 'classified.ads.reports.list', 'classified.ads.list.featured', 'classified.ads.list', 'classified.ads.custom.field.options', 'classified.ads.custom.field.list', 'classified.ads.tag.list', 'classified.ads.condition.list', 'classified.ads.categories.list']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['classified.ads.report.reasons.list', 'classified.ads.report.reasons.edit', 'classified.ads.reports.list', 'classified.ads.list.featured', 'classified.ads.list', 'classified.ads.custom.field.options', 'classified.ads.custom.field.list', 'classified.ads.tag.list', 'classified.ads.condition.list', 'classified.ads.categories.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                {{ __tr('Ads Management') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('Manage Ads')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('All Ads') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.list.featured') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.list.featured']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Featured Ads') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Ad Categories')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.categories.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.categories.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Categories') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Custom Fields')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.custom.field.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.custom.field.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Custom Fields') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Conditions')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.condition.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.condition.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Conditions') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Tags')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.tag.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.tag.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Tags') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Ad Reports')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.reports.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.reports.list']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Reported Ads') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Report Reasons')
                                <li class="nav-item">
                                    <a href="{{ route('classified.ads.report.reasons.list') }}"
                                        class="nav-link {{ Request::routeIs(['classified.ads.report.reasons.list', 'classified.ads.report.reasons.edit']) ? 'active' : '' }}">
                                        <i class="fa fa-minus nav-icon"></i>
                                        <p>{{ __tr('Report Reasons') }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Pricing Plans --}}
                @can('Manage Pricing Plans')
                    <li class="nav-item">
                        <a href="{{ route('admin.pricing.plans.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.pricing.plans.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>{{ __tr('Pricing Plans') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Subscriptions --}}
                @can('Manage Subscriptions')
                    <li class="nav-item">
                        <a href="{{ route('admin.subscriptions.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.subscriptions.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-crown"></i>
                            <p>{{ __tr('Subscriptions') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Payments --}}
                @canany(['Manage Bank Payments', 'Manage Payment Settings', 'Manage Currency Settings'])
                    <li
                        class="nav-item has-treeview {{ Request::routeIs(['admin.bank.payments', 'admin.payment.settings', 'classified.settings.currency']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['admin.bank.payments', 'admin.payment.settings', 'classified.settings.currency']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>
                                {{ __tr('Payments') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('Manage Bank Payments')
                                <li class="nav-item">
                                    <a href="{{ route('admin.bank.payments') }}"
                                        class="nav-link {{ Request::routeIs(['admin.bank.payments']) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __tr('Transactions') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Currency Settings')
                                <li class="nav-item">
                                    <a href="{{ route('classified.settings.currency') }}"
                                        class="nav-link {{ Request::routeIs(['classified.settings.currency']) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __tr('Currency Settings') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('Manage Payment Settings')
                                <li class="nav-item">
                                    <a href="{{ route('admin.payment.settings') }}"
                                        class="nav-link {{ Request::routeIs(['admin.payment.settings']) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __tr('Payment Settings') }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Locations --}}
                @can('Manage Locations')
                    <li
                        class="nav-item {{ Request::routeIs(['classified.locations.country.list', 'classified.locations.state.list', 'classified.locations.city.list']) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs(['classified.locations.country.list', 'classified.locations.state.list', 'classified.locations.city.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>
                                {{ __tr('Locations') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('classified.locations.country.list') }}"
                                    class="nav-link {{ Request::routeIs(['classified.locations.country.list']) ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __tr('Countries') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('classified.locations.state.list') }}"
                                    class="nav-link {{ Request::routeIs(['classified.locations.state.list']) ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __tr('States') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('classified.locations.city.list') }}"
                                    class="nav-link {{ Request::routeIs(['classified.locations.city.list']) ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __tr('Cities') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Safety Tips --}}
                @can('Manage Safety Tips')
                    <li class="nav-item">
                        <a href="{{ route('classified.settings.safety.tips.list') }}"
                            class="nav-link {{ Request::routeIs(['classified.settings.safety.tips.list', 'classified.settings.safety.tips.edit']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>{{ __tr('Safety Tips') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Advertisements (Banner Ads) --}}
                @can('Manage Advertisements')
                    <li class="nav-item">
                        <a href="{{ route('admin.advertisement.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.advertisement.list', 'admin.advertisement.analytics']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-ad"></i>
                            <p>{{ __tr('Advertisements') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Newsletter --}}
                @can('Manage Newsletter')
                    <li class="nav-item has-treeview {{ Request::routeIs('admin.newsletter.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::routeIs('admin.newsletter.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-paper-plane"></i>
                            <p>
                                {{ __tr('Newsletter') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.newsletter.subscribers') }}"
                                    class="nav-link {{ Request::routeIs('admin.newsletter.subscribers') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __tr('Subscribers') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.newsletter.campaigns') }}"
                                    class="nav-link {{ Request::routeIs(['admin.newsletter.campaigns', 'admin.newsletter.campaigns.*']) ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __tr('Campaigns') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Conversations --}}
                @can('Manage Conversations')
                    <li class="nav-item">
                        <a href="{{ route('admin.conversations.index') }}"
                            class="nav-link {{ Request::routeIs(['admin.conversations.index', 'admin.conversations.show']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>{{ __tr('Conversations') }}</p>
                        </a>
                    </li>
                @endcan

                {{-- Contact Messages --}}
                @can('Manage Message')
                    <li class="nav-item">
                        <a href="{{ route('admin.contact.us.message.list') }}"
                            class="nav-link {{ Request::routeIs(['admin.contact.us.message.list']) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope-open-text"></i>
                            <p>{{ __tr('Contact Messages') }}</p>
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
