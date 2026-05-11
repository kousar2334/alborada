@extends('frontend.layouts.dashboard')
@section('dashboard-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ __tr('My Support Tickets') }}</h4>
        <a href="{{ route('member.tickets.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> {{ __tr('New Ticket') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __tr('Ticket #') }}</th>
                        <th>{{ __tr('Subject') }}</th>
                        <th>{{ __tr('Priority') }}</th>
                        <th>{{ __tr('Status') }}</th>
                        <th>{{ __tr('Last Update') }}</th>
                        <th>{{ __tr('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><code>{{ $ticket->ticket_number }}</code></td>
                        <td>{{ $ticket->subject }}</td>
                        <td>
                            <span class="badge badge-{{ match($ticket->priority) {
                                'urgent' => 'danger', 'high' => 'warning',
                                'normal' => 'info', default => 'secondary'
                            } }}">{{ ucfirst($ticket->priority) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->statusLabel() }}</span>
                        </td>
                        <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('member.tickets.show', $ticket->ticket_number) }}" class="btn btn-sm btn-default">
                                {{ __tr('View') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">{{ __tr('No tickets found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>
@endsection
