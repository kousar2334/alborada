@extends('backend.layouts.dashboard_layout')
@section('page-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __tr('Dashboard') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">{{ __tr('Dashboard') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- ===== Row 1: Live IPTV KPIs ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-satellite-dish"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Active Lines') }}</span>
                            <span class="info-box-number">{{ number_format($active_subscriptions) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Revenue This Month') }}</span>
                            <span class="info-box-number">{{ get_setting('currency_symbol','$') }}{{ number_format($monthly_revenue, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user-plus"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('New Members Today') }}</span>
                            <span class="info-box-number">{{ number_format($new_members_today) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hourglass-half"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Expiring in 7 Days') }}</span>
                            <span class="info-box-number">{{ number_format($expiring_soon) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Row 2: Operations KPIs ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-ticket"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Open Tickets') }}</span>
                            <span class="info-box-number">{{ number_format($pending_tickets) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-wifi"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Buffering Reports') }}</span>
                            <span class="info-box-number">{{ number_format($buffering_tickets) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-indigo elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Pending Payments') }}</span>
                            <span class="info-box-number">{{ number_format($pending_subscriptions) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-store"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Resellers') }}</span>
                            <span class="info-box-number">{{ number_format($total_resellers) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Row 3: Total Members ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Members') }}</span>
                            <span class="info-box-number">{{ number_format($total_members) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Revenue (All Time)') }}</span>
                            <span class="info-box-number">{{ get_setting('currency_symbol','$') }}{{ number_format($total_revenue, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ route('admin.subscriptions.index') }}" style="text-decoration:none;">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-lime elevation-1"><i class="fas fa-list-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __tr('Manage Subscriptions') }}</span>
                                <span class="info-box-number" style="font-size:.9rem;">{{ __tr('View All') }} →</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ route('admin.support.tickets') }}" style="text-decoration:none;">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-red elevation-1" style="background:#dc3545!important;"><i class="fas fa-headset"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __tr('Support Center') }}</span>
                                <span class="info-box-number" style="font-size:.9rem;">{{ __tr('View Tickets') }} →</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- ===== Charts ===== --}}
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('New Members (Last 12 Months)') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyMembersChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Monthly Revenue (Last 12 Months)') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyRevenueChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Recent Subscriptions ===== --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Recent Subscriptions') }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-primary">{{ __tr('View All') }}</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('Member') }}</th>
                                        <th>{{ __tr('Plan') }}</th>
                                        <th>{{ __tr('Amount') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th>{{ __tr('Expires') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_subscriptions as $sub)
                                        <tr>
                                            <td>{{ $sub->user?->name ?? '—' }}</td>
                                            <td>{{ $sub->plan?->title ?? '—' }}</td>
                                            <td>{{ format_amount($sub->amount) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($sub->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $sub->expires_at?->format('M d, Y') ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __tr('No subscriptions yet') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('New Members') }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('Joined') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latest_members as $member)
                                        <tr>
                                            <td>
                                                <div style="font-size:.85rem;font-weight:600;">{{ $member->name }}</div>
                                                <div style="font-size:.75rem;color:#6c757d;">{{ $member->email }}</div>
                                            </td>
                                            <td style="font-size:.75rem;color:#6c757d;">{{ $member->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">{{ __tr('No members') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/chart.js/chart.umd.min.js') }}"></script>
    <script>
        $(function() {
            new Chart(document.getElementById('monthlyMembersChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthly_labels) !!},
                    datasets: [{
                        label: '{{ __tr('New Members') }}',
                        data: {!! json_encode($monthly_members_data) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: true, tension: 0.4, pointBackgroundColor: '#28a745'
                    }]
                },
                options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
            });

            new Chart(document.getElementById('monthlyRevenueChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthly_labels) !!},
                    datasets: [{
                        label: '{{ __tr('Revenue') }}',
                        data: {!! json_encode($monthly_revenue_data) !!},
                        backgroundColor: 'rgba(220,53,69,0.7)',
                        borderColor: '#dc3545',
                        borderWidth: 1
                    }]
                },
                options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
            });
        });
    </script>
@endsection
