@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Reseller Settings'))
@section('settings-description',
    __tr('Turn the whole reseller module on or off — the reseller portal, the reseller
    REST API and the admin reseller management screens.'))

@section('settings-content')
    @php($resellerEnabled = reseller_system_enabled())

    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-store mr-2 text-primary"></i>{{ __tr('Reseller System') }}</h6>
        </div>
        <form action="{{ route('admin.system.settings.reseller.update') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group mb-0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="reseller_system_enabled" value="1" class="custom-control-input"
                            id="reseller_system_enabled" {{ $resellerEnabled ? 'checked' : '' }}>
                        <label class="custom-control-label" for="reseller_system_enabled">
                            <strong>{{ __tr('Enable Reseller System') }}</strong>
                            <small class="text-muted d-block">
                                {{ __tr('When disabled, every reseller URL returns 404 and all reseller entry points are hidden from the site and the admin menu.') }}
                            </small>
                        </label>
                    </div>
                </div>

                <hr>

                <p class="mb-2 font-weight-bold">{{ __tr('What gets switched off') }}</p>
                <ul class="mb-0 text-muted">
                    <li>{{ __tr('Reseller portal — login, registration, dashboard, clients, credits, API keys and tickets') }}
                    </li>
                    <li>{{ __tr('Reseller REST API endpoints under /api/reseller/v1') }}</li>
                    <li>{{ __tr('Admin reseller management and the reseller performance report') }}</li>
                    <li>{{ __tr('The reseller section and reseller login buttons on the homepage') }}</li>
                </ul>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>{{ __tr('Save Settings') }}
                </button>
            </div>
        </form>
    </div>

    @if ($resellerEnabled)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-users mr-2 text-info"></i>{{ __tr('Current Resellers') }}</h6>
            </div>
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <span class="badge badge-primary mr-1">{{ number_format($totalResellers) }}</span>
                    {{ __tr('total reseller accounts') }}
                    <span class="badge badge-warning ml-3 mr-1">{{ number_format($pendingResellers) }}</span>
                    {{ __tr('awaiting approval') }}
                </div>
                <a href="{{ route('admin.resellers.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt mr-1"></i>{{ __tr('Manage Resellers') }}
                </a>
            </div>
        </div>
    @else
        <div class="callout callout-warning">
            <h6 class="mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>{{ __tr('Reseller system is off') }}</h6>
            <p class="mb-0 text-muted">
                {{ __tr('Existing reseller accounts and their credit balances are kept untouched — resellers simply cannot sign in or use the API until you switch the system back on.') }}
            </p>
        </div>
    @endif
@endsection
