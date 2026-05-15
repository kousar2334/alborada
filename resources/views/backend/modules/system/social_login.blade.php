@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Social Logins'))
@section('settings-description', __tr('Enable and configure Google and Facebook OAuth login for users.'))

@section('settings-content')
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __tr('Google') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.system.settings.social.login.update') }}">
                        @csrf
                        <input type="hidden" name="type" value="google">
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="google_login" name="google_login"
                                {{ get_setting('google_login') == config('settings.general_status.active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="google_login">
                                {{ __tr('Enable Login with Google') }}
                            </label>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Google Client ID') }}</label>
                            <input type="text" class="form-control" name="GOOGLE_CLIENT_ID"
                                value="{{ env('GOOGLE_CLIENT_ID') }}" placeholder="{{ __tr('Enter Google Client ID') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Google Client Secret') }}</label>
                            <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET"
                                value="{{ env('GOOGLE_CLIENT_SECRET') }}"
                                placeholder="{{ __tr('Enter Google Client Secret') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Google Redirect URL') }}</label>
                            <input type="text" class="form-control" name="GOOGLE_REDIRECT_URL"
                                value="{{ env('GOOGLE_REDIRECT_URL') }}"
                                placeholder="{{ __tr('Enter Google Redirect URL') }}">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __tr('Facebook') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.system.settings.social.login.update') }}">
                        @csrf
                        <input type="hidden" name="type" value="facebook">
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="facebook_login" name="facebook_login"
                                {{ get_setting('facebook_login') == config('settings.general_status.active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="facebook_login">
                                {{ __tr('Enable Login with Facebook') }}
                            </label>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Facebook Client ID') }}</label>
                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_ID"
                                value="{{ env('FACEBOOK_CLIENT_ID') }}"
                                placeholder="{{ __tr('Enter Facebook Client ID') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Facebook Client Secret') }}</label>
                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_SECRET"
                                value="{{ env('FACEBOOK_CLIENT_SECRET') }}"
                                placeholder="{{ __tr('Enter Facebook Client Secret') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Facebook Redirect URL') }}</label>
                            <input type="text" class="form-control" name="FACEBOOK_REDIRECT_URL"
                                value="{{ env('FACEBOOK_REDIRECT_URL') }}"
                                placeholder="{{ __tr('Enter Facebook Redirect URL') }}">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
