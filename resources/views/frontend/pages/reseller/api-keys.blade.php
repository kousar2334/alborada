@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>API Keys - Reseller Portal</title>
    <style>
        .api-token-form .input-group .form-control {
            border-right: none;
            border-radius: 9px 0 0 9px;
            font-size: .875rem;
            padding: .65rem 1rem;
            border-color: #e5e7eb;
        }

        .api-token-form .input-group .form-control:focus {
            border-color: #00c853;
            box-shadow: none;
            border-right: none;
        }

        .api-token-form .input-group-append .btn {
            border-radius: 0 9px 9px 0;
            font-weight: 600;
            font-size: .875rem;
            padding: .65rem 1.4rem;
            background: linear-gradient(135deg, #00a046 0%, #00c853 100%);
            border: none;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0, 200, 83, .2);
            transition: opacity .18s, box-shadow .18s;
        }

        .api-token-form .input-group-append .btn:hover {
            opacity: .9;
            box-shadow: 0 4px 14px rgba(0, 200, 83, .3);
        }

        .api-token-table thead th {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            background: #fafffe;
            border-bottom: 1px solid #e9f5ef;
            padding: .875rem 1.25rem;
        }

        .api-token-table tbody td {
            padding: .9rem 1.25rem;
            vertical-align: middle;
            font-size: .875rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }

        .api-token-table tbody tr:last-child td {
            border-bottom: none;
        }

        .api-token-table tbody tr:hover td {
            background: #f9fffe;
        }

        .token-name-cell {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 600;
            color: #1f2937;
        }

        .token-name-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: rgba(0, 200, 83, .1);
            color: #00a046;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            flex-shrink: 0;
        }

        .token-date {
            font-size: .8rem;
            color: #6b7280;
        }

        .token-revoke-btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .85rem;
            background: rgba(239, 68, 68, .08);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, .2);
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }

        .token-revoke-btn:hover {
            background: rgba(239, 68, 68, .14);
            border-color: rgba(239, 68, 68, .35);
        }

        .api-doc-section {
            padding: 1.25rem;
        }

        .api-doc-base {
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: .4rem;
            margin-bottom: .6rem;
            font-size: .875rem;
        }

        .api-doc-base strong {
            color: #1f2937;
            flex-shrink: 0;
        }

        .api-doc-url {
            color: #00a046;
            font-family: monospace;
            font-size: .85rem;
            background: #f0fdf4;
            padding: 2px 8px;
            border-radius: 5px;
            border: 1px solid #d1fae5;
        }

        .api-auth-code {
            font-family: monospace;
            font-size: .82rem;
            background: #fffbeb;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 5px;
            border: 1px solid #fde68a;
        }

        .api-doc-divider {
            border: none;
            border-top: 1px solid #f0fdf4;
            margin: 1rem 0;
        }

        .api-endpoint-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .api-endpoint-item {
            display: flex;
            align-items: baseline;
            gap: .75rem;
            font-size: .85rem;
            color: #374151;
        }

        .api-method {
            font-family: monospace;
            font-size: .75rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            flex-shrink: 0;
            min-width: 38px;
            text-align: center;
        }

        .api-method.get {
            background: rgba(59, 130, 246, .1);
            color: #2563eb;
        }

        .api-method.post {
            background: rgba(0, 200, 83, .1);
            color: #00a046;
        }

        .api-path {
            font-family: monospace;
            font-size: .82rem;
            color: #1f2937;
            font-weight: 600;
            flex-shrink: 0;
        }

        .api-desc {
            color: #6b7280;
            font-size: .8rem;
        }

        .new-token-alert {
            border-radius: 12px;
            border: 1px solid rgba(245, 158, 11, .3);
            background: rgba(255, 251, 235, 1);
            padding: 1.1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .new-token-alert strong {
            color: #92400e;
            font-size: .875rem;
        }

        .new-token-code {
            display: block;
            font-family: monospace;
            font-size: .85rem;
            background: #1f2937;
            color: #d1fae5;
            padding: .85rem 1rem;
            border-radius: 8px;
            margin-top: .75rem;
            word-break: break-all;
        }

        .copy-token-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-top: .75rem;
            padding: .42rem .95rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }

        .copy-token-btn:hover {
            background: #f0fdf4;
            border-color: rgba(0, 200, 83, .35);
            color: #00a046;
        }

        .no-tokens-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #9ca3af;
        }

        .no-tokens-empty i {
            font-size: 2rem;
            margin-bottom: .75rem;
            display: block;
            color: #d1d5db;
        }

        .no-tokens-empty p {
            font-size: .875rem;
            margin: 0;
        }
    </style>
@endsection
@section('reseller-content')
    <div class="container-fluid py-4">

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

    </div>
@endsection
