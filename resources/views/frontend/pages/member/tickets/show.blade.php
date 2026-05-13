@extends('frontend.layouts.dashboard')
@section('dashboard-content')
    <div class="dashboard-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('member.tickets.index') }}" class="dash-back-link-muted">
                <i class="fas fa-arrow-left"></i> {{ __tr('Back to Tickets') }}
            </a>
            <span class="ticket-divider"></span>
            <h1 class="dash-page-title mb-0">{{ $ticket->subject }}</h1>
        </div>
        <div class="ticket-show-meta">
            <span class="ticket-show-badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->statusLabel() }}</span>
            <span class="ticket-show-number">{{ $ticket->ticket_number }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Thread --}}
    <div class="dashboard-card mb-4 p-0 overflow-hidden">
        <div class="ticket-thread-header">
            <i class="fas fa-comments mr-2"></i>{{ __tr('Conversation') }}
        </div>
        <div class="ticket-thread">
            @foreach ($ticket->replies as $reply)
                <div class="ticket-message {{ $reply->is_staff_reply ? 'ticket-message--staff' : 'ticket-message--user' }}">
                    <div class="ticket-message-avatar">
                        @if ($reply->is_staff_reply)
                            <i class="fas fa-headset"></i>
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    <div class="ticket-message-body">
                        <div class="ticket-message-meta">
                            <span class="ticket-message-author">
                                @if ($reply->is_staff_reply)
                                    {{ __tr('Support Team') }}
                                    <span class="ticket-staff-tag">{{ __tr('Staff') }}</span>
                                @else
                                    {{ $reply->user->name }}
                                @endif
                            </span>
                            <span class="ticket-message-time">{{ $reply->created_at->format('M d, Y · H:i') }}</span>
                        </div>
                        <div class="ticket-message-text">{!! nl2br(e($reply->message)) !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($ticket->status !== \App\Models\SupportTicket::STATUS_CLOSED)
        {{-- Reply form --}}
        <div class="dashboard-card mb-3 p-0 overflow-hidden">
            <div class="ticket-thread-header">
                <i class="fas fa-reply mr-2"></i>{{ __tr('Add Reply') }}
            </div>
            <div class="ticket-reply-body">
                <form action="{{ route('member.tickets.reply', $ticket->ticket_number) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" rows="5" class="form-control form-control-dark @error('message') is-invalid @enderror"
                            placeholder="{{ __tr('Type your reply...') }}" style="resize:vertical;">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="ticket-reply-actions">
                        <button type="submit" class="cmn-btn">
                            <i class="fas fa-paper-plane"></i>
                            {{ __tr('Send Reply') }}
                        </button>
                    </div>
                </form>
                <form action="{{ route('member.tickets.close', $ticket->ticket_number) }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="cmn-btn cmn-btn-secondary"
                        onclick="return confirm('{{ __tr('Close this ticket?') }}')">
                        <i class="fas fa-lock"></i>
                        {{ __tr('Close Ticket') }}
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="ticket-closed-notice">
            <i class="fas fa-lock-open mr-2"></i>{{ __tr('This ticket is closed.') }}
        </div>
    @endif
@endsection
