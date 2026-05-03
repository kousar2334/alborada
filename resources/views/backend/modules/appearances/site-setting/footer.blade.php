@php
    $links = [
        [
            'title' => 'Footer Setting',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Footer Setting') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Footer Setting') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-sm-3">
                                    @include('backend.modules.appearances.site-setting.setting_tabs')
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="tab-content">
                                        <div
                                            class="tab-pane text-left fade {{ Request::routeIs(['admin.appearance.site.setting.footer']) ? 'show active' : '' }}">
                                            <h4>{{ __tr('Footer Setting') }}</h4>
                                            <hr>
                                            <form method="POST"
                                                action="{{ route('admin.appearance.site.setting.footer.update') }}">
                                                @csrf

                                                <div class="form-group">
                                                    <label>{{ __('Copy Right Text') }}</label>
                                                    <textarea class="form-control" id="contentSummernote" name="site_copy_right_text">{{ get_setting('site_copy_right_text') }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Subscribe Text') }}</label>
                                                    <textarea class="form-control" name="footer_subscribe_text">{{ get_setting('footer_subscribe_text') }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __tr('Address') }}</label>
                                                    <input type="text" class="form-control" name="footer_address"
                                                        value="{{ get_setting('footer_address') }}"
                                                        placeholder="{{ __tr('Enter Address') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __tr('Address 2') }}</label>
                                                    <input type="text" class="form-control" name="footer_address_2"
                                                        value="{{ get_setting('footer_address_2') }}"
                                                        placeholder="{{ __tr('Enter Address') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __tr('Phone') }}</label>
                                                    <input type="text" class="form-control" name="footer_phone_number"
                                                        value="{{ get_setting('footer_phone_number') }}"
                                                        placeholder="{{ __tr('Enter Phone') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Hotline') }}</label>
                                                    <input type="text" class="form-control" name="footer_hotline"
                                                        value="{{ get_setting('footer_hotline') }}"
                                                        placeholder="{{ __tr('Enter Number') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __tr('Facebook Account') }}</label>
                                                    <input type="text" class="form-control" name="site_fb_link"
                                                        value="{{ get_setting('site_fb_link') }}"
                                                        placeholder="{{ __tr('Enter Facebook Account Link') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Linkedin Account') }}</label>
                                                    <input type="text" class="form-control" name="site_linkedin_link"
                                                        value="{{ get_setting('site_linkedin_link') }}"
                                                        placeholder="{{ __tr('Enter Linkedin Account Link') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Youtube Account') }}</label>
                                                    <input type="text" class="form-control" name="site_youtube_link"
                                                        value="{{ get_setting('site_youtube_link') }}"
                                                        placeholder="{{ __tr('Enter Youtube Account Link') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Instagram Account') }}</label>
                                                    <input type="text" class="form-control" name="site_instagram_link"
                                                        value="{{ get_setting('site_instagram_link') }}"
                                                        placeholder="{{ __tr('Enter Twitter Account Link') }}">
                                                </div>

                                                <div class="d-flex justify-content-end">
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
    <script src="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
            $('#contentSummernote').summernote({
                tabsize: 2,
                height: 100,
                toolbar: [
                    ["style", ["style"]],
                    ['fontsize', ['fontsize']],
                    ["font", ["bold", "underline", "clear"]],
                    ["color", ["color"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["insert", ["link"]],
                    ["view", ["fullscreen", "help"]],
                ],
            });

        })(jQuery);
    </script>
@endsection
