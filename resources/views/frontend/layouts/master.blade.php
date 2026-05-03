<!DOCTYPE html>
<html lang="en" dir="ltr">

@include('frontend.includes.head')

<body class="main-body">

    <x-site-header />
    @yield('content')
    <x-site-footer />

    <!-- back to top area start -->
    <div class="back-to-top">
        <span class="back-top"></span>
    </div>
    <!-- back to top area end -->

    <!-- Scroll Up -->
    <div class="progressParent">
        <svg class="backCircle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- End-of Scroll-->

    @include('frontend.includes.script')
    @yield('js')

</body>

</html>
