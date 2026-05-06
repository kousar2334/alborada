@extends('frontend.layouts.master')
@section('meta')
    <title>Reseller Login - {{ get_setting('site_name') }}</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="auth-card-header text-center">
                <div class="mb-3">
                    <span style="display:inline-flex;align-items:center;gap:8px;background:rgba(0,212,106,0.1);border:1px solid rgba(0,212,106,0.25);border-radius:50px;padding:6px 16px;font-size:0.78rem;color:#00d46a;font-weight:600;letter-spacing:.5px;">
                        RESELLER PORTAL
                    </span>
                </div>
                <h2 class="auth-title">Reseller Sign In</h2>
                <p class="auth-subtitle">Access your reseller dashboard</p>
            </div>

            <form method="post" action="{{ route('reseller.login.attempt') }}">
                @csrf

                @if ($errors->has('login_error'))
                    <div class="alert alert-danger text-center mb-3">{{ $errors->first('login_error') }}</div>
                @endif

                <div class="form-group mb-20">
                    <label>Email or Phone</label>
                    <input type="text" name="email" class="input-style"
                        placeholder="Enter your email or phone" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <div class="auth-label-row">
                        <label>Password</label>
                        <a href="{{ route('reseller.forgot.password') }}" class="auth-forgot-link">Forgot password?</a>
                    </div>
                    <input type="password" name="password" class="input-style" placeholder="Enter your password">
                    @if ($errors->has('password'))
                        <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="cmn-btn w-100" style="background:var(--green);">Sign In</button>
            </form>

            <p class="auth-switch-text">
                Want to become a reseller?
                <a href="{{ route('reseller.register') }}" class="auth-switch-link">Apply here</a>
            </p>
            <p class="auth-switch-text" style="margin-top:8px;">
                Not a reseller?
                <a href="{{ route('customer.login') }}" class="auth-switch-link">Customer login</a>
            </p>
        </div>
    </div>
@endsection
