@php
    $links = [['title' => 'Support Tickets', 'route' => route('admin.tickets.index'), 'active' => false], ['title' => $ticket->ticket_number, 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title') {{ $ticket->ticket_number }} @endsection
@section('page-content')
<x-admin-page-header title="{{ $ticket->subject }}" :links="$links" />
<section class="content">
    <div class="container-fluid">
        <div class="row">
            {{-- Thread --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span><code>{{ $ticket->ticket_number }}</code></span>
                        <span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->statusLabel() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @foreach($ticket->replies as $reply)
                        <div class="p-3 border-bottom {{ $reply->is_staff_reply ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>
                                    @if($reply->is_staff_reply)
                                        <i class="fas fa-headset text-primary mr-1"></i> Staff
                                    @else
                                        <i class="fas fa-user mr-1"></i> {{ $reply->user->name }}
                                    @endif
                                </strong>
                                <small class="text-muted">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                            </div>
                            <p class="mb-0">{!! nl2br(e($reply->message)) !!}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="message" rows="4" class="form-control" placeholder="Type your reply..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title">Ticket Details</h5></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Customer:</dt><dd class="col-7">{{ $ticket->user->name ?? '-' }}</dd>
                            <dt class="col-5">Priority:</dt><dd class="col-7">{{ ucfirst($ticket->priority) }}</dd>
                            <dt class="col-5">Department:</dt><dd class="col-7">{{ $ticket->department ?: 'General' }}</dd>
                            <dt class="col-5">Opened:</dt><dd class="col-7">{{ $ticket->created_at->format('M d, Y') }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title">Assign / Status</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.tickets.assign') }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <select name="assigned_to" class="form-control form-control-sm mb-2">
                                <option value="">Unassigned</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-default btn-block">Update Assignment</button>
                        </form>

                        <form action="{{ route('admin.tickets.status') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <select name="status" class="form-control form-control-sm mb-2">
                                <option value="1" {{ $ticket->status == 1 ? 'selected' : '' }}>New</option>
                                <option value="2" {{ $ticket->status == 2 ? 'selected' : '' }}>In Progress</option>
                                <option value="3" {{ $ticket->status == 3 ? 'selected' : '' }}>Closed</option>
                                <option value="4" {{ $ticket->status == 4 ? 'selected' : '' }}>Re-opened</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-warning btn-block">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
