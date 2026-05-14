@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ $prefill['is_buffering'] ?? false ? __tr('Report Buffering') : __tr('Open New Ticket') }} -
        {{ get_setting('site_name') }}</title>
@endsection
@section('dashboard-content')
    <div class="dashboard-header">
        <h1 class="dash-page-title">
            @if ($prefill['is_buffering'] ?? false)
                {{ __tr('Report Buffering Issue') }}
            @else
                {{ __tr('Open New Support Ticket') }}
            @endif
        </h1>
        <a href="{{ route('member.tickets.index') }}" class="dash-back-link-muted">
            <i class="fas fa-arrow-left"></i> {{ __tr('Back to Tickets') }}
        </a>
    </div>

    @if ($prefill['is_buffering'] ?? false)
        <div class="buffering-alert">
            <i class="fas fa-circle-info buffering-alert-icon"></i>
            <div class="buffering-alert-text">
                {{ __tr('Please describe the buffering issue in as much detail as possible. Include the channel name, time of occurrence, and your device type. Our team will investigate and respond within 2 hours.') }}
            </div>
        </div>
    @endif

    <div class="dashboard-card">
        <form action="{{ route('member.tickets.store') }}" method="POST">
            @csrf

            <div class="ticket-form-body">
                <div class="full-col">
                    <label class="form-label-sm">
                        {{ __tr('Subject') }} <span class="required-star">*</span>
                    </label>
                    <input type="text" name="subject" required
                        class="form-control form-control-dark @error('subject') is-invalid @enderror"
                        value="{{ old('subject', $prefill['subject'] ?? '') }}"
                        placeholder="{{ __tr('Briefly describe your issue') }}">
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label-sm">
                        {{ __tr('Priority') }} <span class="required-star">*</span>
                    </label>
                    <select name="priority" class="form-control form-control-dark">
                        @foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('priority', $prefill['priority'] ?? 'normal') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label-sm">
                        {{ __tr('Department') }}
                    </label>
                    <select name="department" class="form-control form-control-dark">
                        <option value="">{{ __tr('General') }}</option>
                        <option value="billing"
                            {{ old('department', $prefill['department'] ?? '') === 'billing' ? 'selected' : '' }}>
                            {{ __tr('Billing') }}</option>
                        <option value="technical"
                            {{ old('department', $prefill['department'] ?? '') === 'technical' ? 'selected' : '' }}>
                            {{ __tr('Technical') }}</option>
                        <option value="buffering"
                            {{ old('department', $prefill['department'] ?? '') === 'buffering' ? 'selected' : '' }}>
                            {{ __tr('Buffering / Streaming Issue') }}</option>
                        <option value="sales"
                            {{ old('department', $prefill['department'] ?? '') === 'sales' ? 'selected' : '' }}>
                            {{ __tr('Sales') }}</option>
                    </select>
                </div>
            </div>

            {{-- Buffering-specific extra fields --}}
            @if ($prefill['is_buffering'] ?? false)
                <div class="ticket-extra-fields">
                    <div>
                        <label class="form-label-sm">
                            {{ __tr('Device Type') }}
                        </label>
                        <select name="device_type" class="form-control form-control-dark">
                            <option value="">{{ __tr('-- Select device --') }}</option>
                            <option value="firestick">Amazon Firestick / Fire TV</option>
                            <option value="android_tv">Android TV / Box</option>
                            <option value="smart_tv">Smart TV</option>
                            <option value="ios">iPhone / iPad</option>
                            <option value="desktop">Windows / Mac</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-sm">
                            {{ __tr('Channel Name (if applicable)') }}
                        </label>
                        <input type="text" name="channel_name" class="form-control form-control-dark"
                            placeholder="{{ __tr('e.g. ESPN, NFL Network') }}" value="{{ old('channel_name') }}">
                    </div>
                </div>
            @endif

            <div class="ticket-msg-body">
                <label class="form-label-sm">
                    {{ __tr('Message') }} <span class="required-star">*</span>
                </label>
                <textarea name="message" rows="7" required
                    class="form-control form-control-dark @error('message') is-invalid @enderror" style="resize:vertical;"
                    placeholder="{{ $prefill['is_buffering'] ?? false
                        ? __tr('Describe the buffering: when it started, how often, which channels, what you have tried...')
                        : __tr('Describe your issue in detail...') }}">{{ old('message') }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="ticket-actions">
                <button type="submit" class="cmn-btn">
                    <i class="fas fa-paper-plane"></i>
                    {{ $prefill['is_buffering'] ?? false ? __tr('Submit Buffering Report') : __tr('Submit Ticket') }}
                </button>
                <a href="{{ route('member.tickets.index') }}" class="cmn-btn cmn-btn-ghost">
                    {{ __tr('Cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
