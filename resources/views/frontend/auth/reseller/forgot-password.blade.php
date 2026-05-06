@extends('frontend.layouts.master')
@section('meta')
    <title>Reset Password - Reseller Portal</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="auth-card-header text-center">
                <h2 class="auth-title">Forgot Password</h2>
                <p class="auth-subtitle">Enter your email and we'll send a reset link</p>
            </div>

            <form method="post" action="{{ route('reseller.forgot.password.submit') }}">
                @csrf
                <div class="form-group mb-20">
                    <label>Email Address</label>
                    <input type="email" name="email" class="input-style"
                        placeholder="Enter your email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <button type="submit" class="cmn-btn w-100" style="background:var(--green);">Send Reset Link</button>
            </form>

            <p class="auth-switch-text">
                <a href="{{ route('reseller.login') }}" class="auth-switch-link">← Back to login</a>
            </p>
        </div>
    </div>
@endsection
