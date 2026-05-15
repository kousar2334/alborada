@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ $ticket->ticket_number }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')
    <div class="dashboard-header">
        <div>
            <a href="{{ route('reseller.tickets.index') }}" class="ticket-show-back">
                <i class="fas fa-arrow-left"></i> {{ __tr('All Tickets') }}
            </a>
            <h1 class="dash-page-title ticket-show-title">{{ $ticket->subject }}</h1>
            <div class="ticket-show-meta-row">
                <code class="ticket-show-ticket-num">{{ $ticket->ticket_number }}</code>
                @php
                    $statusClass = 'ticket-status-' . $ticket->status;
                    $priorityClass = match ($ticket->priority) {
                        'urgent' => 'ticket-priority-urgent',
                        'high' => 'ticket-priority-high',
                        'normal' => 'ticket-priority-normal',
                        default => 'ticket-priority-low',
                    };
                @endphp
                <span class="ticket-status {{ $statusClass }}">{{ $ticket->statusLabel() }}</span>
                <span class="ticket-priority {{ $priorityClass }}">{{ ucfirst($ticket->priority) }}</span>
                <span class="ticket-show-dept">{{ ucfirst($ticket->department ?: 'General') }}</span>
            </div>
        </div>

        @if ($ticket->status !== \App\Models\SupportTicket::STATUS_CLOSED)
            <form action="{{ route('reseller.tickets.close', $ticket->ticket_number) }}" method="POST">
                @csrf
                <button type="submit" class="ticket-close-btn"
                    onclick="return confirm('{{ __tr('Close this ticket?') }}')">
                    <i class="fas fa-times-circle"></i> {{ __tr('Close Ticket') }}
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="alert-success-dark">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Conversation Thread --}}
    <div class="dashboard-card p-0 mb-4">
        <div class="card-header">
            <h3 class="card-title">{{ __tr('Conversation') }}</h3>
            <span class="ticket-thread-message-count">{{ $ticket->replies->count() }} {{ __tr('messages') }}</span>
        </div>

        <div class="ticket-thread-wrap">
            @foreach ($ticket->replies as $reply)
                @php $isStaff = $reply->is_staff_reply; @endphp
                <div class="ticket-reply-item {{ $isStaff ? 'staff' : '' }}">
                    <div class="ticket-reply-meta">
                        <div class="ticket-reply-author-wrap">
                            <div class="ticket-reply-avatar {{ $isStaff ? 'staff' : 'user' }}">
                                @if ($isStaff)
                                    <i class="fas fa-headset ticket-reply-role"></i>
                                @else
                                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="ticket-reply-name {{ $isStaff ? 'staff' : 'user' }}">
                                    {{ $isStaff ? __tr('Support Team') : $reply->user->name ?? __tr('You') }}
                                </div>
                                @if ($isStaff)
                                    <div class="ticket-reply-role">{{ __tr('Staff') }}</div>
                                @endif
                            </div>
                        </div>
                        <span class="ticket-reply-time">{{ $reply->created_at->format('M d, Y · H:i') }}</span>
                    </div>
                    <div class="ticket-reply-text">{!! nl2br(e($reply->message)) !!}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Reply Form --}}
    @if ($ticket->status !== \App\Models\SupportTicket::STATUS_CLOSED)
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Add Reply') }}</h3>
            </div>
            <div class="ticket-reply-form-body">
                <form action="{{ route('reseller.tickets.reply', $ticket->ticket_number) }}" method="POST">
                    @csrf
                    @error('message')
                        <div class="ticket-reply-form-error">{{ $message }}</div>
                    @enderror
                    <textarea name="message" rows="5" required minlength="5"
                        class="form-control form-control-dark textarea-resize-v ticket-reply-textarea"
                        placeholder="{{ __tr('Type your message...') }}"></textarea>
                    <button type="submit" class="cmn-btn cmn-btn-green">
                        <i class="fas fa-paper-plane"></i> {{ __tr('Send Reply') }}
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="ticket-closed-wrap">
            <i class="fas fa-lock ticket-closed-icon"></i>
            <p class="ticket-closed-text">{{ __tr('This ticket is closed. Reply to reopen it.') }}</p>
            <form action="{{ route('reseller.tickets.reply', $ticket->ticket_number) }}" method="POST" class="mt-3">
                @csrf
                <textarea name="message" rows="4" required minlength="5"
                    class="form-control form-control-dark textarea-resize-v ticket-closed-textarea"
                    placeholder="{{ __tr('Type a reply to reopen this ticket...') }}"></textarea>
                <button type="submit" class="cmn-btn ticket-reopen-btn">
                    <i class="fas fa-redo"></i> {{ __tr('Reply & Reopen') }}
                </button>
            </form>
        </div>
    @endif
@endsection
