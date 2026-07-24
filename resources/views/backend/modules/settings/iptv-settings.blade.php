@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('IPTV Settings'))
@section('settings-description', __tr('Choose and configure your IPTV provider (Xtream Codes or 8K CMS) and the
    optional WHMCS billing sync.'))

@section('settings-content')
    <form action="{{ route('admin.system.settings.iptv.update') }}" method="POST">
        @csrf

        {{-- Master provider selection --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-sliders-h mr-2 text-primary"></i>{{ __tr('Provisioning') }}</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-lg-6 form-group mb-lg-0">
                        <label>{{ __tr('Active Streaming Provider') }}</label>
                        @php($activeProvider = get_setting('active_iptv_provider', 'xtream'))
                        <select name="active_iptv_provider" id="active_iptv_provider" class="form-control">
                            <option value="none" {{ $activeProvider === 'none' ? 'selected' : '' }}>
                                {{ __tr('None (disabled)') }}</option>
                            <option value="xtream" {{ $activeProvider === 'xtream' ? 'selected' : '' }}>
                                {{ __tr('Xtream Codes API') }}</option>
                            <option value="8k" {{ $activeProvider === '8k' ? 'selected' : '' }}>
                                {{ __tr('8K CMS API') }}</option>
                        </select>
                        <small class="text-muted">{{ __tr('Only one provider is used at a time.') }}</small>
                    </div>
                    <div class="col-lg-6 form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="iptv_provisioning_enabled"
                                name="iptv_provisioning_enabled" value="1"
                                {{ get_setting('iptv_provisioning_enabled', 0) ? 'checked' : '' }}>
                            <label class="custom-control-label"
                                for="iptv_provisioning_enabled">{{ __tr('Auto-provision accounts after payment') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            {{-- Xtream Codes --}}
            <div id="iptv-card-xtream" @class(['col-lg-6 mb-4', 'd-none' => $activeProvider !== 'xtream'])>
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-tv mr-2 text-primary"></i>{{ __tr('Xtream Codes API') }}</h6>
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

            {{-- 8K CMS --}}
            <div id="iptv-card-8k" @class(['col-lg-6 mb-4', 'd-none' => $activeProvider !== '8k'])>
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-broadcast-tower mr-2 text-info"></i>{{ __tr('8K CMS API') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __tr('8K CMS API URL') }}</label>
                            <input type="url" name="iptv_api_url" class="form-control"
                                value="{{ get_setting('iptv_api_url', 'https://8k.cms-only.ru/api/api.php') }}"
                                placeholder="https://8k.cms-only.ru/api/api.php">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Developer API Key') }}</label>
                            <input type="password" name="iptv_api_key" class="form-control"
                                value="{{ get_setting('iptv_api_key') }}" placeholder="••••••••"
                                autocomplete="new-password">
                        </div>
                        <div class="form-group mb-0">
                            <small class="form-text text-muted">
                                {{ __tr('After saving, sync the package list so it can be mapped to your pricing plans.') }}
                            </small>
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
                        <div class="form-group">
                            <label>{{ __tr('API Secret') }}</label>
                            <input type="password" name="whmcs_api_secret" class="form-control"
                                value="{{ get_setting('whmcs_api_secret') }}" placeholder="••••••••"
                                autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('IPTV Product ID') }}</label>
                            <input type="number" name="whmcs_product_id" class="form-control" min="0"
                                value="{{ get_setting('whmcs_product_id', 0) }}"
                                placeholder="{{ __tr('WHMCS product/service ID to order') }}">
                            <small class="form-text text-muted">{{ __tr('The WHMCS product an order is placed against when provisioning. Leave 0 to only sync the client record.') }}</small>
                        </div>
                        <div class="form-group mb-0">
                            <label>{{ __tr('Inbound Webhook Secret') }}</label>
                            <input type="password" name="whmcs_webhook_secret" class="form-control"
                                value="{{ get_setting('whmcs_webhook_secret') }}" placeholder="••••••••"
                                autocomplete="new-password">
                            <small class="form-text text-muted">
                                {{ __tr('Shared secret for WHMCS → app callbacks. Endpoint:') }}
                                <code>{{ url('/whmcs/webhook') }}</code>.
                                {{ __tr('Sign the raw body as HMAC-SHA256 in the X-WHMCS-Signature header.') }}
                            </small>
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

    {{-- 8K package sync (separate form — posts to a different endpoint) --}}
    <form action="{{ route('admin.system.settings.iptv.sync-packages') }}" method="POST"
        id="iptv-sync-8k" @class(['mt-3', 'd-none' => $activeProvider !== '8k'])>
        @csrf
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <small class="text-muted mb-2 mb-md-0">
                {{ __tr('Packages synced from 8K CMS:') }}
                <strong>{{ \App\Models\IptvPackage::count() }}</strong>
            </small>
            <button type="submit" class="btn btn-outline-info">
                <i class="fas fa-sync mr-1"></i>{{ __tr('Sync Packages from 8K') }}
            </button>
        </div>
    </form>

    {{-- Show only the active provider's configuration card --}}
    <script>
        (function () {
            var select = document.getElementById('active_iptv_provider');
            if (!select) return;

            var panels = {
                xtream: [document.getElementById('iptv-card-xtream')],
                '8k': [document.getElementById('iptv-card-8k'), document.getElementById('iptv-sync-8k')]
            };

            function syncPanels() {
                var active = select.value;
                Object.keys(panels).forEach(function (provider) {
                    panels[provider].forEach(function (el) {
                        if (el) el.classList.toggle('d-none', provider !== active);
                    });
                });
            }

            select.addEventListener('change', syncPanels);
            syncPanels();
        })();
    </script>
@endsection
