@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Environment'))
@section('settings-description', __tr('Configure application environment, database connection and debug settings.'))

@section('settings-content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.system.settings.environment.update') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __tr('APP NAME') }}</label>
                    <input type="text" class="form-control" name="APP_NAME"
                        placeholder="{{ __tr('Enter App Name') }}" value="{{ env('APP_NAME') }}">
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('APP URL') }}</label>
                        <input type="text" class="form-control" name="APP_URL"
                            placeholder="{{ __tr('Enter App URL') }}" value="{{ env('APP_URL') }}">
                    </div>
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('APP DEBUG') }}</label>
                        <select class="form-control" name="APP_DEBUG">
                            <option @selected(env('APP_DEBUG')) value="true">True</option>
                            <option @selected(!env('APP_DEBUG')) value="false">False</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('APP ENVIRONMENT') }}</label>
                        <select class="form-control" name="APP_ENV">
                            <option @selected(env('APP_ENV') == 'local') value="local">Local</option>
                            <option @selected(env('APP_ENV') == 'production') value="production">Production</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('DB CONNECTION') }}</label>
                        <input type="text" class="form-control" name="DB_CONNECTION"
                            value="{{ env('DB_CONNECTION') }}" readonly>
                    </div>
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('DB HOST') }}</label>
                        <input type="text" class="form-control" name="DB_HOST"
                            placeholder="{{ __tr('Enter Database Host') }}" value="{{ env('DB_HOST') }}">
                    </div>
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('DB PORT') }}</label>
                        <input type="text" class="form-control" name="DB_PORT"
                            placeholder="{{ __tr('Enter Database Port') }}" value="{{ env('DB_PORT') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('DATABASE NAME') }}</label>
                        <input type="text" class="form-control" name="DB_DATABASE"
                            placeholder="{{ __tr('Enter Database Name') }}" value="{{ env('DB_DATABASE') }}">
                    </div>
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('DB USERNAME') }}</label>
                        <input type="text" class="form-control" name="DB_USERNAME"
                            placeholder="{{ __tr('Enter Database Username') }}" value="{{ env('DB_USERNAME') }}">
                    </div>
                    <div class="form-group col-lg-4 col-12">
                        <label>{{ __tr('DB PASSWORD') }}</label>
                        <input type="text" class="form-control" name="DB_PASSWORD"
                            placeholder="{{ __tr('Enter Database Password') }}" value="{{ env('DB_PASSWORD') }}">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
