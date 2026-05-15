@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('Support Tickets') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')
    <div class="dashboard-header">
        <div>
            <h1 class="dash-page-title">
                <i class="fa-solid fa-headset card-header-icon me-2"></i>{{ __tr('Support Tickets') }}
            </h1>
            <p class="dash-page-subtitle">{{ __tr('Track and manage your support requests.') }}</p>
        </div>
        <a href="{{ route('reseller.tickets.create') }}" class="action-btn primary">
            <i class="fas fa-plus"></i> {{ __tr('New Ticket') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success-dark">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card p-0">
        <div class="card-header">
            <h3 class="card-title">{{ __tr('My Tickets') }}</h3>
            <span class="ticket-total-count">{{ $tickets->total() }} {{ __tr('total') }}</span>
        </div>
        <div class="tickets-table-wrap">
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>{{ __tr('Ticket #') }}</th>
                        <th>{{ __tr('Subject') }}</th>
                        <th>{{ __tr('Priority') }}</th>
                        <th>{{ __tr('Status') }}</th>
                        <th>{{ __tr('Last Update') }}</th>
                        <th class="text-center">{{ __tr('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        @php
                            $priorityClass = match ($ticket->priority) {
                                'urgent' => 'ticket-priority-urgent',
                                'high' => 'ticket-priority-high',
                                'normal' => 'ticket-priority-normal',
                                default => 'ticket-priority-low',
                            };
                            $statusClass = 'ticket-status-' . $ticket->status;
                        @endphp
                        <tr>
                            <td>
                                <code class="ticket-number-code">{{ $ticket->ticket_number }}</code>
                            </td>
                            <td>
                                <span class="ticket-subject-wrap">{{ $ticket->subject }}</span>
                                @if ($ticket->latestReply && !$ticket->latestReply->is_staff_reply === false)
                                    <span class="ticket-staff-note">
                                        <i class="fas fa-reply"></i> {{ __tr('Staff replied') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="ticket-priority {{ $priorityClass }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="ticket-status {{ $statusClass }}">
                                    {{ $ticket->statusLabel() }}
                                </span>
                            </td>
                            <td class="ticket-time">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="ticket-action-cell">
                                <a href="{{ route('reseller.tickets.show', $ticket->ticket_number) }}"
                                    class="ticket-view-btn">
                                    {{ __tr('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="tickets-empty">
                                    <i class="fas fa-ticket-alt tickets-empty-icon"></i>
                                    <p class="tickets-empty-text">{{ __tr('No tickets yet.') }}</p>
                                    <a href="{{ route('reseller.tickets.create') }}" class="tickets-empty-link">
                                        {{ __tr('Open your first ticket') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="tickets-pagination">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection
