<nav class="main-header navbar navbar-expand navbar-white">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-success">{{ __tr('Browse Website') }}</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block ml-2">
            <a href="{{ route('utility.clear.cache') }}" class="btn btn-primary">{{ __tr('Clear Cache') }}</a>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link text-uppercase" data-toggle="dropdown" href="#">{{ getLocale() }}</a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                @foreach (activeLanguages() as $key => $language)
                    <a href="{{ route('admin.system.settings.language.set', ['code' => $language->code]) }}"
                        class="dropdown-item">
                        {{ $language->native_title }}
                    </a>
                    <div class="dropdown-divider"></div>
                @endforeach
            </div>
        </li>

        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge notification-counter"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="notification-list-items">
                </div>
                <a href="#"
                    class="dropdown-item dropdown-footer mark-as-read-all d-none">{{ __tr('Clear All Notification') }}</a>
            </div>
        </li>
        <!--End Notification Dropdown-->
        <!--Expand Button-->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <!--End Expand Button-->
        <!--User Dropdown-->
        <li class="nav-item dropdown">
            <p class="nav-link mb-0 cursor-pointer" data-toggle="dropdown">
                <span class="align-items-center d-flex">
                    <a href="#" class="d-block mr-2">{{ auth()->user()->name }}</a>
                    <img src="{{ asset(getFilePath(auth()->user()->image)) }}"
                        class="elevation-2 img-circle img-rounded img-sm" alt="{{ auth()->user()->name }}">
                </span>
            </p>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-item dropdown-header">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset(getFilePath(auth()->user()->image)) }}"
                            class="elevation-2 img-circle img-rounded img-size-50" alt="{{ auth()->user()->name }}">
                        <div class="d-flex flex-column info ml-3 text-left">
                            <p class="d-block mb-0 font-weight-bold">{{ auth()->user()->name }}</p>
                            <p class="d-block mb-0">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.auth.profile') }}" class="dropdown-item">
                    {{ __tr('Profile') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.auth.logout') }}" class="dropdown-item">
                    {{ __tr('Log out') }}
                </a>
            </div>
        </li>
        <!--End User Dropdown-->
    </ul>
</nav>
