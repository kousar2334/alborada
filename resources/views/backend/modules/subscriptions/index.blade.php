@php
    $links = [
        [
            'title' => 'Subscriptions',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Subscriptions
@endsection
@section('page-content')
    <x-admin-page-header title="Subscriptions" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Stats Row --}}
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total Subscriptions</p>
                        </div>
                        <div class="icon"><i class="fas fa-list"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['active'] }}</h3>
                            <p>Active</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['pending'] }}</h3>
                            <p>Pending
                                @if ($stats['bank_pending'] > 0)
                                    <small>({{ $stats['bank_pending'] }} bank)</small>
                                @endif
                            </p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['expired'] }}</h3>
                            <p>Expired</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('All Subscriptions') }}</h3>
                            <div class="card-tools">
                                <form method="GET" action="{{ route('admin.subscriptions.list') }}" class="d-flex"
                                    style="gap: 0.5rem;">
                                    <input type="text" name="q" class="form-control form-control-sm"
                                        placeholder="{{ __tr('Search member...') }}" value="{{ request('q') }}">
                                    <select name="status" class="form-control form-control-sm" style="width: auto;">
                                        <option value="">{{ __tr('All Status') }}</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>
                                            Failed</option>
                                        <option value="cancelled"
                                            {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                    <select name="method" class="form-control form-control-sm" style="width: auto;">
                                        <option value="">{{ __tr('All Methods') }}</option>
                                        <option value="sslcommerz"
                                            {{ request('method') === 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                                        <option value="bank_transfer"
                                            {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer
                                        </option>
                                        <option value="trial" {{ request('method') === 'trial' ? 'selected' : '' }}>Trial
                                        </option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __tr('Search') }}</button>
                                    @if (request('q') || request('status') || request('method'))
                                        <a href="{{ route('admin.subscriptions.list') }}"
                                            class="btn btn-secondary btn-sm">{{ __tr('Reset') }}</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Member') }}</th>
                                        <th>{{ __tr('Plan') }}</th>
                                        <th>{{ __tr('Transaction ID') }}</th>
                                        <th>{{ __tr('Amount') }}</th>
                                        <th>{{ __tr('Method') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th>{{ __tr('Starts') }}</th>
                                        <th>{{ __tr('Expires') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($subscriptions as $key => $sub)
                                        <tr @if ($sub->payment_method === 'bank_transfer' && $sub->status === 'pending') class="table-warning" @endif>
                                            <td>{{ $subscriptions->firstItem() + $key }}</td>
                                            <td>
                                                <strong>{{ $sub->user->name ?? '—' }}</strong><br>
                                                <small class="text-muted">{{ $sub->user->email ?? '' }}</small>
                                            </td>
                                            <td>{{ $sub->plan->title ?? '—' }}</td>
                                            <td>
                                                <code style="font-size: 0.78rem;">{{ $sub->transaction_id }}</code>
                                            </td>
                                            <td>
                                                @if ($sub->amount > 0)
                                                    <strong>{{ format_amount($sub->amount) }}</strong>
                                                @else
                                                    <span class="badge badge-success">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sub->payment_method === 'sslcommerz')
                                                    <span class="badge badge-info">SSLCommerz</span>
                                                @elseif ($sub->payment_method === 'bank_transfer')
                                                    <span class="badge badge-primary">Bank Transfer</span>
                                                @else
                                                    <span class="badge badge-secondary">Trial</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $isExpired =
                                                        $sub->status === 'active' && $sub->expires_at?->isPast();
                                                @endphp
                                                @if ($sub->status === 'active' && !$isExpired)
                                                    <span class="badge badge-success">Active</span>
                                                @elseif ($sub->status === 'active' && $isExpired)
                                                    <span class="badge badge-secondary">Expired</span>
                                                @elseif ($sub->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif ($sub->status === 'failed')
                                                    <span class="badge badge-danger">Failed</span>
                                                @elseif ($sub->status === 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @elseif ($sub->status === 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($sub->status) }}</span>
                                                @endif
                                                @if ($sub->admin_note)
                                                    <br><small class="text-muted" title="{{ $sub->admin_note }}">
                                                        <i class="fas fa-comment-alt"></i>
                                                        {{ Str::limit($sub->admin_note, 30) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $sub->starts_at?->format('M d, Y') ?? '—' }}</td>
                                            <td>
                                                @if ($sub->expires_at)
                                                    {{ $sub->expires_at->format('M d, Y') }}
                                                    @if ($sub->expires_at->isFuture())
                                                        <br><small
                                                            class="text-success">{{ $sub->expires_at->diffForHumans() }}</small>
                                                    @else
                                                        <br><small class="text-danger">Expired</small>
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $sub->created_at->format('M d, Y') }}</td>
                                            <td class="text-right" style="white-space: nowrap;">
                                                {{-- Bank transfer pending: show approve/reject --}}
                                                @if ($sub->payment_method === 'bank_transfer' && $sub->status === 'pending')
                                                    @if ($sub->bank_slip)
                                                        <a href="{{ asset('storage/' . $sub->bank_slip) }}" target="_blank"
                                                            class="btn btn-info btn-sm" title="{{ __tr('View Slip') }}">
                                                            <i class="fas fa-file-image"></i>
                                                        </a>
                                                    @endif
                                                    <button class="btn btn-success btn-sm approve-item"
                                                        data-id="{{ $sub->id }}"
                                                        data-name="{{ $sub->user->name ?? '' }}"
                                                        data-plan="{{ $sub->plan->title ?? '' }}"
                                                        title="{{ __tr('Approve') }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-warning btn-sm reject-item"
                                                        data-id="{{ $sub->id }}"
                                                        data-name="{{ $sub->user->name ?? '' }}"
                                                        data-plan="{{ $sub->plan->title ?? '' }}"
                                                        title="{{ __tr('Reject') }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-danger btn-sm delete-item"
                                                    data-id="{{ $sub->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11">
                                                <div class="text-center">{{ __tr('No subscriptions found') }}</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($subscriptions->hasPages())
                                <div class="p-3">
                                    {{ $subscriptions->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Approve Modal --}}
    <div class="modal fade" id="approve-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title h6">
                        <i class="fas fa-check-circle mr-2"></i>{{ __tr('Approve Subscription') }}
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.subscriptions.approve') }}">
                    @csrf
                    <input type="hidden" id="approve-item-id" name="id">
                    <div class="modal-body">
                        <p class="mb-3">
                            {{ __tr('Approve bank transfer for') }}
                            <strong id="approve-member-name"></strong> &mdash;
                            {{ __tr('plan') }} <strong id="approve-plan-name"></strong>?
                        </p>
                        <div class="form-group mb-0">
                            <label>{{ __tr('Admin Note (optional, sent to user)') }}</label>
                            <textarea name="admin_note" class="form-control" rows="2" placeholder="e.g. Payment verified successfully."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i> {{ __tr('Approve & Activate') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="reject-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title h6">
                        <i class="fas fa-times-circle mr-2"></i>{{ __tr('Reject Subscription') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.subscriptions.reject') }}">
                    @csrf
                    <input type="hidden" id="reject-item-id" name="id">
                    <div class="modal-body">
                        <p class="mb-3">
                            {{ __tr('Reject bank transfer for') }}
                            <strong id="reject-member-name"></strong> &mdash;
                            {{ __tr('plan') }} <strong id="reject-plan-name"></strong>?
                        </p>
                        <div class="form-group mb-0">
                            <label>{{ __tr('Rejection Reason (sent to user via notification & email)') }}</label>
                            <textarea name="admin_note" class="form-control" rows="2"
                                placeholder="e.g. Payment slip could not be verified."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-times mr-1"></i> {{ __tr('Reject') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="delete-item-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete?') }}</h4>
                    <form method="POST" action="{{ route('admin.subscriptions.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-item-id" name="id">
                        <button type="button" class="btn mt-2 btn-danger"
                            data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        (function($) {
            "use strict";

            $('.delete-item').on('click', function() {
                var id = $(this).data('id');
                $('#delete-item-id').val(id);
                $('#delete-item-modal').modal('show');
            });

            $('.approve-item').on('click', function() {
                $('#approve-item-id').val($(this).data('id'));
                $('#approve-member-name').text($(this).data('name'));
                $('#approve-plan-name').text($(this).data('plan'));
                $('#approve-modal').modal('show');
            });

            $('.reject-item').on('click', function() {
                $('#reject-item-id').val($(this).data('id'));
                $('#reject-member-name').text($(this).data('name'));
                $('#reject-plan-name').text($(this).data('plan'));
                $('#reject-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
