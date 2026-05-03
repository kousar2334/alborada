@extends('frontend.layouts.master')
@section('meta')
    <title>Sign In - {{ get_setting('site_name') }}</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="auth-card-header text-center">
                <h2 class="auth-title">{{ __tr('Welcome Back') }}</h2>
                <p class="auth-subtitle">{{ __tr('Sign in to your account to continue') }}</p>
            </div>

            {{-- Social Login --}}
            <div class="social-login-group">
                @if (get_setting('google_login') == config('settings.general_status.active'))
                    <a href="{{ route('member.social.login', 'google') ?? '#' }}" class="social-btn social-btn-google">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                            <path fill="#EA4335"
                                d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                            <path fill="#4285F4"
                                d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                            <path fill="#FBBC05"
                                d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                            <path fill="#34A853"
                                d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                            <path fill="none" d="M0 0h48v48H0z" />
                        </svg>
                        {{ __tr('Continue with Google') }}
                    </a>
                @endif
                @if (get_setting('facebook_login') == config('settings.general_status.active'))
                    <a href="{{ route('member.social.login', 'facebook') ?? '#' }}" class="social-btn social-btn-facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="#fff">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        {{ __tr('Continue with Facebook') }}
                    </a>
                @endif
            </div>

            @if (get_setting('facebook_login') == config('settings.general_status.active') ||
                    get_setting('google_login') == config('settings.general_status.active'))
                <div class="auth-divider">
                    <span>{{ __tr('or Sign In with email') }}</span>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="post" action="{{ route('member.login.attempt') }}">
                @if ($errors->has('login_error'))
                    <div class="alert alert-danger text-center mb-3">{{ $errors->first('login_error') }}</div>
                @endif
                @csrf

                <div class="form-group mb-20">
                    <label>{{ __tr('Email or Phone') }}</label>
                    <input type="text" name="username" class="input-style"
                        placeholder="{{ __tr('Enter your email or phone') }}" value="{{ old('username') }}">
                    @if ($errors->has('username'))
                        <p class="invalid-feedback d-block">{{ $errors->first('username') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <div class="auth-label-row">
                        <label>{{ __tr('Password') }}</label>
                        <a href="{{ route('member.forgot.password') }}"
                            class="auth-forgot-link">{{ __tr('Forgot password?') }}</a>
                    </div>
                    <input type="password" name="password" class="input-style"
                        placeholder="{{ __tr('Enter your password') }}">
                    @if ($errors->has('password'))
                        <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="form-group">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>{{ __tr('Remember me') }}</span>
                    </label>
                </div>

                <button type="submit" class="cmn-btn w-100">{{ __tr('Sign In') }}</button>
            </form>

            <p class="auth-switch-text">
                {{ __tr("Don't have an account?") }}
                <a href="{{ route('member.register') }}" class="auth-switch-link">{{ __tr('Create account') }}</a>
            </p>
        </div>
    </div>
@endsection
@section('js')
@endsection
