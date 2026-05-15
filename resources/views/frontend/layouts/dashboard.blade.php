@extends('frontend.layouts.master')
@section('meta')
    @yield('dash-meta')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('web-assets/frontend/css/dashboard.css') }}">
    <style>
        /* Dashboard green theme — overrides any DB-stored primary color */
        :root {
            --primary-color: #00c853;
            --primary-color-dark: #00a046;
            --primary-color-light: #33d46a;
            --primary-color-lighter: #66e089;
            --primary-color-shadow: #00c85318;
            --primary: #00c853;
        }
    </style>
@endsection

@section('content')
    <div class="mobile-sidebar-overlay" onclick="toggleSidebar()"></div>
    <div class="dash-page-wrapper">
        <div class="dashboard-container container px-0">
            @include('frontend.includes.navbar')

            <main class="dashboard-main">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i> {{ __tr('Menu') }}
                </button>

                @yield('dashboard-content')
            </main>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('dashboardSidebar');
            const overlay = document.querySelector('.mobile-sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>
    @yield('dashboard-js')
@endsection
