<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel=icon href="{{ asset(getFilePath(get_setting('site_favicon'))) }}" sizes="20x20" type="image/png">
    <title>@yield('page-title')</title>
    @include('backend.includes.style')
    @yield('page-style')
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        @yield('page-content')
    </div>
    @include('backend.includes.script')
    @yield('page-script')
</body>

</html>
