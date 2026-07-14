@extends('frontend.layouts.master')

@section('meta')
    <title>{{ $page->translation('title') }} - {{ get_setting('site_name') }}</title>
    @if ($page->meta_title)
        <meta name="title" content="{{ $page->meta_title }}">
    @endif
    @if ($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if ($page->meta_image)
        <meta property="og:image" content="{{ getFilePath($page->meta_image) }}">
    @endif
@endsection

@section('page-style')
    @if ($page->custom_css)
        {{-- Page-scoped styles authored in the admin. `</style` is stripped so the
             stylesheet cannot close its own tag and inject markup after it. --}}
        <style>
            {!! str_ireplace('</style', '', $page->custom_css) !!}
        </style>
    @endif
@endsection

@section('content')
    @if ($page->is_design_mode)
        {{-- ═════════════════ DESIGN MODE ═════════════════
             The admin owns the layout: their markup renders edge-to-edge, with no
             breadcrumb header and no narrow column constraining it. --}}
        <div class="page-design-canvas">
            {!! $page->translation('content') !!}
        </div>
    @else
        {{-- ═════════════════ EDITOR MODE ═════════════════
             Standard article layout: breadcrumb header + centred content column. --}}
        <section class="contact-breadcrumb-area {{ $page->has_custom_header == config('settings.general_status.active') && $page->header ? 'page-header-' . $page->header : '' }}">
            <div class="container">
                <div class="breadcrumb-content text-center">
                    <h1>{{ $page->translation('title') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">{{ __tr('Home') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $page->translation('title') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>

        <section class="page-content-section" data-padding-top="60" data-padding-bottom="80">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="page-content-body">
                            {!! $page->translation('content') !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
