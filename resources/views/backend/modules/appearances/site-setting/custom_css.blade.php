@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Custom CSS'))
@section('settings-description', __tr('Add custom CSS that will be injected into every page of your site.'))

@section('settings-content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.appearance.site.setting.custom.css.update') }}">
                @csrf
                <div class="form-group">
                    <textarea class="form-control settings-css-editor" rows="22" name="custom_css"
                        placeholder="{{ __tr('/* Write your custom CSS here */') }}">{{ get_setting('custom_css') }}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
