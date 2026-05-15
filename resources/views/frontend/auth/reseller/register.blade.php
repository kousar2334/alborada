@extends('frontend.layouts.master')
@section('meta')
    <title>Reseller Registration - {{ get_setting('site_name') }}</title>
@endsection
@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-card auth-card-wide">
            <div class="auth-card-header text-center">
                <div class="mb-3">
                    <span class="auth-program-badge">RESELLER PROGRAM</span>
                </div>
                <h2 class="auth-title">Apply as Reseller</h2>
                <p class="auth-subtitle">Your application will be reviewed within 24 hours</p>
            </div>

            <form action="{{ route('reseller.register.submit') }}" method="post" class="mb-3">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-20">
                            <label>Full Name</label>
                            <input type="text" class="input-style" name="name" placeholder="Your full name"
                                value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <p class="invalid-feedback d-block">{{ $errors->first('name') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-20">
                            <label>Company / Business Name</label>
                            <input type="text" class="input-style" name="company_name" placeholder="Your company name"
                                value="{{ old('company_name') }}">
                            @if ($errors->has('company_name'))
                                <p class="invalid-feedback d-block">{{ $errors->first('company_name') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-20">
                            <label>Email Address</label>
                            <input type="email" class="input-style" name="email" placeholder="Business email"
                                value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-20">
                            <label>Password</label>
                            <input type="password" class="input-style" name="password" placeholder="Create a password">
                            @if ($errors->has('password'))
                                <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-20">
                            <label>Confirm Password</label>
                            <input type="password" class="input-style" name="password_confirmation"
                                placeholder="Confirm your password">
                        </div>
                    </div>
                </div>

                <div class="auth-notice">
                    Your account will be inactive until approved by our team. We'll contact you via email within 24 hours.
                </div>

                <button type="submit" class="cmn-btn btn-green w-100">
                    Submit Application
                </button>
            </form>

            <p class="auth-switch-text">
                Already a reseller?
                <a href="{{ route('reseller.login') }}" class="auth-switch-link">Sign in</a>
            </p>
        </div>
    </div>
@endsection
