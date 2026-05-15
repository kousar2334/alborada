@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('New Support Ticket') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <h1 class="dash-page-title">
            <i class="fas fa-plus-circle card-header-icon me-2"></i>{{ __tr('New Support Ticket') }}
        </h1>
        <p class="dash-page-subtitle">{{ __tr('Describe your issue and our team will respond as soon as possible.') }}</p>
    </div>

    <div class="ticket-form-wrap">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __tr('Ticket Details') }}</h3>
                <a href="{{ route('reseller.tickets.index') }}" class="ticket-form-back-link">
                    <i class="fas fa-arrow-left"></i> {{ __tr('Back to tickets') }}
                </a>
            </div>
            <div class="ticket-form-body">

                @if ($errors->any())
                    <div class="alert-error-dark">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reseller.tickets.store') }}" method="POST">
                    @csrf

                    <div class="ticket-form-mb">
                        <label class="ticket-field-label">
                            {{ __tr('Subject') }} <span class="ticket-required-star">*</span>
                        </label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                            class="form-control form-control-dark"
                            placeholder="{{ __tr('Brief description of your issue') }}">
                    </div>

                    <div class="ticket-form-grid">
                        <div>
                            <label class="ticket-field-label">
                                {{ __tr('Priority') }} <span class="ticket-required-star">*</span>
                            </label>
                            <select name="priority" required class="form-control form-control-dark">
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
                            <label class="ticket-field-label">
                                {{ __tr('Department') }}
                            </label>
                            <select name="department" class="form-control form-control-dark">
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

                    <div class="ticket-form-mb-lg">
                        <label class="ticket-field-label">
                            {{ __tr('Message') }} <span class="ticket-required-star">*</span>
                        </label>
                        <textarea name="message" rows="8" required minlength="10" class="form-control form-control-dark textarea-resize-v"
                            placeholder="{{ __tr('Please describe your issue in detail...') }}">{{ old('message') }}</textarea>
                    </div>

                    <div class="ticket-submit-row">
                        <button type="submit" class="cmn-btn cmn-btn-green">
                            <i class="fas fa-paper-plane"></i> {{ __tr('Submit Ticket') }}
                        </button>
                        <a href="{{ route('reseller.tickets.index') }}" class="ticket-cancel-btn">
                            {{ __tr('Cancel') }}
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
