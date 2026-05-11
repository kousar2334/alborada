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

            {{-- ===== Row 1: Platform Stats ===== --}}
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
                            <span class="info-box-number">{{ number_format($total_pages) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Row 2: Content & Messages ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-photo-video"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Media') }}</span>
                            <span class="info-box-number">{{ number_format($total_media) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Total Menus') }}</span>
                            <span class="info-box-number">{{ number_format($total_menus) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-lime elevation-1"><i class="fas fa-tags"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Blog Categories') }}</span>
                            <span class="info-box-number">{{ number_format($total_categories) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Row 3: IPTV Stats ===== --}}
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-crown"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Active Subscriptions') }}</span>
                            <span class="info-box-number">{{ number_format($active_subscriptions) }}</span>
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
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Revenue This Month') }}</span>
                            <span class="info-box-number">${{ number_format($monthly_revenue, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-ticket-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __tr('Open Tickets') }}</span>
                            <span class="info-box-number">{{ number_format($pending_tickets) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Charts Row 1 ===== --}}
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
                            <h3 class="card-title">{{ __tr('New Blogs (Last 12 Months)') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyBlogsChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Charts Row 2 ===== --}}
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Blogs by Category') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryDoughnutChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Overview Stats') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="overviewPieChart" style="height:280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
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
                                            <td colspan="4" class="text-center">{{ __tr('No members found') }}</td>
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

            // Monthly Blogs Line Chart
            new Chart(document.getElementById('monthlyBlogsChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthly_labels) !!},
                    datasets: [{
                        label: '{{ __tr('New Blogs') }}',
                        data: {!! json_encode($monthly_blogs_data) !!},
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#007bff'
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

            // Category Doughnut Chart
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($category_labels) !!},
                    datasets: [{
                        data: {!! json_encode($category_data) !!},
                        backgroundColor: chartColors,
                        borderWidth: 1
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

            // Monthly Blogs Line Chart
            new Chart(document.getElementById('monthlyBlogsChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthly_labels) !!},
                    datasets: [{
                        label: '{{ __tr('New Blogs') }}',
                        data: {!! json_encode($monthly_blogs_data) !!},
                        borderColor: '#6610f2',
                        backgroundColor: 'rgba(102,16,242,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#6610f2'
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

            // Blogs by Category Doughnut
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

            // Overview Pie Chart
            new Chart(document.getElementById('overviewPieChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [
                        '{{ __tr('Members') }}',
                        '{{ __tr('Blogs') }}',
                        '{{ __tr('Pages') }}',
                        '{{ __tr('Media') }}'
                    ],
                    datasets: [{
                        data: [
                            {{ $total_members }},
                            {{ $total_blogs }},
                            {{ $total_pages }},
                            {{ $total_media }}
                        ],
                        backgroundColor: ['#28a745', '#6610f2', '#dc3545', '#17a2b8']
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
