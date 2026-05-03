@php
    $links = [['title' => 'Payment Transactions', 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Payment Transactions
@endsection
@section('page-content')
    <x-admin-page-header title="Payment Transactions" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Stats --}}
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total</p>
                        </div>
                        <div class="icon"><i class="fas fa-receipt"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['pending'] }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['approved'] }}</h3>
                            <p>Approved</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['bank_pending'] }}</h3>
                            <p>Bank Pending Approval</p>
                        </div>
                        <div class="icon"><i class="fas fa-university"></i></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __tr('Payment Transactions') }}</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('admin.bank.payments') }}" class="d-flex"
                            style="gap: 0.5rem;">
                            <input type="text" name="q" class="form-control form-control-sm"
                                placeholder="{{ __tr('Search member, txn ID or ref...') }}" value="{{ request('q') }}">
                            <select name="method" class="form-control form-control-sm" style="width: auto;">
                                <option value="">{{ __tr('All Methods') }}</option>
                                <option value="sslcommerz" {{ request('method') === 'sslcommerz' ? 'selected' : '' }}>
                                    SSLCommerz</option>
                                <option value="bank_transfer"
                                    {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="trial" {{ request('method') === 'trial' ? 'selected' : '' }}>Trial</option>
                            </select>
                            <select name="status" class="form-control form-control-sm" style="width: auto;">
                                <option value="">{{ __tr('All Status') }}</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired
                                </option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed
                                </option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">{{ __tr('Search') }}</button>
                            @if (request('q') || request('status') || request('method'))
                                <a href="{{ route('admin.bank.payments') }}"
                                    class="btn btn-secondary btn-sm">{{ __tr('Reset') }}</a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __tr('Member') }}</th>
                                <th>{{ __tr('Plan') }}</th>
                                <th>{{ __tr('Amount') }}</th>
                                <th>{{ __tr('Method') }}</th>
                                <th>{{ __tr('Txn ID / Bank Ref') }}</th>
                                <th>{{ __tr('Slip') }}</th>
                                <th>{{ __tr('Status') }}</th>
                                <th>{{ __tr('Admin Note') }}</th>
                                <th>{{ __tr('Date') }}</th>
                                <th class="text-right">{{ __tr('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $key => $pay)
                                <tr @if ($pay->status === 'pending' && $pay->payment_method === 'bank_transfer') class="table-warning" @endif>
                                    <td>{{ $payments->firstItem() + $key }}</td>
                                    <td>
                                        <strong>{{ $pay->user->name ?? '—' }}</strong><br>
                                        <small class="text-muted">{{ $pay->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $pay->plan->title ?? '—' }}</td>
                                    <td><strong>{{ format_amount($pay->amount) }}</strong></td>
                                    <td>
                                        @if ($pay->payment_method === 'bank_transfer')
                                            <span class="badge badge-primary">Bank Transfer</span>
                                        @elseif ($pay->payment_method === 'sslcommerz')
                                            <span class="badge badge-info">SSLCommerz</span>
                                        @elseif ($pay->payment_method === 'trial')
                                            <span class="badge badge-secondary">Trial</span>
                                        @else
                                            <span class="badge badge-light">{{ ucfirst($pay->payment_method) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pay->bank_transaction_number)
                                            <small class="text-muted d-block">Bank Ref:</small>
                                            <code>{{ $pay->bank_transaction_number }}</code>
                                        @endif
                                        @if ($pay->transaction_id)
                                            <small class="text-muted d-block">Txn ID:</small>
                                            <code style="font-size: 0.72rem;">{{ $pay->transaction_id }}</code>
                                        @endif
                                        @if (!$pay->bank_transaction_number && !$pay->transaction_id)
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($pay->bank_slip)
                                            <a href="{{ asset('storage/' . $pay->bank_slip) }}" target="_blank"
                                                class="btn btn-info btn-xs" title="{{ __tr('View Slip') }}">
                                                <i class="fas fa-file-image"></i> {{ __tr('View') }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pay->status === 'active')
                                            <span class="badge badge-success">{{ __tr('Active') }}</span>
                                        @elseif ($pay->status === 'pending')
                                            <span class="badge badge-warning">{{ __tr('Pending') }}</span>
                                        @elseif ($pay->status === 'rejected')
                                            <span class="badge badge-danger">{{ __tr('Rejected') }}</span>
                                        @elseif ($pay->status === 'expired')
                                            <span class="badge badge-secondary">{{ __tr('Expired') }}</span>
                                        @elseif ($pay->status === 'failed')
                                            <span class="badge badge-dark">{{ __tr('Failed') }}</span>
                                        @else
                                            <span class="badge badge-light">{{ ucfirst($pay->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $pay->admin_note ?? '—' }}</small>
                                    </td>
                                    <td>{{ $pay->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $pay->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-right" style="white-space: nowrap;">
                                        @if ($pay->status === 'pending' && $pay->payment_method === 'bank_transfer')
                                            <button class="btn btn-success btn-sm approve-item"
                                                data-id="{{ $pay->id }}" data-name="{{ $pay->user->name ?? '' }}"
                                                data-plan="{{ $pay->plan->title ?? '' }}"
                                                data-ref="{{ $pay->bank_transaction_number }}"
                                                title="{{ __tr('Approve') }}">
                                                <i class="fas fa-check"></i> {{ __tr('Approve') }}
                                            </button>
                                            <button class="btn btn-warning btn-sm reject-item"
                                                data-id="{{ $pay->id }}" data-name="{{ $pay->user->name ?? '' }}"
                                                data-plan="{{ $pay->plan->title ?? '' }}"
                                                data-ref="{{ $pay->bank_transaction_number }}"
                                                title="{{ __tr('Reject') }}">
                                                <i class="fas fa-times"></i> {{ __tr('Reject') }}
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-3">
                                        {{ __tr('No transactions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($payments->hasPages())
                        <div class="p-3">
                            {{ $payments->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
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
                        <i class="fas fa-check-circle mr-2"></i>{{ __tr('Approve Payment') }}
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form method="POST" action="{{ route('admin.bank.payments.approve') }}">
                    @csrf
                    <input type="hidden" id="approve-id" name="id">
                    <div class="modal-body">
                        <p class="mb-1">
                            {{ __tr('Approve payment from') }} <strong id="approve-name"></strong>
                            {{ __tr('for plan') }} <strong id="approve-plan"></strong>?
                        </p>
                        <p class="text-muted mb-3">
                            {{ __tr('Bank Txn Ref:') }} <code id="approve-ref"></code>
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
                        <i class="fas fa-times-circle mr-2"></i>{{ __tr('Reject Payment') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form method="POST" action="{{ route('admin.bank.payments.reject') }}">
                    @csrf
                    <input type="hidden" id="reject-id" name="id">
                    <div class="modal-body">
                        <p class="mb-1">
                            {{ __tr('Reject payment from') }} <strong id="reject-name"></strong>
                            {{ __tr('for plan') }} <strong id="reject-plan"></strong>?
                        </p>
                        <p class="text-muted mb-3">
                            {{ __tr('Bank Txn Ref:') }} <code id="reject-ref"></code>
                        </p>
                        <div class="form-group mb-0">
                            <label>{{ __tr('Rejection Reason (sent to user)') }}</label>
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
@endsection

@section('page-script')
    <script>
        (function($) {
            "use strict";

            $('.approve-item').on('click', function() {
                $('#approve-id').val($(this).data('id'));
                $('#approve-name').text($(this).data('name'));
                $('#approve-plan').text($(this).data('plan'));
                $('#approve-ref').text($(this).data('ref') || '—');
                $('#approve-modal').modal('show');
            });

            $('.reject-item').on('click', function() {
                $('#reject-id').val($(this).data('id'));
                $('#reject-name').text($(this).data('name'));
                $('#reject-plan').text($(this).data('plan'));
                $('#reject-ref').text($(this).data('ref') || '—');
                $('#reject-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
