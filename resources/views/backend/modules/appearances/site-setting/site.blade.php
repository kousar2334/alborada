@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Site Settings'))
@section('settings-description', __tr('Configure your site name, branding, logo and favicon.'))

@section('settings-content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.appearance.site.setting.update') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __tr('Site Name') }}</label>
                    <input type="text" class="form-control" name="site_name" placeholder="{{ __tr('Enter Site Name') }}"
                        value="{{ get_setting('site_name') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('Site Description') }}</label>
                    <textarea class="form-control" name="site_description" rows="3"
                        placeholder="{{ __tr('Enter Site Description') }}">{{ get_setting('site_description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __tr('Site Slogan') }}</label>
                    <input type="text" class="form-control" name="site_tagline"
                        placeholder="{{ __tr('Enter Site Slogan') }}" value="{{ get_setting('site_tagline') }}">
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4">
                        <label>{{ __tr('Site Logo') }}</label>
                        <x-media name="site_logo" :value="get_setting('site_logo')"></x-media>
                    </div>
                    <div class="form-group col-lg-4">
                        <label>{{ __tr('Site Logo (Dark Background)') }}</label>
                        <x-media name="site_dark_logo" :value="get_setting('site_dark_logo')"></x-media>
                    </div>
                    <div class="form-group col-lg-4">
                        <label>{{ __tr('Site Favicon') }}</label>
                        <x-media name="site_favicon" :value="get_setting('site_favicon')"></x-media>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    @parent
    <script>
        (function($) {
            "use strict";
            initMediaManager();
        })(jQuery);
    </script>
@endsection
