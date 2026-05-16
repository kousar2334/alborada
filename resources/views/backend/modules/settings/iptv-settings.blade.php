@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('IPTV Settings'))
@section('settings-description', __tr('Configure Xtream Codes API and WHMCS integration for automatic IPTV
    provisioning.'))

@section('settings-content')
    <form action="{{ route('admin.system.settings.iptv.update') }}" method="POST">
        @csrf
        <div class="row">

            {{-- Xtream Codes --}}
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="fas fa-tv mr-2 text-primary"></i>{{ __tr('Xtream Codes API') }}</h6>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="iptv_provisioning_enabled"
                                name="iptv_provisioning_enabled" value="1"
                                {{ get_setting('iptv_provisioning_enabled', 0) ? 'checked' : '' }}>
                            <label class="custom-control-label"
                                for="iptv_provisioning_enabled">{{ __tr('Auto-Provision') }}</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __tr('Server Base URL') }}</label>
                            <input type="url" name="xtream_base_url" class="form-control"
                                value="{{ get_setting('xtream_base_url') }}" placeholder="http://yourserver.com:8080">
                            <small
                                class="text-muted">{{ __tr('No trailing slash. Example: http://yourserver.com:8080') }}</small>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Admin Username') }}</label>
                            <input type="text" name="xtream_admin_username" class="form-control"
                                value="{{ get_setting('xtream_admin_username') }}" placeholder="admin">
                        </div>
                        <div class="form-group mb-0">
                            <label>{{ __tr('Admin Password') }}</label>
                            <input type="password" name="xtream_admin_password" class="form-control"
                                value="{{ get_setting('xtream_admin_password') }}" placeholder="••••••••"
                                autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

            {{-- WHMCS --}}
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="fas fa-server mr-2 text-success"></i>{{ __tr('WHMCS Integration') }}
                        </h6>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="whmcs_sync_enabled"
                                name="whmcs_sync_enabled" value="1"
                                {{ get_setting('whmcs_sync_enabled', 0) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="whmcs_sync_enabled">{{ __tr('Sync Enabled') }}</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __tr('WHMCS API URL') }}</label>
                            <input type="url" name="whmcs_api_url" class="form-control"
                                value="{{ get_setting('whmcs_api_url') }}"
                                placeholder="https://billing.yoursite.com/includes/api.php">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('API Identifier') }}</label>
                            <input type="text" name="whmcs_api_identifier" class="form-control"
                                value="{{ get_setting('whmcs_api_identifier') }}" placeholder="API identifier key">
                        </div>
                        <div class="form-group mb-0">
                            <label>{{ __tr('API Secret') }}</label>
                            <input type="password" name="whmcs_api_secret" class="form-control"
                                value="{{ get_setting('whmcs_api_secret') }}" placeholder="••••••••"
                                autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>{{ __tr('Save IPTV Settings') }}
            </button>
        </div>
    </form>
@endsection
