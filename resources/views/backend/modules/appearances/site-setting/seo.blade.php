@php
    $links = [
        [
            'title' => 'Site Settings',
            'route' => '',
            'active' => false,
        ],
        [
            'title' => 'Seo Setting',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Seo Setting') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Seo Setting') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-sm-3">
                                    @include('backend.modules.appearances.site-setting.setting_tabs')
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="tab-content" id="vert-tabs-tabContent">
                                        <div
                                            class="tab-pane text-left fade {{ Request::routeIs(['admin.appearance.site.setting.seo']) ? 'show active' : '' }}">
                                            <h4>{{ __tr('Seo Setting') }}</h4>
                                            <form method="POST"
                                                action="{{ route('admin.appearance.site.setting.seo.update') }}">
                                                @csrf
                                                <div class="form-group">
                                                    <label>{{ __tr('Meta Title') }}</label>
                                                    <input type="text" class="form-control" name="site_meta_title"
                                                        placeholder="Enter Meta Title"
                                                        value="{{ get_setting('site_meta_title') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Meta Description') }}</label>
                                                    <textarea class="form-control" name="site_meta_description">{{ get_setting('site_meta_description') }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Meta Keys') }}</label>
                                                    <textarea class="form-control" name="site_meta_keys">{{ get_setting('site_meta_keys') }}</textarea>
                                                    <span
                                                        class="text-indigo">{{ __tr('Separate each keys with coma(,)') }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Meta Image') }}</label>
                                                    <x-media name="site_meta_image" :value="get_setting('site_meta_image')"></x-media>
                                                </div>
                                                <div class="form-row justify-content-end">
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __tr('Save Change') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
        })(jQuery);
    </script>
@endsection
