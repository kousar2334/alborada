@extends('backend.layouts.dashboard_layout')
@section('page-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">{{ __tr('Reports & Analytics') }}</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Date filter --}}
        <div class="card card-outline card-primary mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="form-inline">
                    <div class="form-group mr-2">
                        <label class="mr-1">From</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="form-group mr-2">
                        <label class="mr-1">To</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    <button class="btn btn-primary mr-2">Apply</button>
                    <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-success">
                        <i class="fas fa-download mr-1"></i> Export CSV
                    </a>
                </form>
            </div>
        </div>

        {{-- Summary stats --}}
        <div class="row">
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Revenue (period)</span>
                        <span class="info-box-number">${{ number_format($totalRevenue, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-crown"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Subscriptions</span>
                        <span class="info-box-number">{{ number_format($activeSubscriptions) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expiring in 7 Days</span>
                        <span class="info-box-number">{{ number_format($expiringSoon) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-ticket-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Open Tickets</span>
                        <span class="info-box-number">{{ number_format($pendingTickets) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Revenue chart --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Monthly Revenue (Last 12 Months)</h3></div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="height:280px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top plans --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Top Plans (Period)</h3></div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead><tr><th>Plan</th><th class="text-right">Sales</th></tr></thead>
                            <tbody>
                                @forelse($topPlans as $row)
                                <tr>
                                    <td>{{ $row->plan->title ?? 'N/A' }}</td>
                                    <td class="text-right">{{ $row->total }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subscribers chart --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Active vs Expired Subscribers (Last 12 Months)</h3></div>
            <div class="card-body">
                <canvas id="subscribersChart" style="height:260px;"></canvas>
            </div>
        </div>

        {{-- Expiring soon link --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Subscriptions Expiring Soon</h3>
                <a href="{{ route('admin.reports.expiring.soon') }}" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function() {
    // Revenue chart
    fetch('{{ route('admin.reports.revenue.chart') }}')
        .then(r => r.json())
        .then(d => {
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: d.labels,
                    datasets: [{ label: 'Revenue ($)', data: d.data, backgroundColor: 'rgba(40,167,69,0.7)', borderColor: '#28a745', borderWidth: 1 }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        });

    // Subscribers chart
    fetch('{{ route('admin.reports.subscribers.chart') }}')
        .then(r => r.json())
        .then(d => {
            new Chart(document.getElementById('subscribersChart'), {
                type: 'line',
                data: {
                    labels: d.labels,
                    datasets: [
                        { label: 'New Active', data: d.active, borderColor: '#007bff', backgroundColor: 'rgba(0,123,255,0.1)', fill: true, tension: 0.3 },
                        { label: 'Expired', data: d.expired, borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', fill: true, tension: 0.3 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        });
})();
</script>
@endpush
