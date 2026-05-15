@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>API Keys - Reseller Portal</title>
@endsection
@section('reseller-content')
    <div class="dashboard-header">
        <div>
            <h1 class="dash-page-title">{{ __tr('API Access Keys') }}</h1>
            <p class="dash-page-subtitle">{{ __tr('Manage tokens to access the Reseller API') }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4 rounded-lg">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('new_token'))
        <div class="new-token-alert mb-4">
            <strong><i class="fas fa-triangle-exclamation me-1"></i>
                {{ __tr('Your new API token — copy it now, it won\'t be shown again:') }}</strong>
            <code class="new-token-code" id="new-token">{{ session('new_token') }}</code>
            <button class="copy-token-btn"
                onclick="navigator.clipboard.writeText(document.getElementById('new-token').innerText).then(()=>this.innerHTML='<i class=\'fas fa-check\'></i> Copied!')">
                <i class="fas fa-copy"></i> {{ __tr('Copy Token') }}
            </button>
        </div>
    @endif

    {{-- Create Token --}}
    <div class="dashboard-card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plus-circle"></i>
                {{ __tr('Create New Token') }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reseller.api.keys.create') }}" method="POST" class="api-token-form">
                @csrf
                <div class="input-group">
                    <input type="text" name="token_name" class="form-control"
                        placeholder="{{ __tr('Token name (e.g. My App)') }}" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn">
                            <i class="fas fa-key"></i> {{ __tr('Generate Token') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Active Tokens --}}
    <div class="dashboard-card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-key"></i>
                {{ __tr('Active Tokens') }}
            </h3>
            <span class="badge-status active">{{ $tokens->count() }} {{ __tr('token(s)') }}</span>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0 api-token-table">
                <thead>
                    <tr>
                        <th>{{ __tr('Name') }}</th>
                        <th>{{ __tr('Last Used') }}</th>
                        <th>{{ __tr('Created') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                        <tr>
                            <td>
                                <div class="token-name-cell">
                                    <div class="token-name-icon"><i class="fas fa-key"></i></div>
                                    {{ $token->name }}
                                </div>
                            </td>
                            <td class="token-date">
                                {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : __tr('Never') }}
                            </td>
                            <td class="token-date">{{ $token->created_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('reseller.api.keys.revoke') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="token_id" value="{{ $token->id }}">
                                    <button type="submit" class="token-revoke-btn"
                                        onclick="return confirm('{{ __tr('Revoke this token?') }}')">
                                        <i class="fas fa-trash-can"></i> {{ __tr('Revoke') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="no-tokens-empty">
                                    <i class="fas fa-key"></i>
                                    <p>{{ __tr('No tokens yet. Create one above to get started.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- API Documentation --}}
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-book"></i>
                {{ __tr('API Documentation') }}
            </h3>
        </div>
        <div class="api-doc-section">
            <div class="api-doc-base">
                <strong>{{ __tr('Base URL:') }}</strong>
                <span class="api-doc-url">{{ url('/api/reseller/v1') }}</span>
            </div>
            <div class="api-doc-base">
                <strong>{{ __tr('Authentication:') }}</strong>
                <span class="text-primary">{{ __tr('Pass your token in the Authorization header:') }}</span>
                <span class="api-auth-code">Authorization: Bearer YOUR_TOKEN</span>
            </div>
            <hr class="api-doc-divider">
            <ul class="api-endpoint-list">
                <li class="api-endpoint-item">
                    <span class="api-method get">GET</span>
                    <span class="api-path">/plans</span>
                    <span class="api-desc">{{ __tr('List available plans') }}</span>
                </li>
                <li class="api-endpoint-item">
                    <span class="api-method get">GET</span>
                    <span class="api-path">/credits</span>
                    <span class="api-desc">{{ __tr('Get your credit balance') }}</span>
                </li>
                <li class="api-endpoint-item">
                    <span class="api-method get">GET</span>
                    <span class="api-path">/clients</span>
                    <span class="api-desc">{{ __tr('List your clients') }}</span>
                </li>
                <li class="api-endpoint-item">
                    <span class="api-method post">POST</span>
                    <span class="api-path">/clients/create</span>
                    <span class="api-desc">{{ __tr('Create a new client') }} <em>(name, email, plan_id)</em></span>
                </li>
                <li class="api-endpoint-item">
                    <span class="api-method post">POST</span>
                    <span class="api-path">/clients/suspend</span>
                    <span class="api-desc">{{ __tr('Suspend a client') }} <em>(client_id)</em></span>
                </li>
                <li class="api-endpoint-item">
                    <span class="api-method post">POST</span>
                    <span class="api-path">/clients/reactivate</span>
                    <span class="api-desc">{{ __tr('Reactivate a client') }} <em>(client_id)</em></span>
                </li>
            </ul>
        </div>
    </div>
@endsection
