@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('Credits & Wallet') }} - {{ get_setting('site_name') }}</title>
    <style>
        .stat-icon-green {
            background: rgba(0, 212, 106, .15);
            color: #00d46a;
        }

        .stat-icon-red {
            background: rgba(204, 0, 0, .1);
            color: #cc0000;
        }

        .stat-card-highlight {
            background: linear-gradient(135deg, rgba(0, 212, 106, .08), rgba(0, 212, 106, 0));
            border: 1px solid rgba(0, 212, 106, .25);
        }

        .stat-value-green {
            color: #00d46a;
        }

        .cred-form-section {
            padding: 1.25rem;
        }

        .cred-form-section p {
            font-size: .85rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .cred-form-group {
            margin-bottom: .875rem;
        }

        .cred-form-label {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            margin-bottom: .35rem;
        }

        .cred-form-group .form-control {
            border-color: #e5e7eb;
            border-radius: 8px;
            font-size: .875rem;
            color: #1f2937;
            background: #fff;
        }

        .cred-form-group .form-control:focus {
            border-color: #00c853;
            box-shadow: 0 0 0 3px rgba(0, 200, 83, .1);
        }

        .cred-form-hint {
            font-size: .75rem;
            color: #9ca3af;
            margin-top: .3rem;
        }

        .cred-submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            padding: .72rem 1rem;
            background: linear-gradient(135deg, #00a046 0%, #00c853 100%);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-weight: 700;
            font-size: .875rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 200, 83, .2);
            transition: opacity .18s, box-shadow .18s;
        }

        .cred-submit-btn:hover {
            opacity: .9;
            box-shadow: 0 4px 14px rgba(0, 200, 83, .3);
            color: #fff;
        }

        .cred-submit-btn.secondary {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        }

        .cred-submit-btn.secondary:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, .15);
        }

        .cred-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: #9ca3af;
        }

        .cred-empty-icon {
            font-size: 2rem;
            opacity: .35;
            display: block;
            margin-bottom: .75rem;
            color: #d1d5db;
        }

        .cred-empty p {
            font-size: .85rem;
            margin: 0 0 .35rem;
            color: #9ca3af;
        }

        .cred-empty p.cred-empty-sub {
            font-size: .8rem;
            opacity: .75;
        }

        /* Transaction table */
        .txn-table {
            width: 100%;
            border-collapse: collapse;
        }

        .txn-table thead th {
            padding: .75rem 1rem;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            background: #fafffe;
            border-bottom: 1px solid #e9f5ef;
            text-align: left;
        }

        .txn-table thead th.text-right {
            text-align: right;
        }

        .txn-table tbody td {
            padding: .875rem 1rem;
            font-size: .85rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .txn-table tbody tr:last-child td {
            border-bottom: none;
        }

        .txn-table tbody tr:hover td {
            background: #f9fffe;
        }

        .txn-table td.text-right {
            text-align: right;
        }

        .txn-date {
            color: #6b7280;
            font-size: .8rem;
        }

        .txn-amount-credit {
            font-weight: 700;
            color: #00d46a;
        }

        .txn-amount-debit {
            font-weight: 700;
            color: #cc0000;
        }

        .txn-balance {
            color: #6b7280;
        }

        .txn-desc {
            color: #6b7280;
            font-size: .82rem;
        }

        .txn-badge-credit {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            background: rgba(0, 212, 106, .12);
            color: #00a046;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: .72rem;
            font-weight: 700;
        }

        .txn-badge-debit {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            background: rgba(204, 0, 0, .1);
            color: #cc0000;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: .72rem;
            font-weight: 700;
        }

        .txn-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #9ca3af;
        }

        .txn-empty i {
            font-size: 2rem;
            opacity: .3;
            display: block;
            margin-bottom: .75rem;
        }

        .txn-pagination {
            padding: 1rem;
            border-top: 1px solid #f0fdf4;
        }

        .txn-records-count {
            font-size: .75rem;
            color: #6b7280;
        }

        .card-header-icon {
            color: #00d46a;
        }
    </style>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <div>
            <h1 class="dash-page-title">
                <i class="fas fa-wallet card-header-icon"></i>
                {{ __tr('Credits & Wallet') }}
            </h1>
            <p class="dash-page-subtitle">
                {{ __tr('Manage your credit balance, request top-ups, and transfer to sub-resellers.') }}</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid mb-4">
        <div class="stat-card stat-card-highlight">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Available Credits') }}</span>
                <div class="stat-icon stat-icon-green"><i class="fas fa-coins"></i></div>
            </div>
            <div class="stat-value stat-value-green">{{ number_format($reseller->credits, 2) }}</div>
            <div class="stat-change positive"><i class="fas fa-circle-check"></i> {{ __tr('Credits') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Total Credited') }}</span>
                <div class="stat-icon blue"><i class="fas fa-arrow-down"></i></div>
            </div>
            <div class="stat-value">{{ number_format($logs->where('type', 'credit')->sum('amount'), 0) }}</div>
            <div class="stat-change positive"><i class="fas fa-plus"></i> {{ __tr('all time') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Total Spent') }}</span>
                <div class="stat-icon stat-icon-red"><i class="fas fa-arrow-up"></i></div>
            </div>
            <div class="stat-value">{{ number_format($logs->where('type', 'debit')->sum('amount'), 0) }}</div>
            <div class="stat-change"><i class="fas fa-minus"></i> {{ __tr('all time') }}</div>
        </div>
    </div>

    <div class="content-grid mb-4">

        {{-- Request Top-Up --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle card-header-icon"></i>
                    {{ __tr('Request Credit Top-Up') }}
                </h3>
            </div>
            <div class="cred-form-section">
                <p>{{ __tr('Submit a top-up request. The admin will review and add credits to your account.') }}</p>
                <form action="{{ route('reseller.credits.topup') }}" method="POST">
                    @csrf
                    <div class="cred-form-group">
                        <label class="cred-form-label">{{ __tr('Amount (Credits)') }}</label>
                        <input type="number" name="amount" min="1" max="10000" step="1" required
                            class="form-control" placeholder="{{ __tr('e.g. 100') }}" value="{{ old('amount') }}">
                    </div>
                    <div class="cred-form-group">
                        <label class="cred-form-label">{{ __tr('Message (Optional)') }}</label>
                        <textarea name="message" rows="3" class="form-control" placeholder="{{ __tr('Any notes for the admin...') }}">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="cred-submit-btn">
                        <i class="fas fa-paper-plane"></i> {{ __tr('Send Request') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Transfer to Sub-Reseller --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-share-nodes card-header-icon"></i>
                    {{ __tr('Transfer Credits') }}
                </h3>
            </div>
            <div class="cred-form-section">
                @if ($subResellers->isEmpty())
                    <div class="cred-empty">
                        <i class="fas fa-users cred-empty-icon"></i>
                        <p>{{ __tr('You have no sub-resellers to transfer credits to.') }}</p>
                        <p class="cred-empty-sub">
                            {{ __tr('Contact admin to set up sub-reseller accounts under your profile.') }}</p>
                    </div>
                @else
                    <p>{{ __tr('Transfer credits from your balance to a sub-reseller account.') }}</p>
                    <form action="{{ route('reseller.credits.transfer') }}" method="POST">
                        @csrf
                        <div class="cred-form-group">
                            <label class="cred-form-label">{{ __tr('Sub-Reseller') }}</label>
                            <select name="sub_reseller_id" required class="form-control">
                                <option value="">{{ __tr('-- Select sub-reseller --') }}</option>
                                @foreach ($subResellers as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->name }} ({{ $sr->credits }}
                                        {{ __tr('credits') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cred-form-group">
                            <label class="cred-form-label">{{ __tr('Amount') }}</label>
                            <input type="number" name="amount" min="1" max="{{ $reseller->credits }}"
                                step="1" required class="form-control"
                                placeholder="{{ __tr('Credits to transfer') }}">
                            <p class="cred-form-hint">{{ __tr('Your balance:') }}
                                {{ number_format($reseller->credits, 0) }} {{ __tr('credits') }}</p>
                        </div>
                        <button type="submit" class="cred-submit-btn secondary"
                            onclick="return confirm('{{ __tr('Confirm credit transfer?') }}')">
                            <i class="fas fa-share-nodes"></i> {{ __tr('Transfer Credits') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

    {{-- Transaction History --}}
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">{{ __tr('Transaction History') }}</h3>
            <span class="txn-records-count">{{ $logs->total() }} {{ __tr('records') }}</span>
        </div>
        <div class="table-responsive">
            <table class="txn-table">
                <thead>
                    <tr>
                        <th>{{ __tr('Date') }}</th>
                        <th>{{ __tr('Type') }}</th>
                        <th class="text-right">{{ __tr('Amount') }}</th>
                        <th class="text-right">{{ __tr('Balance After') }}</th>
                        <th>{{ __tr('Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="txn-date">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if ($log->type === 'credit')
                                    <span class="txn-badge-credit">
                                        <i class="fas fa-plus"></i> {{ __tr('Credit') }}
                                    </span>
                                @else
                                    <span class="txn-badge-debit">
                                        <i class="fas fa-minus"></i> {{ __tr('Debit') }}
                                    </span>
                                @endif
                            </td>
                            <td
                                class="text-right {{ $log->type === 'credit' ? 'txn-amount-credit' : 'txn-amount-debit' }}">
                                {{ $log->type === 'credit' ? '+' : '-' }}{{ number_format($log->amount, 2) }}
                            </td>
                            <td class="text-right txn-balance">{{ number_format($log->balance_after, 2) }}</td>
                            <td class="txn-desc">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="txn-empty">
                                    <i class="fas fa-receipt"></i>
                                    {{ __tr('No transactions yet.') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="txn-pagination">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

@endsection
