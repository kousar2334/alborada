<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel=icon href="{{ asset(getFilePath(get_setting('site_favicon'))) }}" sizes="20x20" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page-title') | {{ __tr('Dashboard') }}</title>
    @include('backend.includes.style')
    @yield('page-style')
</head>

<body class="hold-transition  sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        @include('backend.includes.header')
        @include('backend.includes.sidebar')
        <div class="content-wrapper">
            @yield('page-content')
        </div>
        @include('backend.includes.footer')
    </div>
    @include('backend.media.media_modal')
    @include('backend.includes.script')
    @yield('page-script')
</body>

</html>
