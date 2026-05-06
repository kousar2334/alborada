@extends('frontend.layouts.master')
@section('meta')
    <title>Reset Password - Reseller Portal</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="auth-card-header text-center">
                <h2 class="auth-title">Set New Password</h2>
                <p class="auth-subtitle">Choose a strong password for your account</p>
            </div>

            <form method="post" action="{{ route('reseller.reset.password.submit') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group mb-20">
                    <label>Email Address</label>
                    <input type="email" name="email" class="input-style"
                        placeholder="Your email" value="{{ old('email', $email) }}">
                    @if ($errors->has('email'))
                        <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <label>New Password</label>
                    <input type="password" name="password" class="input-style" placeholder="New password">
                    @if ($errors->has('password'))
                        <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="form-group mb-20">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="input-style"
                        placeholder="Confirm new password">
                </div>

                <button type="submit" class="cmn-btn w-100" style="background:var(--green);">Reset Password</button>
            </form>
        </div>
    </div>
@endsection
