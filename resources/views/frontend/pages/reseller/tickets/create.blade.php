@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('New Support Ticket') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title"><i class="fas fa-plus-circle"
                style="color:#00d46a;margin-right:10px;"></i>{{ __tr('New Support Ticket') }}</h1>
        <p class="dash-page-subtitle">{{ __tr('Describe your issue and our team will respond as soon as possible.') }}</p>
    </div>

    <div style="max-width:720px;">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Ticket Details') }}</h3>
                <a href="{{ route('reseller.tickets.index') }}"
                    style="font-size:.8rem;color:var(--muted);text-decoration:none;">
                    <i class="fas fa-arrow-left"></i> {{ __tr('Back to tickets') }}
                </a>
            </div>
            <div style="padding:20px;">

                @if ($errors->any())
                    <div
                        style="background:rgba(204,0,0,.12);border:1px solid rgba(204,0,0,.3);color:#ff6b6b;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.85rem;">
                        <ul style="margin:0;padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reseller.tickets.store') }}" method="POST">
                    @csrf

                    <div style="margin-bottom:16px;">
                        <label
                            style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);display:block;margin-bottom:6px;">
                            {{ __tr('Subject') }} <span style="color:#cc0000;">*</span>
                        </label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                            class="form-control"
                            style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:6px;"
                            placeholder="{{ __tr('Brief description of your issue') }}">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div>
                            <label
                                style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);display:block;margin-bottom:6px;">
                                {{ __tr('Priority') }} <span style="color:#cc0000;">*</span>
                            </label>
                            <select name="priority" required class="form-control"
                                style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:6px;">
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                    {{ __tr('Low') }}</option>
                                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>
                                    {{ __tr('Normal') }}</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                    {{ __tr('High') }}</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                    {{ __tr('Urgent') }}</option>
                            </select>
                        </div>
                        <div>
                            <label
                                style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);display:block;margin-bottom:6px;">
                                {{ __tr('Department') }}
                            </label>
                            <select name="department" class="form-control"
                                style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:6px;">
                                <option value="general" {{ old('department', 'general') === 'general' ? 'selected' : '' }}>
                                    {{ __tr('General') }}</option>
                                <option value="billing" {{ old('department') === 'billing' ? 'selected' : '' }}>
                                    {{ __tr('Billing') }}</option>
                                <option value="technical" {{ old('department') === 'technical' ? 'selected' : '' }}>
                                    {{ __tr('Technical') }}</option>
                                <option value="sales" {{ old('department') === 'sales' ? 'selected' : '' }}>
                                    {{ __tr('Sales') }}</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:24px;">
                        <label
                            style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);display:block;margin-bottom:6px;">
                            {{ __tr('Message') }} <span style="color:#cc0000;">*</span>
                        </label>
                        <textarea name="message" rows="8" required minlength="10" class="form-control"
                            style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:6px;resize:vertical;"
                            placeholder="{{ __tr('Please describe your issue in detail...') }}">{{ old('message') }}</textarea>
                    </div>

                    <div style="display:flex;gap:12px;">
                        <button type="submit" class="cmn-btn"
                            style="background:#00d46a;color:#000;font-weight:700;padding:12px 28px;">
                            <i class="fas fa-paper-plane"></i> {{ __tr('Submit Ticket') }}
                        </button>
                        <a href="{{ route('reseller.tickets.index') }}"
                            style="padding:12px 24px;border-radius:6px;border:1px solid rgba(255,255,255,.1);color:var(--muted);text-decoration:none;font-size:.88rem;display:inline-flex;align-items:center;">
                            {{ __tr('Cancel') }}
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
