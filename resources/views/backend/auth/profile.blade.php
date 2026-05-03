@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Profile') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <!--Page Header-->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __tr('Profile') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __tr('Home') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __tr('Profile') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!--End page header-->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ __tr('Profile') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-sm-3">
                                    <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist"
                                        aria-orientation="vertical">
                                        <a class="nav-link active" id="profile-tab-tab" data-toggle="pill"
                                            href="#profile-tab" role="tab" aria-controls="profile-tab"
                                            aria-selected="true">{{ __tr('Profile') }}</a>
                                        <a class="nav-link" id="change-password-tab-tab" data-toggle="pill"
                                            href="#change-password-tab" role="tab" aria-controls="change-password-tab"
                                            aria-selected="false">{{ __tr('Change Password') }}</a>
                                    </div>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="tab-content" id="vert-tabs-tabContent">
                                        <div class="tab-pane text-left fade show active" id="profile-tab" role="tabpanel"
                                            aria-labelledby="profile-tab-tab">
                                            <h4 class="header-title">{{ __tr('User Information') }}</h4>
                                            <form id="update-profile-form">
                                                @csrf
                                                <div class="form-group">
                                                    <label>{{ __tr('Image') }}</label>
                                                    <x-media name="image" :value="auth()->user()->image"></x-media>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Name') }}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        placeholder="Enter Name" value="{{ auth()->user()->name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Email') }}</label>
                                                    <input type="email" class="form-control" name="email"
                                                        placeholder="Enter Email" value="{{ auth()->user()->email }}">
                                                </div>
                                            </form>
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-primary update-profile-btn">
                                                    {{ __tr('Save Changes') }}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="change-password-tab" role="tabpanel"
                                            aria-labelledby="change-password-tab-tab">
                                            <h4 class="header-title">{{ __tr('Change Password') }}</h4>
                                            <form id="update-password-form">
                                                @csrf

                                                <div class="form-group">
                                                    <label>{{ __tr('Current Password') }}</label>
                                                    <input type="password" class="form-control" name="current_password"
                                                        placeholder="Enter Current Password">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('New Password') }}</label>
                                                    <input type="password" class="form-control" name="password"
                                                        placeholder="Enter New Password">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __tr('Confirm Password') }}</label>
                                                    <input type="password" class="form-control" name="password_confirmation"
                                                        placeholder="Re Enter Password">
                                                </div>
                                            </form>
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-primary update-password-btn">
                                                    {{ __tr('Update Password') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
    </script>
    <script>
        (function($) {
            "use strict";
            //Update user profile
            $('.update-profile-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#update-profile-form').serialize(),
                    url: '{{ route('admin.auth.profile.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Profile updated successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error(response.message, 'Error')
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>');
                            })
                        } else {
                            toastr.error('Profile update failed', 'Error');
                        }
                    }
                });
            });
            //Update user password
            $('.update-password-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#update-password-form').serialize(),
                    url: '{{ route('admin.auth.password.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Password updated successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error(response.message, 'Error')
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>');
                            })
                        } else {
                            toastr.error('Password update failed', 'Error');
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endsection
