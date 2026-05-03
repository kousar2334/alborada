@php
    $links = [
        [
            'title' => 'Contact Messages',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Contact Messages') }}
@endsection

@section('page-content')
    <x-admin-page-header title="Contact Messages" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Manage Contact Messages') }}</h3>
                        </div>

                        <!-- Filters -->
                        <div class="card-body border-bottom pb-3">
                            <form method="GET" action="{{ route('admin.contact.us.message.list') }}"
                                class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control form-control-sm"
                                        placeholder="{{ __tr('Search by name, email or subject...') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">{{ __tr('All Messages') }}</option>
                                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>
                                            {{ __tr('Unread') }}</option>
                                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>
                                            {{ __tr('Read') }}</option>
                                        <option value="replied"{{ request('status') === 'replied' ? 'selected' : '' }}>
                                            {{ __tr('Replied') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-search me-1"></i> {{ __tr('Filter') }}
                                    </button>
                                </div>
                                @if (request('search') || request('status'))
                                    <div class="col-md-2">
                                        <a href="{{ route('admin.contact.us.message.list') }}"
                                            class="btn btn-secondary btn-sm w-100">
                                            {{ __tr('Clear') }}
                                        </a>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:40px">{{ __tr('#') }}</th>
                                        <th>{{ __tr('From') }}</th>
                                        <th>{{ __tr('Subject') }}</th>
                                        <th style="width:90px">{{ __tr('Status') }}</th>
                                        <th style="width:120px">{{ __tr('Received') }}</th>
                                        <th class="text-right" style="width:140px">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($messages as $key => $msg)
                                        <tr class="{{ !$msg->is_read ? 'table-warning' : '' }}">
                                            <td>{{ $messages->firstItem() + $key }}</td>
                                            <td>
                                                <strong>{{ $msg->name }}</strong>
                                                <br><small class="text-muted">{{ $msg->email }}</small>
                                            </td>
                                            <td>{{ \Illuminate\Support\Str::limit($msg->subject, 60) }}</td>
                                            <td>
                                                @if ($msg->replied_at)
                                                    <span class="badge badge-success">{{ __tr('Replied') }}</span>
                                                @elseif($msg->is_read)
                                                    <span class="badge badge-info">{{ __tr('Read') }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ __tr('New') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $msg->created_at->format('d M Y') }}</small>
                                                <br><small
                                                    class="text-muted">{{ $msg->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td class="text-right">
                                                <button type="button"
                                                    class="btn btn-sm btn-info text-white view-message-btn"
                                                    data-id="{{ $msg->id }}" data-name="{{ $msg->name }}"
                                                    data-email="{{ $msg->email }}" data-subject="{{ $msg->subject }}"
                                                    data-message="{{ $msg->message }}"
                                                    data-replied="{{ $msg->replied_at ? 'yes' : 'no' }}"
                                                    data-reply-message="{{ $msg->reply_message }}" data-toggle="modal"
                                                    data-target="#view-message-modal" title="{{ __tr('View & Reply') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <form action="{{ route('admin.contact.us.message.delete') }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __tr('Are you sure you want to delete this message?') }}')">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $msg->id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="{{ __tr('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                {{ __tr('No messages found.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($messages->hasPages())
                            <div class="card-footer">
                                {{ $messages->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- View & Reply Modal -->
    <div class="modal fade" id="view-message-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope me-2"></i>
                        {{ __tr('Message Details') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Sender Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __tr('From') }}</label>
                            <p class="mb-0 fw-bold" id="modal-name"></p>
                            <a id="modal-email" href="#" class="text-muted small"></a>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __tr('Subject') }}</label>
                            <p class="mb-0 fw-bold" id="modal-subject"></p>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="form-group">
                        <label class="text-muted small">{{ __tr('Message') }}</label>
                        <div id="modal-message" class="p-3 bg-light rounded"
                            style="white-space: pre-wrap; min-height: 80px;"></div>
                    </div>

                    <!-- Previous Reply -->
                    <div id="previous-reply-section" class="alert alert-success d-none">
                        <strong><i class="fas fa-check-circle me-1"></i>{{ __tr('Already Replied:') }}</strong>
                        <div id="modal-reply-message" class="mt-2" style="white-space: pre-wrap;"></div>
                    </div>

                    <!-- Reply Form -->
                    <hr>
                    <form action="{{ route('admin.contact.us.message.reply') }}" method="POST" id="reply-form">
                        @csrf
                        <input type="hidden" name="id" id="reply-msg-id">
                        <div class="form-group">
                            <label class="fw-bold">
                                <i class="fas fa-reply me-1"></i>{{ __tr('Reply Message') }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="reply_message" id="reply-message-textarea" rows="5" class="form-control"
                                placeholder="{{ __tr('Type your reply here...') }}" required></textarea>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                                {{ __tr('Close') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>{{ __tr('Send Reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('.view-message-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const email = this.dataset.email;
                const subject = this.dataset.subject;
                const message = this.dataset.message;
                const replied = this.dataset.replied;
                const replyMessage = this.dataset.replyMessage;

                document.getElementById('modal-name').textContent = name;
                document.getElementById('modal-email').textContent = email;
                document.getElementById('modal-email').href = 'mailto:' + email;
                document.getElementById('modal-subject').textContent = subject;
                document.getElementById('modal-message').textContent = message;
                document.getElementById('reply-msg-id').value = id;
                document.getElementById('reply-message-textarea').value = '';

                const prevSection = document.getElementById('previous-reply-section');
                if (replied === 'yes' && replyMessage) {
                    prevSection.classList.remove('d-none');
                    document.getElementById('modal-reply-message').textContent = replyMessage;
                } else {
                    prevSection.classList.add('d-none');
                }

            });
        });
    </script>
@endsection
