@extends('frontend.layouts.reseller-dashboard')
@section('dashboard-content')
<div class="container-fluid py-4">
    <h4 class="mb-4">{{ __tr('Credit Balance') }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-success">${{ number_format($reseller->credits, 2) }}</h2>
                    <p class="text-muted mb-0">{{ __tr('Available Credits') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __tr('Transaction History') }}</h5></div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __tr('Date') }}</th>
                        <th>{{ __tr('Type') }}</th>
                        <th>{{ __tr('Amount') }}</th>
                        <th>{{ __tr('Balance After') }}</th>
                        <th>{{ __tr('Description') }}</th>
                        <th>{{ __tr('Client') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($log->type === 'credit')
                                <span class="badge badge-success">{{ __tr('Credit') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __tr('Debit') }}</span>
                            @endif
                        </td>
                        <td>${{ number_format($log->amount, 2) }}</td>
                        <td>${{ number_format($log->balance_after, 2) }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->client->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">{{ __tr('No transactions yet.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
