@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Message') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')
    <div class="dashboard-wrapper">
        <div class="dashboard-content">
            <a href="{{ route('member.messages.index') }}" class="back-link">
                <i class="las la-arrow-left"></i> {{ __tr('Back to messages') }}
            </a>

            <div class="chat-card">
                {{-- Header --}}
                @php
                    $other = $chat->sender_id === auth()->id() ? $chat->receiver : $chat->sender;
                @endphp
                <div class="chat-header">
                    @if ($other && $other->image)
                        <img src="{{ asset(getFilePath($other->image)) }}" alt="{{ $other->name }}"
                            class="chat-header-avatar">
                    @else
                        <div class="chat-header-placeholder">{{ strtoupper(substr($other->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="chat-header-info">
                        <p class="chat-header-name">{{ $other->name ?? __tr('Unknown') }}</p>
                        @if ($chat->ad)
                            <p class="chat-header-sub">
                                <a href="{{ route('ad.details.page', $chat->ad->uid) }}" target="_blank"
                                    class="chat-ad-link">
                                    {{ __tr('Re') }}: {{ $chat->ad->title }}
                                </a>
                            </p>
                        @endif
                    </div>
                    @if ($chat->ad && $chat->ad->thumbnail_image)
                        <div class="ad-banner ms-auto d-none d-md-flex">
                            <img src="{{ asset(getFilePath($chat->ad->thumbnail_image, false)) }}"
                                alt="{{ $chat->ad->title }}">
                            <div>
                                <div class="ad-banner-title">{{ Str::limit($chat->ad->title, 30) }}</div>
                                <div class="ad-banner-price">{{ format_amount($chat->ad->price) }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Messages --}}
                <div class="chat-body" id="chatBody">
                    @forelse ($chat->messages as $msg)
                        @php $isMine = $msg->sender_id === auth()->id(); @endphp
                        <div class="msg-bubble-wrap {{ $isMine ? 'mine' : '' }}">
                            @if ($msg->sender && $msg->sender->image)
                                <img src="{{ asset(getFilePath($msg->sender->image)) }}" alt="{{ $msg->sender->name }}"
                                    class="bubble-avatar">
                            @else
                                <div class="bubble-avatar-placeholder">
                                    {{ strtoupper(substr($msg->sender->name ?? 'U', 0, 1)) }}</div>
                            @endif
                            <div class="bubble">
                                <div class="bubble-text">{{ $msg->message }}</div>
                                <div class="bubble-time">{{ $msg->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted mt-auto mb-auto">
                            {{ __tr('No messages yet. Say hello!') }}</div>
                    @endforelse
                </div>

                {{-- Reply Form --}}
                <div class="chat-footer">
                    <form action="{{ route('member.messages.send', $chat->uid) }}" method="POST">
                        @csrf
                        <textarea name="message" rows="2" placeholder="{{ __tr('Type a message...') }}" required
                            onkeydown="if(event.ctrlKey && event.key==='Enter') this.form.submit()">{{ old('message') }}</textarea>
                        <button type="submit">
                            <i class="las la-paper-plane"></i> {{ __tr('Send') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-js')
    <script>
        // Auto-scroll chat to bottom on load
        (function() {
            var body = document.getElementById('chatBody');
            if (body) body.scrollTop = body.scrollHeight;
        })();
    </script>
@endsection
