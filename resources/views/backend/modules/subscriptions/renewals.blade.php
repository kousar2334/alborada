@php
    $links = [
        [
            'title' => 'Subscriptions',
            'route' => 'admin.subscriptions.list',
            'active' => false,
        ],
        [
            'title' => 'Renewals',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Renewals
@endsection

@section('page-style')
    @parent
    <style>
        .rn-slip-link {
            font-size: .78rem;
        }

        .rn-txn {
            font-size: .76rem;
        }

        .rn-note {
            display: block;
            max-width: 220px;
        }
    </style>
@endsection

@section('page-content')
    <x-admin-page-header title="Renewals" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Stats Row --}}
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>{{ __tr('Total Renewals') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-redo-alt"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['paid'] }}</h3>
                            <p>{{ __tr('Paid') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['pending'] }}</h3>
                            <p>{{ __tr('Awaiting Review') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ format_amount($stats['revenue']) }}</h3>
                            <p>{{ __tr('Renewal Revenue') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-coins"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Renewal History') }}</h3>
                            <div class="card-tools">
                                <form method="GET" action="{{ route('admin.subscriptions.renewals') }}"
                                    class="form-inline">
                                    <input type="text" name="q" class="form-control form-control-sm mr-1"
                                        placeholder="{{ __tr('Customer or reference...') }}" value="{{ request('q') }}">
                                    <select name="status" class="form-control form-control-sm w-auto mr-1">
                                        <option value="">{{ __tr('All Status') }}</option>
                                        @foreach (['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'rejected' => 'Rejected'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request('status') === $value ? 'selected' : '' }}>{{ __tr($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="method" class="form-control form-control-sm w-auto mr-1">
                                        <option value="">{{ __tr('All Methods') }}</option>
                                        @foreach (['stripe' => 'Stripe', 'bank_transfer' => 'Bank Transfer', 'manual' => 'Manual'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request('method') === $value ? 'selected' : '' }}>{{ __tr($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __tr('Search') }}</button>
                                    @if (request('q') || request('status') || request('method'))
                                        <a href="{{ route('admin.subscriptions.renewals') }}"
                                            class="btn btn-secondary btn-sm ml-1">{{ __tr('Reset') }}</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Customer') }}</th>
                                        <th>{{ __tr('Plan') }}</th>
                                        <th>{{ __tr('Reference') }}</th>
                                        <th>{{ __tr('Amount') }}</th>
                                        <th>{{ __tr('Method') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th>{{ __tr('Period') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($renewals as $key => $renewal)
                                        <tr>
                                            <td>{{ $renewals->firstItem() + $key }}</td>
                                            <td>
                                                <strong>{{ $renewal->user->name ?? '—' }}</strong><br>
                                                <small class="text-muted">{{ $renewal->user->email ?? '' }}</small>
                                            </td>
                                            <td>{{ $renewal->plan->title ?? '—' }}</td>
                                            <td>
                                                <code class="rn-txn">{{ $renewal->transaction_id }}</code>
                                                @if ($renewal->bank_transaction_number)
                                                    <br><small
                                                        class="text-muted">{{ __tr('Ref:') }}
                                                        {{ $renewal->bank_transaction_number }}</small>
                                                @endif
                                                @if ($renewal->bank_slip)
                                                    <br><a href="{{ asset('storage/' . $renewal->bank_slip) }}"
                                                        target="_blank" rel="noopener" class="rn-slip-link">
                                                        <i class="fas fa-file-invoice"></i> {{ __tr('View slip') }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($renewal->amount > 0)
                                                    <strong>{{ format_amount($renewal->amount) }}</strong>
                                                @else
                                                    <span class="badge badge-success">{{ __tr('Free') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($renewal->payment_method === 'stripe')
                                                    <span class="badge badge-info">Stripe</span>
                                                    @if ($renewal->stripe_mode === 'test')
                                                        <br><span
                                                            class="badge badge-warning">{{ __tr('Sandbox') }}</span>
                                                    @endif
                                                @elseif ($renewal->payment_method === 'bank_transfer')
                                                    <span class="badge badge-secondary">{{ __tr('Bank Transfer') }}</span>
                                                @else
                                                    <span class="badge badge-primary">{{ __tr('Manual') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($renewal->status === 'paid')
                                                    <span class="badge badge-success">{{ __tr('Paid') }}</span>
                                                @elseif ($renewal->status === 'pending')
                                                    <span class="badge badge-warning">{{ __tr('Pending') }}</span>
                                                @elseif ($renewal->status === 'failed')
                                                    <span class="badge badge-danger">{{ __tr('Failed') }}</span>
                                                @else
                                                    <span class="badge badge-dark">{{ __tr('Rejected') }}</span>
                                                @endif
                                                @if ($renewal->admin_note)
                                                    <small class="text-muted rn-note text-truncate"
                                                        title="{{ $renewal->admin_note }}">
                                                        {{ Str::limit($renewal->admin_note, 40) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($renewal->new_expires_at)
                                                    <small class="text-muted">
                                                        {{ $renewal->previous_expires_at?->format('M d, Y') ?? '—' }}
                                                        <i class="fas fa-arrow-right"></i>
                                                    </small><br>
                                                    <strong>{{ $renewal->new_expires_at->format('M d, Y') }}</strong>
                                                @else
                                                    <small class="text-muted">{{ $renewal->days }}
                                                        {{ __tr('days pending') }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $renewal->created_at->format('M d, Y') }}</td>
                                            <td class="text-right text-nowrap">
                                                @if ($renewal->status === 'pending' && $renewal->payment_method === 'stripe')
                                                    <small class="text-muted">{{ __tr('Awaiting card payment') }}</small>
                                                @elseif ($renewal->status === 'pending')
                                                    <button class="btn btn-success btn-sm approve-renewal"
                                                        data-id="{{ $renewal->id }}"
                                                        data-user="{{ $renewal->user->name ?? '' }}"
                                                        title="{{ __tr('Approve and extend') }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm reject-renewal"
                                                        data-id="{{ $renewal->id }}"
                                                        data-user="{{ $renewal->user->name ?? '' }}"
                                                        title="{{ __tr('Reject') }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @elseif ($renewal->invoice_id)
                                                    <span class="text-muted rn-txn">
                                                        {{ __tr('Invoice #') }}{{ $renewal->invoice_id }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10">
                                                <div class="text-center py-3 text-muted">
                                                    {{ __tr('No renewals found') }}</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($renewals->hasPages())
                                <div class="p-3">
                                    {{ $renewals->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Approve Renewal Modal --}}
    <div class="modal fade" id="approve-renewal-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.subscriptions.renewals.approve') }}">
                    @csrf
                    <input type="hidden" id="approve-renewal-id" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title h6"><i class="fas fa-check-circle mr-1"></i>
                            {{ __tr('Approve Renewal') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __tr('Confirm the payment from') }} <strong id="approve-renewal-user"></strong>
                            {{ __tr('was received?') }}</p>
                        <div class="form-group mb-0">
                            <label for="approve-renewal-note">{{ __tr('Note (optional)') }}</label>
                            <textarea name="admin_note" id="approve-renewal-note" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                        <small class="text-muted d-block mt-2">
                            {{ __tr('The subscription period and the streaming account are extended immediately, and a receipt is emailed.') }}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __tr('Approve & Extend') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Renewal Modal --}}
    <div class="modal fade" id="reject-renewal-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.subscriptions.renewals.reject') }}">
                    @csrf
                    <input type="hidden" id="reject-renewal-id" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title h6"><i class="fas fa-times-circle mr-1"></i>
                            {{ __tr('Reject Renewal') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __tr('Reject the renewal submitted by') }} <strong id="reject-renewal-user"></strong>?</p>
                        <div class="form-group mb-0">
                            <label for="reject-renewal-note">{{ __tr('Reason') }}</label>
                            <textarea name="admin_note" id="reject-renewal-note" class="form-control" rows="2" maxlength="500"
                                placeholder="{{ __tr('e.g. transfer not found on the statement') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __tr('Reject') }}</button>
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

            $('.approve-renewal').on('click', function() {
                $('#approve-renewal-id').val($(this).data('id'));
                $('#approve-renewal-user').text($(this).data('user'));
                $('#approve-renewal-modal').modal('show');
            });

            $('.reject-renewal').on('click', function() {
                $('#reject-renewal-id').val($(this).data('id'));
                $('#reject-renewal-user').text($(this).data('user'));
                $('#reject-renewal-modal').modal('show');
            });

        })(jQuery);
    </script>
@endsection
