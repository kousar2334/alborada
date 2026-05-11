@extends('backend.layouts.dashboard_layout')
@section('page-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Expiring Subscriptions</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Expiring Soon</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Subscriptions expiring within {{ $days }} days</h3>
                <div>
                    <a href="{{ route('admin.reports.expiring.soon', ['days' => 7]) }}" class="btn btn-sm {{ $days == 7 ? 'btn-warning' : 'btn-outline-warning' }}">7 days</a>
                    <a href="{{ route('admin.reports.expiring.soon', ['days' => 30]) }}" class="btn btn-sm {{ $days == 30 ? 'btn-warning' : 'btn-outline-warning' }}">30 days</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Customer</th>
                            <th>Plan</th>
                            <th>Expires At</th>
                            <th>Days Left</th>
                            <th>Auto-Renew</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                        <tr>
                            <td>
                                <strong>{{ $sub->user->name }}</strong><br>
                                <small class="text-muted">{{ $sub->user->email }}</small>
                            </td>
                            <td>{{ $sub->plan->title ?? 'N/A' }}</td>
                            <td>{{ $sub->expires_at?->format('M d, Y') }}</td>
                            <td>
                                @php $days_left = now()->diffInDays($sub->expires_at, false); @endphp
                                <span class="badge badge-{{ $days_left <= 3 ? 'danger' : 'warning' }}">
                                    {{ $days_left }} day{{ $days_left != 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                @if($sub->auto_renew)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="btn btn-xs btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No subscriptions expiring in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($subscriptions->hasPages())
            <div class="card-footer">{{ $subscriptions->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection
