@php
    $links = [
        ['title' => 'Resellers', 'route' => 'admin.resellers.index', 'active' => false],
        ['title' => "{$reseller->name} - Credit Logs", 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Credit Logs
@endsection
@section('page-content')
    <x-admin-page-header title="{{ $reseller->name }} — Credit Logs" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current Balance</span>
                            <span class="info-box-number">${{ number_format($reseller->credits, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Description</th>
                                <th>Client</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if ($log->type === 'credit')
                                            <span class="badge badge-success">Credit</span>
                                        @else
                                            <span class="badge badge-danger">Debit</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($log->amount, 2) }}</td>
                                    <td>${{ number_format($log->balance_after, 2) }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->client->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($logs->hasPages())
                    <div class="card-footer">{{ $logs->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection
