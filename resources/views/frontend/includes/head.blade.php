<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel=icon href="{{ asset(getFilePath(get_setting('site_favicon'))) }}" sizes="20x20" type="image/png">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/bootstrap.css') }}">

    <style>
        :root {
            --primary-color: {{ get_setting('site_primary_color', '#cc0000') }};
            --primary-color-dark: {{ get_setting('site_primary_color_dark', '#990000') }};
            --primary-color-light: {{ get_setting('site_primary_color_light', '#ff3333') }};
            --primary-color-lighter: {{ get_setting('site_primary_color_lighter', '#ff6666') }};
            --primary-color-shadow: {{ get_setting('site_primary_color_shadow', '#cc000018') }};

            --main-color-one: var(--primary-color);
            --main-color-two: {{ get_setting('site_main_color_two', '#111111') }};
            --main-color-three: {{ get_setting('site_main_color_three', '#00d46a') }};

            --heading-color: {{ get_setting('site_heading_color', '#1a1a1a') }};
            --secondary-color: {{ get_setting('site_base_color', '#00d46a') }};
            --header-color: {{ get_setting('site_header_color', '#0a0a0a') }};
        }
    </style>

    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/plugin.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/main-style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/helpers.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/common/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/jquery.ihavecookies.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/all.min.css') }}">
    <link rel="canonical" href="#" />
    @yield('meta')

</head>
