@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Footer'))
@section('settings-description', __tr('Manage footer content, copyright text, contact details and social media links.'))

@section('settings-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.css') }}">
@endsection

@section('settings-content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.appearance.site.setting.footer.update') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __tr('Copy Right Text') }}</label>
                    <textarea class="form-control" id="contentSummernote" name="site_copy_right_text">{{ get_setting('site_copy_right_text') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __tr('Subscribe Text') }}</label>
                    <textarea class="form-control" name="footer_subscribe_text" rows="3">{{ get_setting('footer_subscribe_text') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __tr('Address') }}</label>
                    <input type="text" class="form-control" name="footer_address"
                        value="{{ get_setting('footer_address') }}" placeholder="{{ __tr('Enter Address') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('Address 2') }}</label>
                    <input type="text" class="form-control" name="footer_address_2"
                        value="{{ get_setting('footer_address_2') }}" placeholder="{{ __tr('Enter Address 2') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('Phone') }}</label>
                    <input type="text" class="form-control" name="footer_phone_number"
                        value="{{ get_setting('footer_phone_number') }}" placeholder="{{ __tr('Enter Phone') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('Hotline') }}</label>
                    <input type="text" class="form-control" name="footer_hotline"
                        value="{{ get_setting('footer_hotline') }}" placeholder="{{ __tr('Enter Hotline Number') }}">
                </div>

                <hr>
                <p class="font-weight-bold text-muted mb-3">{{ __tr('Social Media') }}</p>

                <div class="form-group">
                    <label>{{ __tr('Facebook URL') }}</label>
                    <input type="text" class="form-control" name="site_fb_link"
                        value="{{ get_setting('site_fb_link') }}" placeholder="{{ __tr('Enter Facebook URL') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('LinkedIn URL') }}</label>
                    <input type="text" class="form-control" name="site_linkedin_link"
                        value="{{ get_setting('site_linkedin_link') }}" placeholder="{{ __tr('Enter LinkedIn URL') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('YouTube URL') }}</label>
                    <input type="text" class="form-control" name="site_youtube_link"
                        value="{{ get_setting('site_youtube_link') }}" placeholder="{{ __tr('Enter YouTube URL') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('Instagram URL') }}</label>
                    <input type="text" class="form-control" name="site_instagram_link"
                        value="{{ get_setting('site_instagram_link') }}" placeholder="{{ __tr('Enter Instagram URL') }}">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('settings-script')
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
