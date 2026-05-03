@php
    $links = [
        [
            'title' => 'Site Settings',
            'route' => '',
            'active' => false,
        ],
        [
            'title' => 'Site Settings',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Site Settings') }}
@endsection
@section('page-content')
    <x-admin-page-header title="" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Site Settings') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-sm-3">
                                    @include('backend.modules.appearances.site-setting.setting_tabs')
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="tab-content" id="vert-tabs-tabContent">
                                        <div
                                            class="tab-pane text-left fade {{ Request::routeIs(['admin.appearance.site.setting']) ? 'show active' : '' }}">
                                            <h4>{{ __tr('Site Settings') }}</h4>
                                            <form method="POST"
                                                action="{{ route('admin.appearance.site.setting.update') }}">
                                                @csrf
                                                <div class="form-group">
                                                    <label>{{ __tr('Site Name') }}</label>
                                                    <input type="text" class="form-control" name="site_name"
                                                        placeholder="Enter Site Name"
                                                        value="{{ get_setting('site_name') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Site Description') }}</label>
                                                    <textarea class="form-control" name="site_description">{{ get_setting('site_description') }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Site Slogan') }}</label>
                                                    <input type="text" class="form-control" name="site_tagline"
                                                        placeholder="Enter Site Slogan"
                                                        value="{{ get_setting('site_tagline') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Site Logo') }}</label>
                                                    <x-media name="site_logo" :value="get_setting('site_logo')"></x-media>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Site Logo (Dark Background)') }}</label>
                                                    <x-media name="site_dark_logo" :value="get_setting('site_dark_logo')"></x-media>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Site Favicon') }}</label>
                                                    <x-media name="site_favicon" :value="get_setting('site_favicon')"></x-media>
                                                </div>

                                                <div class="form-row justify-content-end">
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __tr('Save Change') }}</button>
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
    <script>
        (function($) {
            "use strict";
            initMediaManager();
        })(jQuery);
    </script>
@endsection
