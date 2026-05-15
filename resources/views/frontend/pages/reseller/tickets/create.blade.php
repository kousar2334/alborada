@extends('frontend.layouts.reseller-dashboard')
@section('reseller-meta')
    <title>{{ __tr('New Support Ticket') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('reseller-content')

    <div class="dashboard-header">
        <div>
            <div class="nt-breadcrumb">
                <a href="{{ route('reseller.tickets.index') }}" class="nt-breadcrumb-link">
                    <i class="fas fa-headset"></i> {{ __tr('Support') }}
                </a>
                <i class="fas fa-chevron-right nt-breadcrumb-sep"></i>
                <span class="nt-breadcrumb-current">{{ __tr('New Ticket') }}</span>
            </div>
            <h1 class="dash-page-title">{{ __tr('Open a Support Ticket') }}</h1>
            <p class="dash-page-subtitle">{{ __tr('Our team typically responds within a few hours.') }}</p>
        </div>
        <a href="{{ route('reseller.tickets.index') }}" class="action-btn secondary">
            <i class="fas fa-arrow-left"></i> {{ __tr('Back to Tickets') }}
        </a>
    </div>

    <div class="new-ticket-layout">

        {{-- Main Form --}}
        <div class="dashboard-card">

            @if ($errors->any())
                <div class="nt-error-bar">
                    <i class="fas fa-circle-exclamation"></i>
                    <ul class="mb-0 ps-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('reseller.tickets.store') }}" method="POST" class="nt-form">
                @csrf

                {{-- Subject --}}
                <div class="nt-field">
                    <label class="nt-label">
                        <i class="fas fa-pen-to-square nt-label-icon"></i>
                        {{ __tr('Subject') }} <span class="nt-req">*</span>
                    </label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                        class="nt-input" placeholder="{{ __tr('Brief description of your issue') }}">
                </div>

                {{-- Priority + Department --}}
                <div class="nt-row-two">
                    <div class="nt-field">
                        <label class="nt-label">
                            <i class="fas fa-flag nt-label-icon"></i>
                            {{ __tr('Priority') }} <span class="nt-req">*</span>
                        </label>
                        <div class="nt-priority-group">
                            @foreach ([
            'low' => ['label' => __tr('Low'), 'icon' => 'fa-circle-minus', 'cls' => 'nt-p-low'],
            'normal' => ['label' => __tr('Normal'), 'icon' => 'fa-circle', 'cls' => 'nt-p-normal'],
            'high' => ['label' => __tr('High'), 'icon' => 'fa-circle-up', 'cls' => 'nt-p-high'],
            'urgent' => ['label' => __tr('Urgent'), 'icon' => 'fa-circle-exclamation', 'cls' => 'nt-p-urgent'],
        ] as $val => $p)
                                <label
                                    class="nt-priority-chip {{ $p['cls'] }} {{ old('priority', 'normal') === $val ? 'is-active' : '' }}">
                                    <input type="radio" name="priority" value="{{ $val }}"
                                        {{ old('priority', 'normal') === $val ? 'checked' : '' }}>
                                    <i class="fas {{ $p['icon'] }}"></i> {{ $p['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="nt-field">
                        <label class="nt-label">
                            <i class="fas fa-building nt-label-icon"></i>
                            {{ __tr('Department') }}
                        </label>
                        <div class="nt-select-wrap">
                            <select name="department" class="nt-select">
                                <option value="general"
                                    {{ old('department', 'general') === 'general' ? 'selected' : '' }}>
                                    {{ __tr('General') }}</option>
                                <option value="billing" {{ old('department') === 'billing' ? 'selected' : '' }}>
                                    {{ __tr('Billing') }}</option>
                                <option value="technical" {{ old('department') === 'technical' ? 'selected' : '' }}>
                                    {{ __tr('Technical') }}</option>
                                <option value="sales" {{ old('department') === 'sales' ? 'selected' : '' }}>
                                    {{ __tr('Sales') }}</option>
                            </select>
                            <i class="fas fa-chevron-down nt-select-arrow"></i>
                        </div>
                    </div>
                </div>

                {{-- Message --}}
                <div class="nt-field">
                    <label class="nt-label">
                        <i class="fas fa-message nt-label-icon"></i>
                        {{ __tr('Message') }} <span class="nt-req">*</span>
                    </label>
                    <textarea name="message" rows="8" required minlength="10" class="nt-textarea"
                        placeholder="{{ __tr('Please describe your issue in detail. Include any error messages, steps to reproduce, or relevant account information.') }}">{{ old('message') }}</textarea>
                    <p class="nt-hint"><i class="fas fa-circle-info"></i> {{ __tr('Minimum 10 characters.') }}</p>
                </div>

                {{-- Actions --}}
                <div class="nt-actions">
                    <button type="submit" class="nt-submit-btn">
                        <i class="fas fa-paper-plane"></i> {{ __tr('Submit Ticket') }}
                    </button>
                    <a href="{{ route('reseller.tickets.index') }}" class="nt-cancel-btn">
                        {{ __tr('Cancel') }}
                    </a>
                </div>

            </form>
        </div>

        {{-- Help Sidebar --}}
        <aside class="nt-sidebar">

            <div class="dashboard-card nt-help-card">
                <div class="nt-help-icon-wrap">
                    <i class="fas fa-headset"></i>
                </div>
                <h4 class="nt-help-title">{{ __tr('How can we help?') }}</h4>
                <p class="nt-help-desc">{{ __tr('Our support team is here to assist with any issues you encounter.') }}</p>
                <ul class="nt-help-list">
                    <li><i class="fas fa-check"></i> {{ __tr('Account & billing questions') }}</li>
                    <li><i class="fas fa-check"></i> {{ __tr('Technical issues & bugs') }}</li>
                    <li><i class="fas fa-check"></i> {{ __tr('Feature questions') }}</li>
                    <li><i class="fas fa-check"></i> {{ __tr('API & integration help') }}</li>
                </ul>
                <div class="nt-response-badge">
                    <i class="fas fa-clock"></i> {{ __tr('Avg. response: a few hours') }}
                </div>
            </div>

            <div class="dashboard-card nt-tips-card">
                <h4 class="nt-tips-title">
                    <i class="fas fa-lightbulb"></i> {{ __tr('Tips for faster support') }}
                </h4>
                <ul class="nt-tips-list">
                    <li>{{ __tr('Include exact error messages') }}</li>
                    <li>{{ __tr('Describe steps to reproduce the issue') }}</li>
                    <li>{{ __tr('Mention which feature is affected') }}</li>
                    <li>{{ __tr('Select the correct priority level') }}</li>
                </ul>
            </div>

        </aside>

    </div>

@endsection
@section('js')
    <script>
        document.querySelectorAll('.nt-priority-chip input[type="radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.nt-priority-chip').forEach(function(chip) {
                    chip.classList.remove('is-active');
                });
                this.closest('.nt-priority-chip').classList.add('is-active');
            });
        });
    </script>
@endsection
