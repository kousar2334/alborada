@extends('frontend.layouts.master')
@section('meta')
    <title>Reset Password - {{ get_setting('site_name') }}</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="auth-card-header text-center">
                <h2 class="auth-title">{{ __tr('Reset Password') }}</h2>
                <p class="auth-subtitle">{{ __tr('Enter your new password below') }}</p>
            </div>

            <form method="post" action="{{ route('member.reset.password.submit') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group mb-20">
                    <label>{{ __tr('Email') }}</label>
                    <input type="email" name="email" class="input-style"
                        placeholder="{{ __tr('Enter your email') }}" value="{{ old('email', $email ?? '') }}">
                    @if ($errors->has('email'))
                        <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <label>{{ __tr('New Password') }}</label>
                    <input type="password" name="password" class="input-style"
                        placeholder="{{ __tr('Enter new password') }}">
                    @if ($errors->has('password'))
                        <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <label>{{ __tr('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" class="input-style"
                        placeholder="{{ __tr('Confirm new password') }}">
                </div>

                <button type="submit" class="cmn-btn w-100">{{ __tr('Reset Password') }}</button>
            </form>
        </div>
    </div>
@endsection
@section('js')
@endsection
