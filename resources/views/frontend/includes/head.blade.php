<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel=icon href="{{ asset(getFilePath(get_setting('site_favicon'))) }}" sizes="20x20" type="image/png">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;regular;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/bootstrap.css') }}">

    <style>
        :root {
            --primary-color: {{ get_setting('site_primary_color', '#260ac4') }};
            --primary-color-dark: {{ get_setting('site_primary_color_dark', '#e84e1b') }};
            --primary-color-light: {{ get_setting('site_primary_color_light', '#ff9a5c') }};
            --primary-color-lighter: {{ get_setting('site_primary_color_lighter', '#ffb347') }};
            --primary-color-shadow: {{ get_setting('site_primary_color_shadow', '#f766310f') }};

            --main-color-one: var(--primary-color);
            --main-color-two: {{ get_setting('site_main_color_two', '#524eb7') }};
            --main-color-three: {{ get_setting('site_main_color_three', '#00cad5') }};

            --heading-color: {{ get_setting('site_heading_color', '#333333') }};
            --secondary-color: {{ get_setting('site_base_color', '#fba260') }};
            --header-color: {{ get_setting('site_header_color', '#fff4ed') }};
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
