@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Messages') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')
    <div class="dashboard-wrapper">
        <div class="dashboard-content">
            <div class="msg-card">
                <div class="msg-card-header">
                    <h5>{{ __tr('My Messages') }}</h5>
                    <span class="text-muted msg-count-label">{{ $chats->total() }}
                        {{ __tr('conversation(s)') }}</span>
                </div>

                @if ($chats->isEmpty())
                    <div class="empty-state">
                        <i class="las la-comments"></i>
                        <p>{{ __tr('No conversations yet.') }}</p>
                        <a href="{{ route('ad.listing.page') }}"
                            class="btn btn-primary btn-sm mt-2">{{ __tr('Browse Ads') }}</a>
                    </div>
                @else
                    <ul class="msg-list">
                        @foreach ($chats as $chat)
                            @php
                                $other = $chat->sender_id === auth()->id() ? $chat->receiver : $chat->sender;
                                $unread = $chat->unreadCountFor(auth()->id());
                            @endphp
                            <li>
                                <a href="{{ route('member.messages.show', $chat->uid) }}"
                                    class="msg-list-item {{ $unread > 0 ? 'unread' : '' }}">
                                    @if ($other && $other->image)
                                        <img src="{{ asset(getFilePath($other->image)) }}" alt="{{ $other->name }}"
                                            class="msg-avatar">
                                    @else
                                        <div class="msg-avatar-placeholder">
                                            {{ strtoupper(substr($other->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="msg-info">
                                        <div class="msg-top">
                                            <span class="msg-name">{{ $other->name ?? __tr('Unknown') }}</span>
                                            <span class="msg-time">
                                                {{ $chat->lastMessage ? $chat->lastMessage->created_at->diffForHumans() : '' }}
                                            </span>
                                        </div>
                                        @if ($chat->ad)
                                            <div class="msg-ad-title">{{ __tr('Re') }}: {{ $chat->ad->title }}
                                            </div>
                                        @endif
                                        <div class="msg-preview">
                                            {{ $chat->lastMessage ? Str::limit($chat->lastMessage->message, 70) : __tr('No messages yet') }}
                                        </div>
                                    </div>
                                    @if ($unread > 0)
                                        <span class="msg-badge">{{ $unread }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="p-3">
                        {{ $chats->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
