@php
    $links = [['title' => 'Conversations', 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    User Conversations
@endsection
@section('page-content')
    <x-admin-page-header title="User Conversations" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('All Conversations') }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __tr('Listing') }}</th>
                                            <th>{{ __tr('Sender') }}</th>
                                            <th>{{ __tr('Receiver') }}</th>
                                            <th>{{ __tr('Last Message') }}</th>
                                            <th>{{ __tr('Messages') }}</th>
                                            <th>{{ __tr('Started') }}</th>
                                            <th class="text-center">{{ __tr('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($chats as $key => $chat)
                                            <tr>
                                                <td>{{ $chats->firstItem() + $key }}</td>
                                                <td>
                                                    @if ($chat->ad)
                                                        <a href="{{ route('ad.details.page', $chat->ad->uid) }}"
                                                            target="_blank">
                                                            {{ Str::limit($chat->ad->title, 35) }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Deleted</span>
                                                    @endif
                                                </td>
                                                <td>{{ $chat->sender->name ?? '—' }}</td>
                                                <td>{{ $chat->receiver->name ?? '—' }}</td>
                                                <td>
                                                    @if ($chat->lastMessage)
                                                        <span
                                                            class="text-muted">{{ Str::limit($chat->lastMessage->message, 45) }}</span>
                                                        <br><small
                                                            class="text-muted">{{ $chat->lastMessage->created_at->diffForHumans() }}</small>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-info">{{ $chat->messages_count ?? '—' }}</span>
                                                </td>
                                                <td>{{ $chat->created_at->format('d M Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.conversations.show', $chat->uid) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="las la-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No conversations
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($chats->hasPages())
                            <div class="card-footer">
                                {{ $chats->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
