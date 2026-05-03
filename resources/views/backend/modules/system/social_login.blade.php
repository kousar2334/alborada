@php
    $links = [
        [
            'title' => 'System',
            'route' => 'admin.system.settings.environment',
            'active' => false,
        ],
        [
            'title' => 'Social Media Logins',
            'route' => 'admin.system.settings.smtp',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Social Media Logins') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="Social Media Logins" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @include('backend.includes.settings_navbar')
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card google-settings-card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __tr('Google') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.system.settings.social.login.update') }}">
                                        @csrf
                                        <input type="hidden" name="type" value="google">
                                        <div class="form-group form-check">
                                            <input type="checkbox" class="form-check-input" id="google_login"
                                                name="google_login"
                                                {{ get_setting('google_login') == config('settings.general_status.active') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="google_login">
                                                {{ __tr('Login with Google') }}
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="google_client_id">{{ __tr('Google Client ID') }}</label>
                                            <input type="text" class="form-control" name="GOOGLE_CLIENT_ID"
                                                id="google_client_id" value="{{ env('GOOGLE_CLIENT_ID') }}"
                                                placeholder="{{ __tr('Enter Google Client ID') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="google_client_secret">{{ __tr('Google Client Secret') }}</label>
                                            <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET"
                                                id="google_client_secret" value="{{ env('GOOGLE_CLIENT_SECRET') }}"
                                                placeholder="{{ __tr('Enter Google Client Secret') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="google_redirect_url">{{ __tr('Google Redirect URL') }}</label>
                                            <input type="text" class="form-control" name="GOOGLE_REDIRECT_URL"
                                                id="google_redirect_url" value="{{ env('GOOGLE_REDIRECT_URL') }}"
                                                placeholder="{{ __tr('Enter Google Redirect URL') }}">
                                        </div>
                                        <div class="form-row justify-content-end">
                                            <button type="submit"
                                                class="btn btn-primary">{{ __tr('Save Change') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>


                        </div>
                        <div class="col-lg-6">
                            <div class="card facebook-settings-card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        {{ __tr('Facebook') }}</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.system.settings.social.login.update') }}">
                                        @csrf
                                        <input type="hidden" name="type" value="facebook">
                                        <div class="form-group form-check">
                                            <input type="checkbox" class="form-check-input" id="facebook_login"
                                                name="facebook_login"
                                                {{ get_setting('facebook_login') == config('settings.general_status.active') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="facebook_login">
                                                {{ __tr('Login with Facebook') }}
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="facebook_client_id">{{ __tr('Facebook Client ID') }}</label>
                                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_ID"
                                                id="facebook_client_id" value="{{ env('FACEBOOK_CLIENT_ID') }}"
                                                placeholder="{{ __tr('Enter Facebook Client ID') }}">
                                        </div>
                                        <div class="form-group">
                                            <label
                                                for="facebook_client_secret">{{ __tr('Facebook Client Secret') }}</label>
                                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_SECRET"
                                                id="facebook_client_secret" value="{{ env('FACEBOOK_CLIENT_SECRET') }}"
                                                placeholder="{{ __tr('Enter Facebook Client Secret') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="facebook_redirect_url">{{ __tr('Facebook Redirect URL') }}</label>
                                            <input type="text" class="form-control" name="FACEBOOK_REDIRECT_URL"
                                                id="facebook_redirect_url" value="{{ env('FACEBOOK_REDIRECT_URL') }}"
                                                placeholder="{{ __tr('Enter Facebook Redirect URL') }}">
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
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

        })(jQuery);
    </script>
@endsection
