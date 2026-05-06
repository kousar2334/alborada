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
        <div class="card-body" style="padding:24px;">
            <form action="{{ route('reseller.clients.add') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label style="font-size:.8rem;color:var(--muted);margin-bottom:6px;display:block;">Full Name</label>
                        <input type="text" name="name" class="input-style"
                            placeholder="Client name" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <p class="invalid-feedback d-block">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label style="font-size:.8rem;color:var(--muted);margin-bottom:6px;display:block;">Email</label>
                        <input type="email" name="email" class="input-style"
                            placeholder="client@email.com" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <p class="invalid-feedback d-block">{{ $errors->first('email') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label style="font-size:.8rem;color:var(--muted);margin-bottom:6px;display:block;">Phone</label>
                        <input type="text" name="phone" class="input-style"
                            placeholder="+1234567890" value="{{ old('phone') }}">
                        @if ($errors->has('phone'))
                            <p class="invalid-feedback d-block">{{ $errors->first('phone') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label style="font-size:.8rem;color:var(--muted);margin-bottom:6px;display:block;">Password</label>
                        <input type="password" name="password" class="input-style" placeholder="Min 8 chars">
                        @if ($errors->has('password'))
                            <p class="invalid-feedback d-block">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="cmn-btn w-100" style="background:var(--green);color:#000;">
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
        <div class="card-body" style="padding:0;">
            @if ($clients->count())
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--border);">
                                <th style="padding:14px 20px;text-align:left;font-size:0.75rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">CLIENT</th>
                                <th style="padding:14px 20px;text-align:left;font-size:0.75rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">CONTACT</th>
                                <th style="padding:14px 20px;text-align:left;font-size:0.75rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">SUBSCRIPTION</th>
                                <th style="padding:14px 20px;text-align:left;font-size:0.75rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">STATUS</th>
                                <th style="padding:14px 20px;text-align:left;font-size:0.75rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">ADDED</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                @php $sub = $client->subscriptions->first(); @endphp
                                <tr style="border-bottom:1px solid var(--border);transition:.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:14px 20px;">
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <div style="width:36px;height:36px;border-radius:50%;background:rgba(0,212,106,0.1);color:#00d46a;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;flex-shrink:0;">
                                                {{ strtoupper(substr($client->name,0,1)) }}
                                            </div>
                                            <div>
                                                <div style="font-size:.88rem;font-weight:500;color:var(--text);">{{ $client->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:14px 20px;">
                                        <div style="font-size:.82rem;color:var(--muted);">{{ $client->email }}</div>
                                        <div style="font-size:.78rem;color:var(--muted-2);">{{ $client->phone }}</div>
                                    </td>
                                    <td style="padding:14px 20px;">
                                        @if($sub)
                                            <div style="font-size:.82rem;color:var(--text);">{{ $sub->plan->title ?? '—' }}</div>
                                            <div style="font-size:.75rem;color:var(--muted-2);">
                                                Expires {{ $sub->expires_at?->format('M d, Y') ?? '—' }}
                                            </div>
                                        @else
                                            <span style="font-size:.78rem;color:var(--muted-2);">No active plan</span>
                                        @endif
                                    </td>
                                    <td style="padding:14px 20px;">
                                        @if($client->status == config('settings.general_status.active'))
                                            <span class="badge-status active">Active</span>
                                        @else
                                            <span class="badge-status inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="padding:14px 20px;font-size:.78rem;color:var(--muted-2);">
                                        {{ $client->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                    <div style="padding:16px 20px;">
                        {{ $clients->links() }}
                    </div>
                @endif
            @else
                <div class="empty-activity" style="padding:48px 24px;">
                    <i class="fas fa-users empty-activity-icon"></i>
                    No clients yet. Add your first client above.
                </div>
            @endif
        </div>
    </div>

@endsection
