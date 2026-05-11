@extends('frontend.layouts.dashboard')
@section('dashboard-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('member.tickets.index') }}" class="btn btn-sm btn-default mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class="mb-0">{{ $ticket->subject }}</h4>
        </div>
        <div>
            <span class="badge {{ $ticket->statusBadgeClass() }} mr-2">{{ $ticket->statusLabel() }}</span>
            <code>{{ $ticket->ticket_number }}</code>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Thread --}}
    <div class="card mb-3">
        <div class="card-body p-0">
            @foreach($ticket->replies as $reply)
            <div class="p-3 border-bottom {{ $reply->is_staff_reply ? 'bg-light' : '' }}">
                <div class="d-flex justify-content-between mb-1">
                    <strong>
                        @if($reply->is_staff_reply)
                            <i class="fas fa-headset text-primary mr-1"></i> {{ __tr('Support Team') }}
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
    </div>

    @if($ticket->status !== \App\Models\SupportTicket::STATUS_CLOSED)
    {{-- Reply form --}}
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">{{ __tr('Add Reply') }}</h6></div>
        <div class="card-body">
            <form action="{{ route('member.tickets.reply', $ticket->ticket_number) }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                        placeholder="{{ __tr('Type your reply...') }}">{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary mr-2">{{ __tr('Send Reply') }}</button>
            </form>
        </div>
    </div>

    <form action="{{ route('member.tickets.close', $ticket->ticket_number) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm"
            onclick="return confirm('{{ __tr('Close this ticket?') }}')">
            {{ __tr('Close Ticket') }}
        </button>
    </form>
    @else
    <div class="alert alert-secondary">{{ __tr('This ticket is closed.') }}</div>
    @endif
</div>
@endsection
