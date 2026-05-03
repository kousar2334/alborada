@extends('frontend.layouts.master')
@section('meta')
    <title>Forgot Password - {{ get_setting('site_name') }}</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="auth-card-header text-center">
                <h2 class="auth-title">{{ __tr('Forgot Password?') }}</h2>
                <p class="auth-subtitle">{{ __tr('Enter your email and we\'ll send you a reset link') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success text-center mb-3">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ route('member.forgot.password.submit') }}">
                @csrf

                <div class="form-group mb-20">
                    <label>{{ __tr('Email') }}</label>
                    <input type="email" name="email" class="input-style" placeholder="{{ __tr('Enter your email') }}"
                        value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <button type="submit" class="cmn-btn w-100">{{ __tr('Send Reset Link') }}</button>
            </form>

            <p class="auth-switch-text">
                {{ __tr('Remember your password?') }}
                <a href="{{ route('member.login') }}" class="auth-switch-link">{{ __tr('Sign in') }}</a>
            </p>
        </div>
    </div>
@endsection
@section('js')
@endsection
