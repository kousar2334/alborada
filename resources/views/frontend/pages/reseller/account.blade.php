@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>Account - Reseller Portal</title>
@endsection
@section('reseller-content')
    <div class="dashboard-header">
        <h1 class="dash-page-title">My Account</h1>
        <p class="dash-page-subtitle">Manage your reseller profile and security settings.</p>
    </div>

    <div class="content-grid">
        {{-- Profile --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="card-body card-body-padded">
                <form action="{{ route('reseller.account.update') }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-20">
                        <label class="form-label-sm">Full Name</label>
                        <input type="text" name="name" class="input-style" value="{{ old('name', $user->name) }}">
                        @if ($errors->has('name'))
                            <p class="invalid-feedback d-block">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <div class="form-group mb-20">
                        <label class="form-label-sm">Company / Business Name</label>
                        <input type="text" name="company_name" class="input-style"
                            value="{{ old('company_name', $user->company_name) }}" placeholder="Your company name">
                        @if ($errors->has('company_name'))
                            <p class="invalid-feedback d-block">{{ $errors->first('company_name') }}</p>
                        @endif
                    </div>

                    <div class="form-group mb-20">
                        <label class="form-label-sm">Email Address</label>
                        <input type="email" name="email" class="input-style" value="{{ old('email', $user->email) }}">
                        @if ($errors->has('email'))
                            <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <div class="form-group mb-20">
                        <label class="form-label-sm">Phone</label>
                        <input type="text" name="phone" class="input-style" value="{{ old('phone', $user->phone) }}"
                            placeholder="+1234567890">
                    </div>

                    <button type="submit" class="cmn-btn cmn-btn-green">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- Password --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <div class="card-body card-body-padded">
                <form action="{{ route('reseller.account.password') }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-20">
                        <label class="form-label-sm">Current Password</label>
                        <input type="password" name="current_password" class="input-style"
                            placeholder="Your current password">
                        @if ($errors->has('current_password'))
                            <p class="invalid-feedback d-block">{{ $errors->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="form-group mb-20">
                        <label class="form-label-sm">New Password</label>
                        <input type="password" name="new_password" class="input-style" placeholder="Min 8 characters">
                        @if ($errors->has('new_password'))
                            <p class="invalid-feedback d-block">{{ $errors->first('new_password') }}</p>
                        @endif
                    </div>

                    <div class="form-group mb-20">
                        <label class="form-label-sm">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="input-style"
                            placeholder="Repeat new password">
                    </div>

                    <button type="submit" class="cmn-btn cmn-btn-green">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
