@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>My Clients - Reseller Portal</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title">My Clients</h1>
        <p class="dash-page-subtitle">Manage all customers under your reseller account.</p>
    </div>

    {{-- Add Client Form --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-plus me-2"></i> Add New Client</h3>
        </div>
        <div class="card-body card-body-padded">
            <form action="{{ route('reseller.clients.add') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-sm">Full Name</label>
                        <input type="text" name="name" class="input-style" placeholder="Client name"
                            value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <p class="invalid-feedback d-block">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Email</label>
                        <input type="email" name="email" class="input-style" placeholder="client@email.com"
                            value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Phone</label>
                        <input type="text" name="phone" class="input-style" placeholder="+1234567890"
                            value="{{ old('phone') }}">
                        @if ($errors->has('phone'))
                            <p class="invalid-feedback d-block">{{ $errors->first('phone') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Password</label>
                        <input type="password" name="password" class="input-style" placeholder="Min 8 chars">
                        @if ($errors->has('password'))
                            <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="cmn-btn cmn-btn-green w-100">
                            <i class="fas fa-plus"></i> Add Client
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Clients Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Clients ({{ $clients->total() }})</h3>
        </div>
        <div class="card-body p-0">
            @if ($clients->count())
                <div class="clients-table-wrap">
                    <table class="clients-table">
                        <thead>
                            <tr>
                                <th>CLIENT</th>
                                <th>CONTACT</th>
                                <th>SUBSCRIPTION</th>
                                <th>STATUS</th>
                                <th>ADDED</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                @php $sub = $client->subscriptions->first(); @endphp
                                <tr>
                                    <td>
                                        <div class="client-cell-name-wrap">
                                            <div class="client-avatar">
                                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="client-name">{{ $client->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="client-email">{{ $client->email }}</div>
                                        <div class="client-phone">{{ $client->phone }}</div>
                                    </td>
                                    <td>
                                        @if ($sub)
                                            <div class="client-sub-plan">{{ $sub->plan->title ?? '—' }}</div>
                                            <div class="client-sub-expiry">
                                                Expires {{ $sub->expires_at?->format('M d, Y') ?? '—' }}
                                            </div>
                                        @else
                                            <span class="client-no-plan">No active plan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($client->status == config('settings.general_status.active'))
                                            <span class="badge-status active">Active</span>
                                        @else
                                            <span class="badge-status inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="client-date">
                                        {{ $client->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($clients->hasPages())
                    <div class="clients-pagination">
                        {{ $clients->links() }}
                    </div>
                @endif
            @else
                <div class="empty-activity clients-empty">
                    <i class="fas fa-users empty-activity-icon"></i>
                    No clients yet. Add your first client above.
                </div>
            @endif
        </div>
    </div>

@endsection
