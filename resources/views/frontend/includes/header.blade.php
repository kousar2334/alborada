<header class="header-style-01">
    <nav class="navbar navbar-area header-bg navbar-expand-lg">
        <div class="container nav-container gap-lg-4">
            <div class="responsive-mobile-menu">
                <div class="logo-wrapper">
                    <a href="{{ url('/') }}" class="logo">
                        @if (get_setting('site_logo'))
                            <img src="{{ asset(getFilePath(get_setting('site_logo'))) }}"
                                alt="{{ get_setting('site_name') }}" style="max-height: 50px;">
                        @else
                            <h3>{{ get_setting('site_name') }}</h3>
                        @endif
                    </a>
                </div>
                <div class="click-mobile-menu">
                    {{-- Mobile: user account or sign in (visible only on mobile) --}}
                    @if (auth()->user() == null)
                        <a href="{{ route('member.login') }}" class="cmn-btn sign-in mobile-sign-in d-flex d-lg-none">
                            {{ __tr('Sign In') }}
                        </a>
                    @else
                        <div class="nav-item dropdown mobile-user-dropdown d-flex d-lg-none">
                            <a class="user-trigger dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-user-circle user-icon"></i>
                                <i class="las la-angle-down caret-icon"></i>
                            </a>
                            <ul class="dropdown-menu user-dropdown dropdown-menu-end">
                                <li>
                                    <span class="dropdown-header">{{ __tr('My Account') }}</span>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('member.dashboard') }}">
                                        <i class="las la-tachometer-alt"></i>
                                        {{ __tr('Dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item logout-item" href="{{ route('member.logout') }}">
                                        <i class="las la-sign-out-alt"></i>
                                        {{ __tr('Logout') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    {{-- Mobile: Language Switcher icon (visible only on mobile) --}}
                    @if ($languages->count() > 1)
                        @php $currentLocale = session('locale', 'en'); @endphp
                        <div class="nav-item dropdown mobile-lang-switcher d-flex d-lg-none">
                            <a class="lang-trigger" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false" title="Switch Language">
                                <i class="las la-globe" style="font-size: 1.4rem;"></i>
                            </a>
                            <ul class="dropdown-menu lang-dropdown dropdown-menu-end">
                                @foreach ($languages as $lang)
                                    <li>
                                        <a class="dropdown-item {{ $lang->code === $currentLocale ? 'active' : '' }}"
                                            href="{{ route('frontend.language.switch', $lang->code) }}">
                                            {{ $lang->native_title ?? $lang->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#bizcoxx_main_menu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
            <div class="nav-wrapper">
                <!-- Main Menu -->
                <div class="collapse navbar-collapse" id="bizcoxx_main_menu">
                    <ul class="navbar-nav">
                        @foreach ($menu_items as $item)
                            @if ($item->children->isNotEmpty())
                                <li class="menu-item-has-children">
                                    <a href="{{ $item->link() }}" class="menu-arrow"
                                        @if ($item->target) target="_blank" @endif>
                                        {{ $item->translation('title') }}
                                    </a>
                                    <ul class="sub-menu">
                                        @foreach ($item->children as $child)
                                            <li>
                                                <a href="{{ $child->link() }}"
                                                    @if ($child->target) target="_blank" @endif>
                                                    {{ $child->translation('title') }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $item->link() }}"
                                        @if ($item->target) target="_blank" @endif>
                                        {{ $item->translation('title') }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="btn-wrapper nav-post-btn d-lg-none d-block">
                        <a href="{{ route('ad.post.page') }}" class="cmn-btn popup-modal text-uppercase">
                            <span>{{ __tr('Post free ad') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Menu Right - desktop only -->
            <div class="nav-right-content d-none d-lg-flex gap-4 align-items-center">
                {{-- Language Switcher (desktop) --}}
                @if ($languages->count() > 1)
                    <div class="nav-item dropdown lang-switcher">
                        @php $currentLocale = session('locale', 'en'); @endphp
                        @php $currentLang = $languages->firstWhere('code', $currentLocale) ?? $languages->first(); @endphp
                        <a class="lang-trigger" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="las la-globe"></i>
                            <span class="lang-label">{{ $currentLang->native_title ?? $currentLang->title }}</span>
                            <i class="las la-angle-down caret-icon"></i>
                        </a>
                        <ul class="dropdown-menu lang-dropdown dropdown-menu-end">
                            @foreach ($languages as $lang)
                                <li>
                                    <a class="dropdown-item {{ $lang->code === $currentLocale ? 'active' : '' }}"
                                        href="{{ route('frontend.language.switch', $lang->code) }}">
                                        {{ $lang->native_title ?? $lang->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <ul class="header-cart">

                    @if (auth()->user() == null)
                        <li>
                            <div class="btn-wrapper">
                                <a href="{{ route('member.login') }}" class="cmn-btn sign-in">
                                    {{ __tr('Sign In') }}
                                </a>
                            </div>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="user-trigger dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-user-circle user-icon"></i>
                                <span class="user-name">{{ Str::limit(auth()->user()->name, 12, '...') }}</span>
                                <i class="las la-angle-down caret-icon"></i>
                            </a>
                            <ul class="dropdown-menu user-dropdown dropdown-menu-end">
                                <li>
                                    <span class="dropdown-header">{{ __tr('My Account') }}</span>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('member.dashboard') }}">
                                        <i class="las la-tachometer-alt"></i>
                                        {{ __tr('Dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item logout-item" href="{{ route('member.logout') }}">
                                        <i class="las la-sign-out-alt"></i>
                                        {{ __tr('Logout') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                </ul>
                <div class="btn-wrapper nav-post-btn">
                    <a href="{{ route('ad.post.page') }}" class="cmn-btn popup-modal d-none d-lg-block text-uppercase">
                        <span>{{ __tr('Post free ad') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
