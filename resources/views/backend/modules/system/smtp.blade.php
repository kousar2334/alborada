@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('SMTP'))
@section('settings-description', __tr('Configure outgoing mail server and test your email delivery.'))

@section('settings-content')
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __tr('SMTP Configuration') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.system.settings.smtp.update') }}">
                        @csrf
                        <input type="hidden" name="MAIL_MAILER" value="smtp">
                        <div class="form-group">
                            <label>{{ __tr('MAIL HOST') }}</label>
                            <input type="text" class="form-control" name="MAIL_HOST"
                                placeholder="{{ __tr('Enter Host') }}" value="{{ env('MAIL_HOST') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('PORT') }}</label>
                            <input type="text" class="form-control" name="MAIL_PORT"
                                placeholder="{{ __tr('Enter Port') }}" value="{{ env('MAIL_PORT') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('MAIL USERNAME') }}</label>
                            <input type="text" class="form-control" name="MAIL_USERNAME"
                                placeholder="{{ __tr('Enter Username') }}" value="{{ env('MAIL_USERNAME') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('MAIL PASSWORD') }}</label>
                            <input type="text" class="form-control" name="MAIL_PASSWORD"
                                placeholder="{{ __tr('Enter Password') }}" value="{{ env('MAIL_PASSWORD') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('MAIL ENCRYPTION') }}</label>
                            <input type="text" class="form-control" name="MAIL_ENCRYPTION"
                                placeholder="{{ __tr('Enter Encryption (tls / ssl)') }}"
                                value="{{ env('MAIL_ENCRYPTION') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('MAIL FROM ADDRESS') }}</label>
                            <input type="text" class="form-control" name="MAIL_FROM_ADDRESS"
                                placeholder="{{ __tr('Enter From Address') }}" value="{{ env('MAIL_FROM_ADDRESS') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('MAIL FROM NAME') }}</label>
                            <input type="text" class="form-control" name="MAIL_FROM_NAME"
                                placeholder="{{ __tr('Enter From Name') }}" value="{{ env('MAIL_FROM_NAME') }}">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
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
                                placeholder="{{ __tr('Enter Email') }}">
                            @if ($errors->has('email'))
                                <div class="text-danger mt-1">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Email Subject') }}</label>
                            <input type="text" class="form-control" name="subject"
                                placeholder="{{ __tr('Enter Subject') }}">
                            @if ($errors->has('subject'))
                                <div class="text-danger mt-1">{{ $errors->first('subject') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Message') }}</label>
                            <textarea class="form-control" name="message" rows="4" placeholder="{{ __tr('Enter Message') }}"></textarea>
                            @if ($errors->has('message'))
                                <div class="text-danger mt-1">{{ $errors->first('message') }}</div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __tr('Send Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
