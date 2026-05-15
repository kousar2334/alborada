@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('Support Tickets') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')
    <div class="dashboard-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <h1 class="dash-page-title"><i class="fa-solid fa-headset"
                        style="color:#00d46a;margin-right:10px;"></i>{{ __tr('Support Tickets') }}</h1>
                <p class="dash-page-subtitle">{{ __tr('Track and manage your support requests.') }}</p>
            </div>
            <a href="{{ route('reseller.tickets.create') }}" class="cmn-btn"
                style="background:#00d46a;color:#000;font-weight:700;padding:10px 20px;white-space:nowrap;">
                <i class="fas fa-plus"></i> {{ __tr('New Ticket') }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div
            style="background:rgba(0,212,106,.12);border:1px solid rgba(0,212,106,.3);color:#00d46a;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.88rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card p-0">
        <div class="card-header" style="padding:16px 20px;">
            <h3 class="card-title">{{ __tr('My Tickets') }}</h3>
            <span style="font-size:.75rem;color:var(--muted);">{{ $tickets->total() }} {{ __tr('total') }}</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,.07);">
                        <th
                            style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">
                            {{ __tr('Ticket #') }}</th>
                        <th
                            style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">
                            {{ __tr('Subject') }}</th>
                        <th
                            style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">
                            {{ __tr('Priority') }}</th>
                        <th
                            style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">
                            {{ __tr('Status') }}</th>
                        <th
                            style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">
                            {{ __tr('Last Update') }}</th>
                        <th
                            style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:center;">
                            {{ __tr('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        @php
                            $priorityColor = match ($ticket->priority) {
                                'urgent' => '#cc0000',
                                'high' => '#e67e00',
                                'normal' => '#0088cc',
                                default => '#888',
                            };
                            $statusColor = match ($ticket->status) {
                                1 => '#0088cc',
                                2 => '#e67e00',
                                3 => '#555',
                                4 => '#00bcd4',
                                default => '#888',
                            };
                        @endphp
                        <tr style="border-bottom:1px solid rgba(255,255,255,.04);">
                            <td style="padding:14px 16px;">
                                <code style="color:#00d46a;font-size:.82rem;">{{ $ticket->ticket_number }}</code>
                            </td>
                            <td style="padding:14px 16px;font-size:.88rem;max-width:260px;">
                                <span
                                    style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $ticket->subject }}</span>
                                @if ($ticket->latestReply && !$ticket->latestReply->is_staff_reply === false)
                                    <span style="font-size:.72rem;color:#00d46a;margin-top:3px;display:block;"><i
                                            class="fas fa-reply"></i> {{ __tr('Staff replied') }}</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                <span
                                    style="background:{{ $priorityColor }}22;color:{{ $priorityColor }};padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:700;">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;">
                                <span
                                    style="background:{{ $statusColor }}22;color:{{ $statusColor }};padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:700;">
                                    {{ $ticket->statusLabel() }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;font-size:.82rem;color:var(--muted);">
                                {{ $ticket->updated_at->diffForHumans() }}</td>
                            <td style="padding:14px 16px;text-align:center;">
                                <a href="{{ route('reseller.tickets.show', $ticket->ticket_number) }}"
                                    style="background:rgba(255,255,255,.07);color:#fff;padding:6px 14px;border-radius:6px;font-size:.78rem;text-decoration:none;font-weight:600;">
                                    {{ __tr('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:60px 20px;text-align:center;color:var(--muted);">
                                <i class="fas fa-ticket-alt"
                                    style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:12px;"></i>
                                <p style="margin:0;font-size:.9rem;">{{ __tr('No tickets yet.') }}</p>
                                <a href="{{ route('reseller.tickets.create') }}"
                                    style="color:#00d46a;font-size:.82rem;">{{ __tr('Open your first ticket') }}</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div style="padding:16px;border-top:1px solid rgba(255,255,255,.07);">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection
