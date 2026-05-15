@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('SEO Settings'))
@section('settings-description', __tr('Configure meta title, description, keywords and social sharing image.'))

@section('settings-content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.appearance.site.setting.seo.update') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __tr('Meta Title') }}</label>
                    <input type="text" class="form-control" name="site_meta_title"
                        placeholder="{{ __tr('Enter Meta Title') }}"
                        value="{{ get_setting('site_meta_title') }}">
                </div>
                <div class="form-group">
                    <label>{{ __tr('Meta Description') }}</label>
                    <textarea class="form-control" name="site_meta_description"
                        rows="3">{{ get_setting('site_meta_description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __tr('Meta Keywords') }}</label>
                    <textarea class="form-control" name="site_meta_keys"
                        rows="3">{{ get_setting('site_meta_keys') }}</textarea>
                    <small class="text-muted">{{ __tr('Separate each keyword with a comma (,)') }}</small>
                </div>
                <div class="form-group">
                    <label>{{ __tr('Meta / OG Image') }}</label>
                    <x-media name="site_meta_image" :value="get_setting('site_meta_image')"></x-media>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('settings-script')
    <script>
        (function($) {
            "use strict";
            initMediaManager();
        })(jQuery);
    </script>
@endsection
