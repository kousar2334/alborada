@extends('backend.layouts.dashboard_layout')
@section('page-content')
    <!--Page Header-->
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
    <!--End page header-->

    <section class="content">
        <div class="container-fluid">

            {{-- ===== Row 1: Ads Stats ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ad"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Ads') }}</span>
                            <span class="info-box-number">{{ number_format($total_ads) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Active Ads') }}</span>
                            <span class="info-box-number">{{ number_format($active_ads) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Inactive Ads') }}</span>
                            <span class="info-box-number">{{ number_format($pending_ads) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-star"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Featured Ads') }}</span>
                            <span class="info-box-number">{{ number_format($featured_ads) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Row 2: Users & Platform Stats ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Members') }}</span>
                            <span class="info-box-number">{{ number_format($total_members) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-crown"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Active Subscriptions') }}</span>
                            <span class="info-box-number">{{ number_format($active_subs) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-flag"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Ad Reports') }}</span>
                            <span class="info-box-number">{{ number_format($total_reports) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-envelope"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Unread Messages') }}</span>
                            <span class="info-box-number">{{ number_format($unread_messages) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Row 3: Content Stats ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-th-large"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Categories') }}</span>
                            <span class="info-box-number">{{ number_format($total_categories) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-indigo elevation-1"><i class="fas fa-blog"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Blogs') }}</span>
                            <span class="info-box-number">{{ number_format($total_blogs) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Pages') }}</span>
                            <span class="info-box-number">{{ number_format($total_page) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-lime elevation-1"><i class="fas fa-bookmark"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Saved Ads') }}</span>
                            <span class="info-box-number">{{ number_format($total_saved_ads) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Charts Row 1 ===== --}}
            <div class="row">
                <div class="col-md-8 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Monthly Ad Posts (Last 12 Months)') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyAdsChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Ads by Category') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryDoughnutChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Charts Row 2 ===== --}}
            <div class="row">
                <div class="col-md-8 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('New Members (Last 12 Months)') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyMembersChart" style="height:240px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Overview Stats') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="overviewPieChart" style="height:240px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Latest Ads & Members ===== --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Latest Ads') }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Category') }}</th>
                                        <th>{{ __tr('User') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latest_ads as $ad)
                                        <tr>
                                            <td>{{ Str::limit($ad->title, 25) ?? 'N/A' }}</td>
                                            <td>{{ $ad->categoryInfo->title ?? 'N/A' }}</td>
                                            <td>{{ $ad->userInfo->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($ad->status == $active)
                                                    <span class="badge badge-success">{{ __tr('Active') }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ __tr('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $ad->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __tr('No ads found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Latest Members') }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('Email') }}</th>
                                        <th>{{ __tr('Phone') }}</th>
                                        <th>{{ __tr('Joined Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latest_members as $member)
                                        <tr>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->email }}</td>
                                            <td>{{ $member->phone ?? 'N/A' }}</td>
                                            <td>{{ $member->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __tr('No members found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Reports & Messages ===== --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ __tr('Recent Ad Reports') }}
                                @if ($total_reports > 0)
                                    <span class="badge badge-danger ml-2">{{ $total_reports }}</span>
                                @endif
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('Ad') }}</th>
                                        <th>{{ __tr('Reported By') }}</th>
                                        <th>{{ __tr('Reason') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latest_reports as $report)
                                        <tr>
                                            <td>{{ Str::limit($report->ad?->title ?? 'N/A', 20) }}</td>
                                            <td>{{ $report->user?->name ?? 'N/A' }}</td>
                                            <td>{{ $report->reason?->title ?? 'N/A' }}</td>
                                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __tr('No reports found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ __tr('Unread Contact Messages') }}
                                @if ($unread_messages > 0)
                                    <span class="badge badge-warning ml-2">{{ $unread_messages }}</span>
                                @endif
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('Email') }}</th>
                                        <th>{{ __tr('Subject') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latest_messages as $msg)
                                        <tr>
                                            <td>{{ $msg->name }}</td>
                                            <td>{{ $msg->email }}</td>
                                            <td>{{ Str::limit($msg->subject, 25) }}</td>
                                            <td>{{ $msg->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                {{ __tr('No unread messages') }}</td>
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
            const chartColors = [
                '#17a2b8', '#28a745', '#ffc107', '#dc3545',
                '#6610f2', '#fd7e14', '#20c997', '#e83e8c'
            ];

            // Monthly Ads Bar Chart
            new Chart(document.getElementById('monthlyAdsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthly_labels) !!},
                    datasets: [{
                        label: '{{ __tr('Ads Posted') }}',
                        data: {!! json_encode($monthly_data) !!},
                        backgroundColor: '#17a2b8',
                        borderColor: '#138496',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Ads by Category Doughnut
            new Chart(document.getElementById('categoryDoughnutChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($category_labels) !!},
                    datasets: [{
                        data: {!! json_encode($category_data) !!},
                        backgroundColor: chartColors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Monthly Members Line Chart
            new Chart(document.getElementById('monthlyMembersChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthly_labels) !!},
                    datasets: [{
                        label: '{{ __tr('New Members') }}',
                        data: {!! json_encode($monthly_members_data) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#28a745'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Overview Pie Chart
            new Chart(document.getElementById('overviewPieChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [
                        '{{ __tr('Total Ads') }}',
                        '{{ __tr('Members') }}',
                        '{{ __tr('Blogs') }}',
                        '{{ __tr('Pages') }}'
                    ],
                    datasets: [{
                        data: [
                            {{ $total_ads }},
                            {{ $total_members }},
                            {{ $total_blogs }},
                            {{ $total_page }}
                        ],
                        backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endsection
