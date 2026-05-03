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

@section('content')
    {{-- ══════════════════ HERO / BREADCRUMB ══════════════════ --}}
    @if ($page->has_custom_header == config('settings.general_status.active') && $page->header)
        {!! $page->header !!}
    @else
        <section class="contact-breadcrumb-area">
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
    @endif
    {{-- ══════════════════ END HERO ══════════════════════════ --}}

    {{-- ══════════════════ PAGE CONTENT ══════════════════════ --}}
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
    {{-- ══════════════════ END PAGE CONTENT ═══════════════════ --}}
@endsection
