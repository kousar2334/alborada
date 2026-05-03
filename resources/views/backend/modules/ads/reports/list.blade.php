@php
    $links = [['title' => 'Ad Reports', 'route' => 'classified.ads.reports.list', 'active' => false]];
    $statuses = [0 => 'Pending', 1 => 'Reviewed', 2 => 'Dismissed'];
    $statusBadges = [0 => 'badge-warning', 1 => 'badge-success', 2 => 'badge-secondary'];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Ad Reports') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Ad Reports" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Filters --}}
            <div class="card card-outline card-primary mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('classified.ads.reports.list') }}"
                        class="form-inline flex-wrap gap-2">
                        <div class="form-group mr-2 mb-2">
                            <label class="mr-1">{{ __tr('Reason') }}</label>
                            <select name="reason_id" class="form-control form-control-sm">
                                <option value="">{{ __tr('All Reasons') }}</option>
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason->id }}"
                                        {{ request('reason_id') == $reason->id ? 'selected' : '' }}>
                                        {{ $reason->translation('title') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2 mb-2">
                            <label class="mr-1">{{ __tr('Status') }}</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">{{ __tr('All Statuses') }}</option>
                                @foreach ($statuses as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ request('status') !== null && request('status') == $val ? 'selected' : '' }}>
                                        {{ __tr($label) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mb-2">{{ __tr('Filter') }}</button>
                        <a href="{{ route('classified.ads.reports.list') }}"
                            class="btn btn-secondary btn-sm mb-2 ml-1">{{ __tr('Reset') }}</a>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h3 class="card-title">{{ __tr('Reported Ads') }}</h3>
                            <span class="badge badge-danger">{{ $reports->total() }} {{ __tr('Reports') }}</span>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Ad') }}</th>
                                        <th>{{ __tr('Reported By') }}</th>
                                        <th>{{ __tr('Reason') }}</th>
                                        <th>{{ __tr('Message') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                        <th class="text-right">{{ __tr('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reports as $key => $report)
                                        <tr>
                                            <td>{{ $reports->firstItem() + $key }}</td>
                                            <td>
                                                @if ($report->ad)
                                                    <a href="{{ route('ad.details.page', $report->ad->uid) }}"
                                                        target="_blank">
                                                        {{ Str::limit($report->ad->title, 40) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ __tr('Ad deleted') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $report->user?->name ?? __tr('Guest') }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $report->reason ? $report->reason->translation('title') : '—' }}
                                                </span>
                                            </td>
                                            <td>{{ $report->message ? Str::limit($report->message, 60) : '—' }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $statusBadges[$report->status] ?? 'badge-warning' }}">
                                                    {{ __tr($statuses[$report->status] ?? 'Pending') }}
                                                </span>
                                            </td>
                                            <td>{{ $report->created_at->format('d M Y') }}</td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-default">{{ __tr('Action') }}</button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-default dropdown-toggle dropdown-hover dropdown-icon"
                                                        data-toggle="dropdown" aria-expanded="false"></button>
                                                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                        @if ($report->status != 1)
                                                            <button class="dropdown-item update-status-btn"
                                                                data-id="{{ $report->id }}" data-status="1">
                                                                {{ __tr('Mark as Reviewed') }}
                                                            </button>
                                                        @endif
                                                        @if ($report->status != 2)
                                                            <button class="dropdown-item update-status-btn"
                                                                data-id="{{ $report->id }}" data-status="2">
                                                                {{ __tr('Dismiss') }}
                                                            </button>
                                                        @endif
                                                        @if ($report->status != 0)
                                                            <button class="dropdown-item update-status-btn"
                                                                data-id="{{ $report->id }}" data-status="0">
                                                                {{ __tr('Mark as Pending') }}
                                                            </button>
                                                        @endif
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item text-danger delete-report-btn"
                                                            data-id="{{ $report->id }}">
                                                            {{ __tr('Delete') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-center py-3">{{ __tr('No reports found') }}</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($reports->hasPages())
                                <div class="p-3">
                                    {{ $reports->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div class="modal fade" id="delete-report-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1 h6 my-2">{{ __tr('Are you sure to delete this report?') }}</p>
                        <form method="POST" action="{{ route('classified.ads.reports.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-report-id" name="id">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Update Form (hidden) --}}
        <form id="status-update-form" method="POST" action="{{ route('classified.ads.reports.status') }}"
            style="display:none;">
            @csrf
            <input type="hidden" id="status-report-id" name="id">
            <input type="hidden" id="status-report-value" name="status">
        </form>
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

            $('.delete-report-btn').on('click', function() {
                $('#delete-report-id').val($(this).data('id'));
                $('#delete-report-modal').modal('show');
            });

            $('.update-status-btn').on('click', function() {
                $('#status-report-id').val($(this).data('id'));
                $('#status-report-value').val($(this).data('status'));
                $('#status-update-form').submit();
            });
        })(jQuery);
    </script>
@endsection
