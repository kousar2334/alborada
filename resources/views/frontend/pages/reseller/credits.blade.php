@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('Credits & Wallet') }} - {{ get_setting('site_name') }}</title>
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
