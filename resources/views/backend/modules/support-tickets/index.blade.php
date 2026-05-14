@php
    $links = [['title' => 'Support Tickets', 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title') Support Tickets @endsection
@section('page-content')
<x-admin-page-header title="Support Tickets" :links="$links" />
<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-4 col-md-2">
                <div class="small-box bg-primary"><div class="inner"><h4>{{ $stats['new'] }}</h4><p>New</p></div></div>
            </div>
            <div class="col-4 col-md-2">
                <div class="small-box bg-warning"><div class="inner"><h4>{{ $stats['in_progress'] }}</h4><p>In Progress</p></div></div>
            </div>
            <div class="col-4 col-md-2">
                <div class="small-box bg-secondary"><div class="inner"><h4>{{ $stats['closed'] }}</h4><p>Closed</p></div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <form class="form-inline" method="GET">
                    <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Search..." value="{{ request('q') }}">
                    <select name="status" class="form-control form-control-sm mr-2">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>New</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <select name="priority" class="form-control form-control-sm mr-2">
                        <option value="">All Priority</option>
                        @foreach(['low','normal','high','urgent'] as $p)
                            <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-default" type="submit">Filter</button>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Ticket</th><th>Member</th><th>Priority</th><th>Status</th><th>Assigned</th><th>Created</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>
                                <code>{{ $ticket->ticket_number }}</code><br>
                                <small>{{ Str::limit($ticket->subject, 40) }}</small>
                            </td>
                            <td>{{ $ticket->user->name ?? '-' }}</td>
                            <td><span class="badge badge-{{ match($ticket->priority) { 'urgent'=>'danger','high'=>'warning','normal'=>'info',default=>'secondary' } }}">{{ ucfirst($ticket->priority) }}</span></td>
                            <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->statusLabel() }}</span></td>
                            <td>{{ $ticket->assignedAdmin->name ?? '-' }}</td>
                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4">No tickets found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="card-footer">{{ $tickets->withQueryString()->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</section>
@endsection
