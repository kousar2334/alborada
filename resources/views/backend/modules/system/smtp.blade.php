@php
    $links = [
        [
            'title' => 'System',
            'route' => 'admin.system.settings.environment',
            'active' => false,
        ],
        [
            'title' => 'SMTP Setup',
            'route' => 'admin.system.settings.smtp',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('SMTP Setup') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="SMTP Setup" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @include('backend.includes.settings_navbar')
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __tr('SMTP Configuration') }}</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.system.settings.smtp.update') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label>MAIL HOST</label>
                                            <input type="text" class="form-control" name="MAIL_HOST"
                                                placeholder="Enter Host" value="{{ env('MAIL_HOST') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>PORT</label>
                                            <input type="text" class="form-control" name="MAIL_PORT"
                                                placeholder="Enter Port" value="{{ env('MAIL_PORT') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>MAIL USERNAME</label>
                                            <input type="text" class="form-control" name="MAIL_USERNAME"
                                                placeholder="Enter Username" value="{{ env('MAIL_USERNAME') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>MAIL PASSWORD</label>
                                            <input type="text" class="form-control" name="MAIL_PASSWORD"
                                                placeholder="Enter Password" value="{{ env('MAIL_PASSWORD') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>MAIL ENCRYPTION</label>
                                            <input type="text" class="form-control" name="MAIL_ENCRYPTION"
                                                placeholder="Enter Encription" value="{{ env('MAIL_ENCRYPTION') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>MAIL FROM ADDRESS</label>
                                            <input type="text" class="form-control" name="MAIL_FROM_ADDRESS"
                                                placeholder="Enter Mail Address" value="{{ env('MAIL_FROM_ADDRESS') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>MAIL FROM NAME</label>
                                            <input type="text" class="form-control" name="MAIL_FROM_NAME"
                                                placeholder="Enter Name" value="{{ env('MAIL_FROM_NAME') }}">
                                        </div>


                                        <div class="form-row justify-content-end">
                                            <button type="submit"
                                                class="btn btn-primary">{{ __tr('Save Change') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __tr('Test Mail') }}</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.system.settings.smtp.mail.test') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label>{{ __tr('Email') }}</label>
                                            <input type="email" class="form-control" name="email"
                                                placeholder="Enter Email">
                                            @if ($errors->has('email'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('email') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __tr('Email Subject') }}</label>
                                            <input type="text" class="form-control" name="subject"
                                                placeholder="Enter Mail Subject">
                                            @if ($errors->has('subject'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('subject') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __tr('Message') }}</label>
                                            <textarea name="message" class="form-control" placeholder="Enter Message"></textarea>
                                            @if ($errors->has('message'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('message') }}</div>
                                            @endif
                                        </div>


                                        <div class="form-row justify-content-end">
                                            <button type="submit" class="btn btn-primary">{{ __tr('Send Now') }}</button>
                                        </div>
                                    </form>
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
    <script>
        (function($) {
            "use strict";

        })(jQuery);
    </script>
@endsection
