@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ $ticket->ticket_number }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')
    <div class="dashboard-header">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                    <a href="{{ route('reseller.tickets.index') }}"
                        style="color:var(--muted);font-size:.82rem;text-decoration:none;">
                        <i class="fas fa-arrow-left"></i> {{ __tr('All Tickets') }}
                    </a>
                </div>
                <h1 class="dash-page-title" style="font-size:1.3rem;">{{ $ticket->subject }}</h1>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:6px;">
                    <code style="color:#00d46a;font-size:.82rem;">{{ $ticket->ticket_number }}</code>
                    @php
                        $statusColor = match ($ticket->status) {
                            1 => '#0088cc',
                            2 => '#e67e00',
                            3 => '#555',
                            4 => '#00bcd4',
                            default => '#888',
                        };
                        $priorityColor = match ($ticket->priority) {
                            'urgent' => '#cc0000',
                            'high' => '#e67e00',
                            'normal' => '#0088cc',
                            default => '#888',
                        };
                    @endphp
                    <span
                        style="background:{{ $statusColor }}22;color:{{ $statusColor }};padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:700;">
                        {{ $ticket->statusLabel() }}
                    </span>
                    <span
                        style="background:{{ $priorityColor }}22;color:{{ $priorityColor }};padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:700;">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                    <span
                        style="font-size:.78rem;color:var(--muted);">{{ ucfirst($ticket->department ?: 'General') }}</span>
                </div>
            </div>

            @if ($ticket->status !== \App\Models\SupportTicket::STATUS_CLOSED)
                <form action="{{ route('reseller.tickets.close', $ticket->ticket_number) }}" method="POST">
                    @csrf
                    <button type="submit"
                        style="padding:8px 18px;border-radius:6px;border:1px solid rgba(204,0,0,.4);background:rgba(204,0,0,.1);color:#ff6b6b;font-size:.82rem;font-weight:600;cursor:pointer;"
                        onclick="return confirm('{{ __tr('Close this ticket?') }}')">
                        <i class="fas fa-times-circle"></i> {{ __tr('Close Ticket') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div
            style="background:rgba(0,212,106,.12);border:1px solid rgba(0,212,106,.3);color:#00d46a;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.88rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Conversation Thread --}}
    <div class="dashboard-card p-0" style="margin-bottom:20px;">
        <div class="card-header" style="padding:14px 20px;">
            <h3 class="card-title">{{ __tr('Conversation') }}</h3>
            <span style="font-size:.75rem;color:var(--muted);">{{ $ticket->replies->count() }}
                {{ __tr('messages') }}</span>
        </div>

        <div style="padding:0;">
            @foreach ($ticket->replies as $reply)
                @php $isStaff = $reply->is_staff_reply; @endphp
                <div
                    style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.05);background:{{ $isStaff ? 'rgba(0,212,106,.04)' : 'transparent' }};">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div
                                style="width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;
                                        background:{{ $isStaff ? 'rgba(0,212,106,.2)' : 'rgba(255,255,255,.08)' }};
                                        color:{{ $isStaff ? '#00d46a' : '#fff' }};">
                                @if ($isStaff)
                                    <i class="fas fa-headset" style="font-size:.75rem;"></i>
                                @else
                                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size:.85rem;font-weight:600;color:{{ $isStaff ? '#00d46a' : '#fff' }};">
                                    {{ $isStaff ? __tr('Support Team') : $reply->user->name ?? __tr('You') }}
                                </div>
                                @if ($isStaff)
                                    <div style="font-size:.72rem;color:var(--muted);">{{ __tr('Staff') }}</div>
                                @endif
                            </div>
                        </div>
                        <span
                            style="font-size:.75rem;color:var(--muted);">{{ $reply->created_at->format('M d, Y · H:i') }}</span>
                    </div>
                    <div
                        style="font-size:.88rem;line-height:1.7;color:rgba(255,255,255,.85);white-space:pre-wrap;padding-left:44px;">
                        {!! nl2br(e($reply->message)) !!}</div>
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
            <div style="padding:20px;">
                <form action="{{ route('reseller.tickets.reply', $ticket->ticket_number) }}" method="POST">
                    @csrf
                    @error('message')
                        <div
                            style="background:rgba(204,0,0,.12);color:#ff6b6b;padding:10px 14px;border-radius:6px;margin-bottom:12px;font-size:.84rem;">
                            {{ $message }}
                        </div>
                    @enderror
                    <textarea name="message" rows="5" required minlength="5" class="form-control"
                        style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:6px;resize:vertical;margin-bottom:14px;"
                        placeholder="{{ __tr('Type your message...') }}"></textarea>
                    <button type="submit" class="cmn-btn"
                        style="background:#00d46a;color:#000;font-weight:700;padding:10px 24px;">
                        <i class="fas fa-paper-plane"></i> {{ __tr('Send Reply') }}
                    </button>
                </form>
            </div>
        </div>
    @else
        <div
            style="text-align:center;padding:24px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid rgba(255,255,255,.06);">
            <i class="fas fa-lock" style="font-size:1.5rem;opacity:.3;display:block;margin-bottom:8px;"></i>
            <p style="color:var(--muted);font-size:.88rem;margin:0;">
                {{ __tr('This ticket is closed. Reply to reopen it.') }}</p>
            <form action="{{ route('reseller.tickets.reply', $ticket->ticket_number) }}" method="POST"
                style="margin-top:14px;">
                @csrf
                <textarea name="message" rows="4" required minlength="5" class="form-control"
                    style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:6px;resize:vertical;margin-bottom:12px;"
                    placeholder="{{ __tr('Type a reply to reopen this ticket...') }}"></textarea>
                <button type="submit" class="cmn-btn"
                    style="background:rgba(0,188,212,.15);color:#00bcd4;border:1px solid rgba(0,188,212,.3);font-weight:700;padding:10px 24px;">
                    <i class="fas fa-redo"></i> {{ __tr('Reply & Reopen') }}
                </button>
            </form>
        </div>
    @endif
@endsection
