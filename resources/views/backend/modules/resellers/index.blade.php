@php
    $links = [['title' => 'Resellers', 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title') Resellers @endsection
@section('page-content')
<x-admin-page-header title="Reseller Management" :links="$links" />
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form class="form-inline" method="GET">
                    <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Search name/email..." value="{{ request('q') }}">
                    <button class="btn btn-sm btn-default" type="submit">Search</button>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Email</th><th>Clients</th>
                            <th>Credits</th><th>Markup %</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resellers as $reseller)
                        <tr>
                            <td>{{ $reseller->id }}</td>
                            <td>{{ $reseller->name }}</td>
                            <td>{{ $reseller->email }}</td>
                            <td>{{ $reseller->reseller_clients_count }}</td>
                            <td><strong>${{ number_format($reseller->credits, 2) }}</strong></td>
                            <td>{{ $reseller->markup_percentage }}%</td>
                            <td>
                                <button class="btn btn-sm btn-success" data-toggle="modal"
                                    data-target="#top-up-modal" data-reseller-id="{{ $reseller->id }}"
                                    data-reseller-name="{{ $reseller->name }}">
                                    Top Up
                                </button>
                                <a href="{{ route('admin.resellers.credit.logs', $reseller->id) }}" class="btn btn-sm btn-default">
                                    Logs
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4">No resellers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($resellers->hasPages())
                <div class="card-footer">{{ $resellers->withQueryString()->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>

    {{-- Top-up modal --}}
    <div class="modal fade" id="top-up-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="{{ route('admin.resellers.top.up') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Credits</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="reseller_id" id="top-up-reseller-id">
                        <p class="mb-2">Adding credits to: <strong id="top-up-reseller-name"></strong></p>
                        <div class="form-group">
                            <label>Amount ($)</label>
                            <input type="number" name="amount" class="form-control" min="0.01" step="0.01" placeholder="50.00">
                        </div>
                        <div class="form-group mb-0">
                            <label>Note</label>
                            <input type="text" name="description" class="form-control" placeholder="Admin top-up">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Credits</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
@section('page-script')
<script>
    $('#top-up-modal').on('show.bs.modal', function(event) {
        var btn = $(event.relatedTarget);
        $(this).find('#top-up-reseller-id').val(btn.data('reseller-id'));
        $(this).find('#top-up-reseller-name').text(btn.data('reseller-name'));
    });
</script>
@endsection
