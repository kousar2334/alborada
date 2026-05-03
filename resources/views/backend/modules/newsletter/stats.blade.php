@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Campaign Stats — {{ $campaign->subject }}
@endsection
@section('page-content')
    <x-admin-page-header title="Campaign Stats" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Summary Cards --}}
            <div class="row mb-3">
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="fas fa-paper-plane"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Sent</span>
                            <span class="info-box-number">{{ number_format($campaign->total_sent) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-primary"><i class="fas fa-envelope-open"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Opened</span>
                            <span class="info-box-number">
                                {{ number_format($campaign->total_opened) }}
                                <small class="font-weight-normal text-muted">({{ $campaign->open_rate }}%)</small>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="fas fa-mouse-pointer"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Clicked</span>
                            <span class="info-box-number">
                                {{ number_format($campaign->total_clicked) }}
                                <small class="font-weight-normal text-muted">({{ $campaign->click_rate }}%)</small>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Sent At</span>
                            <span class="info-box-number" style="font-size:13px;line-height:1.4;">
                                {{ $campaign->sent_at ? $campaign->sent_at->format('d M Y H:i') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart --}}
            @if ($dates->isNotEmpty())
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Opens &amp; Clicks Over Time</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="campaignStatsChart" height="80"></canvas>
                    </div>
                </div>
            @endif

            {{-- Subscriber Detail Table --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recipient Details</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th class="text-center">Opened</th>
                                <th class="text-center">Clicked</th>
                                <th>Opened At</th>
                                <th>Clicked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($campaign->stats as $idx => $stat)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $stat->subscriber->email ?? '—' }}</td>
                                    <td class="text-center">
                                        @if ($stat->opened_at)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($stat->clicked_at)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $stat->opened_at ? $stat->opened_at->format('d M Y H:i') : '—' }}</td>
                                    <td>{{ $stat->clicked_at ? $stat->clicked_at->format('d M Y H:i') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recipient data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
@section('page-script')
    @if ($dates->isNotEmpty())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                var ctx = document.getElementById('campaignStatsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($dates->values()),
                        datasets: [{
                                label: 'Opens',
                                data: @json($opensData->values()),
                                backgroundColor: 'rgba(23,162,184,0.7)',
                            },
                            {
                                label: 'Clicks',
                                data: @json($clicksData->values()),
                                backgroundColor: 'rgba(40,167,69,0.7)',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                        },
                    },
                });
            }());
        </script>
    @endif
@endsection
