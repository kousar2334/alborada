@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ $prefill['is_buffering'] ?? false ? __tr('Report Buffering') : __tr('Open New Ticket') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('dashboard-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title">
            @if($prefill['is_buffering'] ?? false)
                <i class="fas fa-wifi" style="color:#cc0000;margin-right:10px;"></i>{{ __tr('Report Buffering Issue') }}
            @else
                <i class="fas fa-ticket" style="margin-right:10px;"></i>{{ __tr('Open New Support Ticket') }}
            @endif
        </h1>
        <a href="{{ route('member.support.index') }}" style="color:var(--muted);text-decoration:none;font-size:.85rem;">
            <i class="fas fa-arrow-left"></i> {{ __tr('Back to Tickets') }}
        </a>
    </div>

    @if($prefill['is_buffering'] ?? false)
        <div style="background:rgba(204,0,0,.08);border:1px solid rgba(204,0,0,.2);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:flex-start;gap:12px;">
            <i class="fas fa-circle-info" style="color:#cc0000;margin-top:2px;flex-shrink:0;"></i>
            <div style="font-size:.85rem;color:rgba(255,255,255,.7);">
                {{ __tr('Please describe the buffering issue in as much detail as possible. Include the channel name, time of occurrence, and your device type. Our team will investigate and respond within 2 hours.') }}
            </div>
        </div>
    @endif

    <div class="dashboard-card">
        <form action="{{ route('member.support.store') }}" method="POST">
            @csrf

            <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div style="grid-column:1/-1;">
                    <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);display:block;margin-bottom:6px;">
                        {{ __tr('Subject') }} <span style="color:#cc0000;">*</span>
                    </label>
                    <input type="text" name="subject" required
                        class="form-control @error('subject') is-invalid @enderror"
                        style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;"
                        value="{{ old('subject', $prefill['subject'] ?? '') }}"
                        placeholder="{{ __tr('Briefly describe your issue') }}">
                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);display:block;margin-bottom:6px;">
                        {{ __tr('Priority') }} <span style="color:#cc0000;">*</span>
                    </label>
                    <select name="priority" class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;">
                        @foreach(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', $prefill['priority'] ?? 'normal') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);display:block;margin-bottom:6px;">
                        {{ __tr('Department') }}
                    </label>
                    <select name="department" class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;">
                        <option value="">{{ __tr('General') }}</option>
                        <option value="billing"   {{ old('department', $prefill['department'] ?? '') === 'billing'   ? 'selected' : '' }}>{{ __tr('Billing') }}</option>
                        <option value="technical" {{ old('department', $prefill['department'] ?? '') === 'technical' ? 'selected' : '' }}>{{ __tr('Technical') }}</option>
                        <option value="buffering" {{ old('department', $prefill['department'] ?? '') === 'buffering' ? 'selected' : '' }}>{{ __tr('Buffering / Streaming Issue') }}</option>
                        <option value="sales"     {{ old('department', $prefill['department'] ?? '') === 'sales'     ? 'selected' : '' }}>{{ __tr('Sales') }}</option>
                    </select>
                </div>
            </div>

            {{-- Buffering-specific extra fields --}}
            @if($prefill['is_buffering'] ?? false)
                <div style="padding:0 20px 16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;border-top:1px solid rgba(255,255,255,.06);padding-top:16px;">
                    <div>
                        <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);display:block;margin-bottom:6px;">
                            {{ __tr('Device Type') }}
                        </label>
                        <select name="device_type" class="form-control" style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;">
                            <option value="">{{ __tr('-- Select device --') }}</option>
                            <option value="firestick">Amazon Firestick / Fire TV</option>
                            <option value="android_tv">Android TV / Box</option>
                            <option value="smart_tv">Smart TV</option>
                            <option value="ios">iPhone / iPad</option>
                            <option value="desktop">Windows / Mac</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);display:block;margin-bottom:6px;">
                            {{ __tr('Channel Name (if applicable)') }}
                        </label>
                        <input type="text" name="channel_name" class="form-control"
                            style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;"
                            placeholder="{{ __tr('e.g. ESPN, NFL Network') }}" value="{{ old('channel_name') }}">
                    </div>
                </div>
            @endif

            <div style="padding:0 20px 20px;">
                <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);display:block;margin-bottom:6px;">
                    {{ __tr('Message') }} <span style="color:#cc0000;">*</span>
                </label>
                <textarea name="message" rows="7" required
                    class="form-control @error('message') is-invalid @enderror"
                    style="background:#111;border:1px solid rgba(255,255,255,.1);color:#fff;resize:vertical;"
                    placeholder="{{ $prefill['is_buffering'] ?? false
                        ? __tr('Describe the buffering: when it started, how often, which channels, what you have tried...')
                        : __tr('Describe your issue in detail...') }}">{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="padding:0 20px 20px;display:flex;gap:12px;">
                <button type="submit" class="cmn-btn">
                    <i class="fas fa-paper-plane"></i>
                    {{ $prefill['is_buffering'] ?? false ? __tr('Submit Buffering Report') : __tr('Submit Ticket') }}
                </button>
                <a href="{{ route('member.support.index') }}" class="cmn-btn" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);">
                    {{ __tr('Cancel') }}
                </a>
            </div>
        </form>
    </div>

@endsection
