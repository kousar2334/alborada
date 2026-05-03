@php
    $links = [
        ['title' => 'Advertisement', 'route' => route('admin.advertisement.list'), 'active' => false],
        ['title' => "Analytics: {$ad->title}", 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Ad Analytics — {{ $ad->title }}
@endsection
@section('page-content')
    <x-admin-page-header title="Ad Analytics" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Summary Cards --}}
            <div class="row mb-3">
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="fas fa-eye"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Views</span>
                            <span class="info-box-number">{{ number_format($ad->total_impressions) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="fas fa-mouse-pointer"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Clicks</span>
                            <span class="info-box-number">{{ number_format($ad->total_clicks) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">CTR</span>
                            <span class="info-box-number">
                                @if ($ad->total_impressions > 0)
                                    {{ number_format(($ad->total_clicks / $ad->total_impressions) * 100, 2) }}%
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-map-marker-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Position</span>
                            <span class="info-box-number" style="font-size:14px;line-height:1.4;">
                                {{ str_replace('_', ' ', ucfirst($ad->position)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Date Range Switcher --}}
            <div class="row mb-2">
                <div class="col-12 d-flex align-items-center">
                    <span class="mr-2 font-weight-bold">Period:</span>
                    @foreach ([7 => '7 days', 14 => '14 days', 30 => '30 days', 90 => '90 days'] as $d => $label)
                        <a href="{{ route('admin.advertisement.analytics', [$ad->id, 'days' => $d]) }}"
                            class="btn btn-sm mr-1 {{ $days == $d ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Chart --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Views &amp; Clicks — last {{ $days }} days</h3>
                </div>
                <div class="card-body">
                    <canvas id="adAnalyticsChart" height="80"></canvas>
                </div>
            </div>

            {{-- Daily breakdown table --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Daily Breakdown</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Views</th>
                                <th class="text-center">Clicks</th>
                                <th class="text-center">CTR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = count($dates) - 1; $i >= 0; $i--)
                                @php
                                    $imp = $impressions[$i];
                                    $clk = $clicks[$i];
                                    $ctr = $imp > 0 ? number_format(($clk / $imp) * 100, 2) . '%' : '—';
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dates[$i])->format('d M Y') }}</td>
                                    <td class="text-center">{{ number_format($imp) }}</td>
                                    <td class="text-center">{{ number_format($clk) }}</td>
                                    <td class="text-center">{{ $ctr }}</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            var ctx = document.getElementById('adAnalyticsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($dates),
                    datasets: [{
                            label: 'Views',
                            data: @json($impressions),
                            borderColor: 'rgba(23,162,184,1)',
                            backgroundColor: 'rgba(23,162,184,0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3,
                        },
                        {
                            label: 'Clicks',
                            data: @json($clicks),
                            borderColor: 'rgba(40,167,69,1)',
                            backgroundColor: 'rgba(40,167,69,0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3,
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
                        x: {
                            ticks: {
                                maxTicksLimit: 10
                            }
                        },
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
@endsection
