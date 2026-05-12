@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('Credits & Wallet') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title"><i class="fas fa-wallet" style="color:#00d46a;margin-right:10px;"></i>{{ __tr('Credits & Wallet') }}</h1>
        <p class="dash-page-subtitle">{{ __tr('Manage your credit balance, request top-ups, and transfer to sub-resellers.') }}</p>
    </div>

    {{-- Credit Balance Card --}}
    <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
        <div class="stat-card" style="background:linear-gradient(135deg,rgba(0,212,106,.1),rgba(0,0,0,0));border:1px solid rgba(0,212,106,.25);">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Available Credits') }}</span>
                <div class="stat-icon" style="background:rgba(0,212,106,.15);color:#00d46a;"><i class="fas fa-coins"></i></div>
            </div>
            <div class="stat-value" style="color:#00d46a;">{{ number_format($reseller->credits, 2) }}</div>
            <div class="stat-change positive"><i class="fas fa-circle-check"></i> {{ __tr('Credits') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Total Credited') }}</span>
                <div class="stat-icon blue"><i class="fas fa-arrow-down"></i></div>
            </div>
            <div class="stat-value">{{ number_format($logs->where('type','credit')->sum('amount'), 0) }}</div>
            <div class="stat-change positive"><i class="fas fa-plus"></i> {{ __tr('all time') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">{{ __tr('Total Spent') }}</span>
                <div class="stat-icon" style="background:rgba(204,0,0,.1);color:#cc0000;"><i class="fas fa-arrow-up"></i></div>
            </div>
            <div class="stat-value">{{ number_format($logs->where('type','debit')->sum('amount'), 0) }}</div>
            <div class="stat-change"><i class="fas fa-minus"></i> {{ __tr('all time') }}</div>
        </div>
    </div>

    <div class="content-grid" style="margin-bottom:24px;">

        {{-- Request Top-Up --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle" style="color:#00d46a;margin-right:6px;"></i>{{ __tr('Request Credit Top-Up') }}</h3>
            </div>
            <div style="padding:16px;">
                <p style="font-size:.85rem;color:var(--muted);margin-bottom:16px;">{{ __tr('Submit a top-up request. The admin will review and add credits to your account.') }}</p>
                <form action="{{ route('reseller.credits.topup') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);">{{ __tr('Amount (Credits)') }}</label>
                        <input type="number" name="amount" min="1" max="10000" step="1" required
                            class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;margin-top:4px;"
                            placeholder="{{ __tr('e.g. 100') }}" value="{{ old('amount') }}">
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);">{{ __tr('Message (Optional)') }}</label>
                        <textarea name="message" rows="3" class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;resize:none;margin-top:4px;"
                            placeholder="{{ __tr('Any notes for the admin...') }}">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="cmn-btn" style="background:#00d46a;color:#000;width:100%;font-weight:700;">
                        <i class="fas fa-paper-plane"></i> {{ __tr('Send Request') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Transfer to Sub-Reseller --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-share-nodes" style="color:var(--primary-color);margin-right:6px;"></i>{{ __tr('Transfer Credits') }}</h3>
            </div>
            <div style="padding:16px;">
                @if($subResellers->isEmpty())
                    <div style="text-align:center;padding:24px 0;color:var(--muted);">
                        <i class="fas fa-users" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                        <p style="font-size:.85rem;">{{ __tr('You have no sub-resellers to transfer credits to.') }}</p>
                        <p style="font-size:.8rem;opacity:.7;">{{ __tr('Contact admin to set up sub-reseller accounts under your profile.') }}</p>
                    </div>
                @else
                    <p style="font-size:.85rem;color:var(--muted);margin-bottom:16px;">{{ __tr('Transfer credits from your balance to a sub-reseller account.') }}</p>
                    <form action="{{ route('reseller.credits.transfer') }}" method="POST">
                        @csrf
                        <div style="margin-bottom:12px;">
                            <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);">{{ __tr('Sub-Reseller') }}</label>
                            <select name="sub_reseller_id" required class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;margin-top:4px;">
                                <option value="">{{ __tr('-- Select sub-reseller --') }}</option>
                                @foreach($subResellers as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->name }} ({{ $sr->credits }} {{ __tr('credits') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);">{{ __tr('Amount') }}</label>
                            <input type="number" name="amount" min="1" max="{{ $reseller->credits }}" step="1" required
                                class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;margin-top:4px;"
                                placeholder="{{ __tr('Credits to transfer') }}">
                            <small style="color:rgba(255,255,255,.4);font-size:.72rem;">{{ __tr('Your balance:') }} {{ number_format($reseller->credits, 0) }} {{ __tr('credits') }}</small>
                        </div>
                        <button type="submit" class="cmn-btn" style="width:100%;font-weight:700;"
                            onclick="return confirm('{{ __tr('Confirm credit transfer?') }}')">
                            <i class="fas fa-share-nodes"></i> {{ __tr('Transfer Credits') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

    {{-- Transaction History --}}
    <div class="dashboard-card p-0">
        <div class="card-header">
            <h3 class="card-title">{{ __tr('Transaction History') }}</h3>
            <span style="font-size:.75rem;color:var(--muted);">{{ $logs->total() }} {{ __tr('records') }}</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,.07);">
                        <th style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">{{ __tr('Date') }}</th>
                        <th style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">{{ __tr('Type') }}</th>
                        <th style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:right;">{{ __tr('Amount') }}</th>
                        <th style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:right;">{{ __tr('Balance After') }}</th>
                        <th style="padding:10px 16px;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);text-align:left;">{{ __tr('Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr style="border-bottom:1px solid rgba(255,255,255,.04);">
                            <td style="padding:12px 16px;font-size:.82rem;color:var(--muted);">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td style="padding:12px 16px;">
                                @if($log->type === 'credit')
                                    <span style="background:rgba(0,212,106,.12);color:#00d46a;padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:700;">
                                        <i class="fas fa-plus"></i> {{ __tr('Credit') }}
                                    </span>
                                @else
                                    <span style="background:rgba(204,0,0,.12);color:#cc0000;padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:700;">
                                        <i class="fas fa-minus"></i> {{ __tr('Debit') }}
                                    </span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:{{ $log->type === 'credit' ? '#00d46a' : '#cc0000' }};">
                                {{ $log->type === 'credit' ? '+' : '-' }}{{ number_format($log->amount, 2) }}
                            </td>
                            <td style="padding:12px 16px;text-align:right;font-size:.85rem;color:var(--muted);">{{ number_format($log->balance_after, 2) }}</td>
                            <td style="padding:12px 16px;font-size:.82rem;color:var(--muted);">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:40px;text-align:center;color:var(--muted);">
                                <i class="fas fa-receipt" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                                {{ __tr('No transactions yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div style="padding:16px;border-top:1px solid rgba(255,255,255,.07);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

@endsection
