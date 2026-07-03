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
                            <p>Pending</p>
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
                            <h3 class="card-title">
                                {{ __tr('All Subscriptions') }}
                                <button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal"
                                    data-target="#assign-subscription-modal">
                                    <i class="fas fa-plus"></i> {{ __tr('Assign Subscription') }}
                                </button>
                            </h3>
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
                                        <option value="stripe" {{ request('method') === 'stripe' ? 'selected' : '' }}>
                                            Stripe</option>
                                        <option value="trial" {{ request('method') === 'trial' ? 'selected' : '' }}>Trial
                                        </option>
                                        <option value="manual" {{ request('method') === 'manual' ? 'selected' : '' }}>
                                            Manual</option>
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
                                        <tr>
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
                                                @if ($sub->payment_method === 'stripe')
                                                    <span class="badge badge-info">Stripe</span>
                                                @elseif ($sub->payment_method === 'manual')
                                                    <span class="badge badge-primary">Manual</span>
                                                @else
                                                    <span
                                                        class="badge badge-secondary">{{ ucfirst($sub->payment_method ?? 'Trial') }}</span>
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
                                                @if ($sub->status === 'active' && get_setting('iptv_provisioning_enabled', 0))
                                                    <button class="btn btn-info btn-sm reprovision-item"
                                                        data-id="{{ $sub->id }}"
                                                        data-user="{{ $sub->user->name ?? '' }}"
                                                        title="{{ __tr('Re-provision IPTV') }}">
                                                        <i class="fas fa-sync-alt"></i>
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

    {{-- Assign Subscription Modal --}}
    <div class="modal fade" id="assign-subscription-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.subscriptions.assign') }}">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title h6">
                            <i class="fas fa-user-plus mr-1"></i> {{ __tr('Assign Subscription to Customer') }}
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="assign-user-id">{{ __tr('Customer') }} <span class="text-danger">*</span></label>
                            <select name="user_id" id="assign-user-id" class="form-control" required>
                                <option value="">{{ __tr('Select a customer...') }}</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assign-plan-id">{{ __tr('Plan') }} <span class="text-danger">*</span></label>
                            <select name="plan_id" id="assign-plan-id" class="form-control" required>
                                <option value="">{{ __tr('Select a plan...') }}</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->title }} — {{ format_amount($plan->price) }}
                                        ({{ $plan->duration_days }} {{ __tr('days') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="assign-admin-note">{{ __tr('Note (optional)') }}</label>
                            <textarea name="admin_note" id="assign-admin-note" class="form-control" rows="2"
                                maxlength="500" placeholder="{{ __tr('e.g. paid in cash, promotional access...') }}"></textarea>
                        </div>
                        <small class="text-muted d-block mt-2">
                            {{ __tr('The subscription is activated immediately and the customer is notified by email.') }}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __tr('Assign & Activate') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Re-provision Confirmation Modal --}}
    <div class="modal fade" id="reprovision-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6"><i class="fas fa-sync-alt mr-1"></i> {{ __tr('Re-provision IPTV') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">{{ __tr('Re-generate IPTV credentials for') }} <strong
                            id="reprovision-user"></strong>?</p>
                    <form method="POST" action="{{ route('admin.subscriptions.reprovision') }}">
                        @csrf
                        <input type="hidden" id="reprovision-id" name="id">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-info ml-1">{{ __tr('Re-provision') }}</button>
                    </form>
                </div>
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

            $('.reprovision-item').on('click', function() {
                $('#reprovision-id').val($(this).data('id'));
                $('#reprovision-user').text($(this).data('user'));
                $('#reprovision-modal').modal('show');
            });

        })(jQuery);
    </script>
@endsection
