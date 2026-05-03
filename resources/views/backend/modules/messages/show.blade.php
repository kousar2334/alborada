@php
    $links = [
        ['title' => 'Conversations', 'route' => route('admin.conversations.index'), 'active' => false],
        ['title' => 'View Conversation', 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    View Conversation
@endsection
@section('page-style')
    <style>
        .conv-card {
            border-radius: 10px;
            overflow: hidden;
        }

        .conv-meta {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .conv-meta p {
            margin: 0 0 6px;
            font-size: 13px;
        }

        .conv-meta p:last-child {
            margin: 0;
        }

        .conv-meta strong {
            color: #1e293b;
        }

        .msg-thread {
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .msg-row {
            display: flex;
            gap: 10px;
        }

        .msg-row.right {
            flex-direction: row-reverse;
        }

        .msg-row-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            flex-shrink: 0;
        }

        .msg-row-avatar img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .msg-bubble {
            max-width: 60%;
        }

        .msg-bubble-sender {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .msg-row.right .msg-bubble-sender {
            text-align: right;
        }

        .msg-bubble-text {
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.55;
            word-break: break-word;
        }

        .msg-row:not(.right) .msg-bubble-text {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-top-left-radius: 4px;
        }

        .msg-row.right .msg-bubble-text {
            background: #3B82F6;
            color: #fff;
            border-top-right-radius: 4px;
        }

        .msg-bubble-time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .msg-row.right .msg-bubble-time {
            text-align: right;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="View Conversation" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    {{-- Conversation Meta --}}
                    <div class="conv-meta">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Sender:</strong> {{ $chat->sender->name ?? '—' }}
                                    @if ($chat->sender)
                                        <small class="text-muted">({{ $chat->sender->email }})</small>
                                    @endif
                                </p>
                                <p><strong>Receiver:</strong> {{ $chat->receiver->name ?? '—' }}
                                    @if ($chat->receiver)
                                        <small class="text-muted">({{ $chat->receiver->email }})</small>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                @if ($chat->ad)
                                    <p><strong>Listing:</strong>
                                        <a href="{{ route('ad.details.page', $chat->ad->uid) }}" target="_blank">
                                            {{ $chat->ad->title }}
                                        </a>
                                    </p>
                                    <p><strong>Price:</strong> {{ format_amount($chat->ad->price) }}</p>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <p><strong>Started:</strong> {{ $chat->created_at->format('d M Y, h:i A') }}</p>
                                <p><strong>Messages:</strong> {{ $chat->messages->count() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Message Thread --}}
                    <div class="card conv-card">
                        <div class="card-header">
                            <h3 class="card-title">Message Thread</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="msg-thread" id="msgThread">
                                @forelse ($chat->messages as $msg)
                                    @php $isReceiver = $msg->sender_id === $chat->receiver_id; @endphp
                                    <div class="msg-row {{ $isReceiver ? 'right' : '' }}">
                                        <div class="msg-row-avatar">
                                            @if ($msg->sender && $msg->sender->image)
                                                <img src="{{ getFilePath($msg->sender->image) }}"
                                                    alt="{{ $msg->sender->name }}">
                                            @else
                                                {{ strtoupper(substr($msg->sender->name ?? 'U', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="msg-bubble">
                                            <div class="msg-bubble-sender">{{ $msg->sender->name ?? 'Unknown' }}</div>
                                            <div class="msg-bubble-text">{{ $msg->message }}</div>
                                            <div class="msg-bubble-time">{{ $msg->created_at->format('d M Y, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-muted py-4">No messages in this conversation.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary">
                            <i class="las la-arrow-left"></i> Back to Conversations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        (function() {
            var el = document.getElementById('msgThread');
            if (el) el.scrollTop = el.scrollHeight;
        })();
    </script>
@endsection
