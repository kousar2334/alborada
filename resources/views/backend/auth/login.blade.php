@extends('backend.layouts.auth_layout')
@section('page-title')
    {{ __tr('Login') }}
@endsection
@section('page-content')
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="/" class="h1">
                @if (get_setting('site_logo') != null)
                    <img src="{{ asset(getFilePath(get_setting('site_logo'))) }}" alt="{{ get_setting('site_name') }}"
                        class="img-fluid">
                @else
                    {{ get_setting('site_name') }}
                @endif
            </a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">{{ __tr('Sign in to start your session') }}</p>
            @if ($errors->has('login_error'))
                <p class="alert alert-danger">{{ $errors->first('login_error') }}</p>
            @endif
            <form action="{{ route('admin.auth.login.attempt') }}" method="post">
                @csrf
                <div class="form-row mb-3">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        <p class="error text-danger mb-0">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-row mb-3">
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <p class="error text-danger mb-0">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                {{ __tr('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="social-auth-links text-center mt-2 mb-3">
                    <button class="btn btn-block btn-primary">
                        {{ __tr('Login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
