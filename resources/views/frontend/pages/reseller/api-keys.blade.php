@extends('frontend.layouts.reseller-dashboard')
@section('dashboard-content')
<div class="container-fluid py-4">
    <h4 class="mb-4">{{ __tr('API Access Keys') }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('new_token'))
        <div class="alert alert-warning">
            <strong>{{ __tr('Your new API token (copy it now — shown only once):') }}</strong>
            <div class="mt-2">
                <code class="user-select-all d-block p-2 bg-dark text-light rounded" id="new-token">{{ session('new_token') }}</code>
            </div>
            <button class="btn btn-sm btn-light mt-2" onclick="navigator.clipboard.writeText(document.getElementById('new-token').innerText)">
                {{ __tr('Copy') }}
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">{{ __tr('Create New Token') }}</h5></div>
        <div class="card-body">
            <form action="{{ route('reseller.api.keys.create') }}" method="POST" class="form-inline">
                @csrf
                <input type="text" name="token_name" class="form-control mr-2" placeholder="{{ __tr('Token name (e.g. My App)') }}" required>
                <button type="submit" class="btn btn-primary">{{ __tr('Generate Token') }}</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __tr('Active Tokens') }}</h5></div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr><th>{{ __tr('Name') }}</th><th>{{ __tr('Last Used') }}</th><th>{{ __tr('Created') }}</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                    <tr>
                        <td>{{ $token->name }}</td>
                        <td>{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : __tr('Never') }}</td>
                        <td>{{ $token->created_at->format('M d, Y') }}</td>
                        <td>
                            <form action="{{ route('reseller.api.keys.revoke') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="token_id" value="{{ $token->id }}">
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('{{ __tr('Revoke this token?') }}')">
                                    {{ __tr('Revoke') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">{{ __tr('No tokens found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header"><h5 class="mb-0">{{ __tr('API Documentation') }}</h5></div>
        <div class="card-body">
            <p class="mb-1"><strong>{{ __tr('Base URL:') }}</strong> <code>{{ url('/api/reseller/v1') }}</code></p>
            <p class="mb-1"><strong>{{ __tr('Authentication:') }}</strong> {{ __tr('Pass your token in the Authorization header:') }}
                <code>Authorization: Bearer YOUR_TOKEN</code></p>
            <hr>
            <ul class="mb-0">
                <li><code>GET /plans</code> — {{ __tr('List available plans') }}</li>
                <li><code>GET /credits</code> — {{ __tr('Get your credit balance') }}</li>
                <li><code>GET /clients</code> — {{ __tr('List your clients') }}</li>
                <li><code>POST /clients/create</code> — {{ __tr('Create a new client') }} (name, email, plan_id)</li>
                <li><code>POST /clients/suspend</code> — {{ __tr('Suspend a client') }} (client_id)</li>
                <li><code>POST /clients/reactivate</code> — {{ __tr('Reactivate a client') }} (client_id)</li>
            </ul>
        </div>
    </div>
</div>
@endsection
