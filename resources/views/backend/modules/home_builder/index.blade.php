@extends('backend.layouts.dashboard_layout')

@section('page-title')
    {{ __tr('Home Page Builder') }}
@endsection

@section('page-style')
    <style>
        .hb-section-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 12px;
            transition: box-shadow .2s;
        }

        .hb-section-card.ui-sortable-helper {
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
        }

        .hb-section-card.section-disabled {
            opacity: .55;
        }

        .hb-card-header {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            cursor: default;
            gap: 12px;
        }

        .hb-drag-handle {
            cursor: grab;
            color: #adb5bd;
            font-size: 18px;
            flex-shrink: 0;
        }

        .hb-drag-handle:active {
            cursor: grabbing;
        }

        .hb-section-title {
            flex: 1;
            font-weight: 600;
            font-size: 14px;
            color: #343a40;
            margin: 0;
        }

        .hb-toggle-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6c757d;
            margin: 0;
            cursor: pointer;
            user-select: none;
        }

        .hb-toggle-switch {
            position: relative;
            width: 36px;
            height: 20px;
            flex-shrink: 0;
        }

        .hb-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .hb-toggle-switch .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #ced4da;
            border-radius: 20px;
            transition: .25s;
        }

        .hb-toggle-switch .slider:before {
            content: "";
            position: absolute;
            width: 14px;
            height: 14px;
            left: 3px;
            bottom: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .25s;
        }

        .hb-toggle-switch input:checked+.slider {
            background: #28a745;
        }

        .hb-toggle-switch input:checked+.slider:before {
            transform: translateX(16px);
        }

        .hb-expand-btn {
            background: none;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 12px;
            color: #495057;
            cursor: pointer;
            flex-shrink: 0;
        }

        .hb-expand-btn:hover {
            background: #f8f9fa;
        }

        .hb-card-body {
            border-top: 1px solid #f0f0f0;
            padding: 16px;
            display: none;
        }

        .hb-card-body.open {
            display: block;
        }

        .hb-no-fields {
            color: #6c757d;
            font-size: 13px;
            font-style: italic;
        }

        #hb-sortable-list {
            min-height: 40px;
        }

        .hb-sort-placeholder {
            height: 58px;
            background: #e9f5ff;
            border: 2px dashed #90caf9;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .order-saving-indicator {
            display: none;
            font-size: 13px;
            color: #28a745;
        }

        /* Banner section groupings */
        .hb-banner-section {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 12px;
            background: #fafbfc;
        }

        .hb-banner-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #495057;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hb-banner-label i {
            color: #6c757d;
        }

        .hb-preview-hint {
            font-size: 11px;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            color: #adb5bd;
            margin-left: 4px;
        }
    </style>
@endsection

@section('page-content')
    <x-admin-page-header title="" :links="$links" />

    <section class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="lang-switcher-wrap">
                    <div class="lang-switcher-label"><i class="fas fa-globe-americas"></i> <span>{{ __tr('Language') }}</span>
                    </div>
                    <div class="lang-switcher-tabs">
                        @foreach (activeLanguages() as $language)
                            <a href="{{ route('admin.home.builder', ['lang' => $language->code]) }}"
                                class="lang-switcher-btn {{ $language->code == $lang ? 'active' : '' }}">
                                <span class="lang-dot"></span> {{ $language->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <span class="order-saving-indicator" id="order-saving-indicator">
                    <i class="fas fa-check-circle"></i> {{ __tr('Order saved') }}
                </span>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __tr('Home Page Sections') }}</h5>
                    <small class="text-muted"><i class="fas fa-arrows-alt"></i> {{ __tr('Drag to reorder') }}</small>
                </div>
                <div class="card-body">
                    <ul id="hb-sortable-list" class="list-unstyled mb-0">
                        @foreach ($sections as $section)
                            <li class="hb-section-card {{ !$section->is_active ? 'section-disabled' : '' }}"
                                data-id="{{ $section->id }}">

                                <div class="hb-card-header">
                                    <i class="fas fa-grip-vertical hb-drag-handle"></i>
                                    <span class="hb-section-title">{{ $section->title }}</span>

                                    <label class="hb-toggle-label">
                                        <span class="hb-toggle-switch">
                                            <input type="checkbox" class="section-toggle"
                                                data-section-id="{{ $section->id }}"
                                                {{ $section->is_active ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </span>
                                        <span
                                            class="toggle-status-text">{{ $section->is_active ? __tr('Visible') : __tr('Hidden') }}</span>
                                    </label>

                                    <button type="button" class="hb-expand-btn" onclick="toggleSectionBody(this)">
                                        <i class="fas fa-chevron-down"></i> {{ __tr('Edit Content') }}
                                    </button>
                                </div>

                                <div class="hb-card-body">
                                    <form method="POST" action="{{ route('admin.home.builder.content') }}">
                                        @csrf
                                        <input type="hidden" name="lang" value="{{ $lang }}">
                                        <input type="hidden" name="section_key" value="{{ $section->key }}">

                                        @switch($section->key)
                                            @case('hero')
                                                {{-- ① Background Image --}}
                                                <div class="hb-banner-section">
                                                    <div class="hb-banner-label">
                                                        <i class="fas fa-image"></i> {{ __tr('Background Image') }}
                                                    </div>
                                                    <div class="form-group mb-1">
                                                        <x-media name="home_hero_bg_image" :value="p_trans('home_hero_bg_image', $lang, '')"></x-media>
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ __tr('Recommended: 1920×1080 landscape. Shown at 55% opacity with a dark overlay.') }}</small>
                                                </div>

                                                {{-- ② Heading --}}
                                                <div class="hb-banner-section">
                                                    <div class="hb-banner-label">
                                                        <i class="fas fa-heading"></i> {{ __tr('Main Heading') }}
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="text" class="form-control" name="home_hero_heading"
                                                            value="{{ p_trans('home_hero_heading', $lang, '') }}"
                                                            placeholder="Unlimited channels, movies, series, and more.">
                                                    </div>
                                                </div>

                                                {{-- ③ Tagline: "Starts at $11.99. Cancel anytime." --}}
                                                <div class="hb-banner-section">
                                                    <div class="hb-banner-label">
                                                        <i class="fas fa-tag"></i> {{ __tr('Price Tagline') }}
                                                        <span
                                                            class="hb-preview-hint">{{ __tr('"Starts at" · Price · Cancel text — shown as one line') }}</span>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>{{ __tr('"Starts at" Label') }}</label>
                                                            <input type="text" class="form-control" name="home_hero_from_label"
                                                                value="{{ p_trans('home_hero_from_label', $lang, 'Starts at') }}">
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>{{ __tr('Price') }}</label>
                                                            <input type="text" class="form-control" name="home_hero_from_price"
                                                                value="{{ p_trans('home_hero_from_price', $lang, '$11.99') }}"
                                                                placeholder="$11.99">
                                                        </div>
                                                        <div class="form-group col-md-5">
                                                            <label>{{ __tr('Cancel Text') }}</label>
                                                            <input type="text" class="form-control" name="home_hero_cancel"
                                                                value="{{ p_trans('home_hero_cancel', $lang, 'Cancel anytime.') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ④ Email CTA Form --}}
                                                <div class="hb-banner-section">
                                                    <div class="hb-banner-label">
                                                        <i class="fas fa-envelope"></i> {{ __tr('Email CTA Form') }}
                                                        <span
                                                            class="hb-preview-hint">{{ __tr('Hint text above · email input placeholder · button label') }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __tr('Hint Text') }}</label>
                                                        <input type="text" class="form-control" name="home_hero_cta_hint"
                                                            value="{{ p_trans('home_hero_cta_hint', $lang, 'Ready to watch? Enter your email to get started.') }}">
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-8">
                                                            <label>{{ __tr('Email Input Placeholder') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="home_hero_email_placeholder"
                                                                value="{{ p_trans('home_hero_email_placeholder', $lang, 'Email address') }}"
                                                                placeholder="Email address">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>{{ __tr('Button Label') }}</label>
                                                            <input type="text" class="form-control" name="home_hero_btn1"
                                                                value="{{ p_trans('home_hero_btn1', $lang, 'Get Started') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ⑤ Stats Bar --}}
                                                <div class="hb-banner-section mb-0">
                                                    <div class="hb-banner-label">
                                                        <i class="fas fa-chart-bar"></i> {{ __tr('Stats Bar') }}
                                                        <span
                                                            class="hb-preview-hint">{{ __tr('3 numbers shown at the bottom of the banner') }}</span>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-2">
                                                            <label>{{ __tr('Number') }}</label>
                                                            <input type="text" class="form-control" name="home_stat1_num"
                                                                value="{{ p_trans('home_stat1_num', $lang, '40K+') }}">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>{{ __tr('Label') }}</label>
                                                            <input type="text" class="form-control" name="home_stat1_label"
                                                                value="{{ p_trans('home_stat1_label', $lang, 'Live Channels') }}">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label>{{ __tr('Number') }}</label>
                                                            <input type="text" class="form-control" name="home_stat2_num"
                                                                value="{{ p_trans('home_stat2_num', $lang, '150K+') }}">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>{{ __tr('Label') }}</label>
                                                            <input type="text" class="form-control" name="home_stat2_label"
                                                                value="{{ p_trans('home_stat2_label', $lang, 'Movies / Series') }}">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label>{{ __tr('Number') }}</label>
                                                            <input type="text" class="form-control" name="home_stat3_num"
                                                                value="{{ p_trans('home_stat3_num', $lang, '10K+') }}">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>{{ __tr('Label') }}</label>
                                                            <input type="text" class="form-control" name="home_stat3_label"
                                                                value="{{ p_trans('home_stat3_label', $lang, 'Customers Happy') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @break

                                            @case('about')
                                                <div class="form-group">
                                                    <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                    <input type="text" class="form-control" name="home_about_heading"
                                                        value="{{ p_trans('home_about_heading', $lang, '') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">{{ __tr('Description') }}</label>
                                                    <textarea class="form-control" name="home_about_desc" rows="3">{{ p_trans('home_about_desc', $lang, '') }}</textarea>
                                                </div>
                                                <hr><small
                                                    class="text-muted font-weight-bold">{{ __tr('Stats (4 boxes)') }}</small>
                                                <div class="form-row mt-2">
                                                    @foreach ([1, 2, 3, 4] as $i)
                                                        <div class="form-group col-md-3">
                                                            <label>Stat {{ $i }} Value</label>
                                                            <input type="text" class="form-control"
                                                                name="home_about_stat{{ $i }}_val"
                                                                value="{{ p_trans('home_about_stat' . $i . '_val', $lang, '') }}">
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>Stat {{ $i }} Label</label>
                                                            <input type="text" class="form-control"
                                                                name="home_about_stat{{ $i }}_label"
                                                                value="{{ p_trans('home_about_stat' . $i . '_label', $lang, '') }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @break

                                            @case('categories')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Section Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_cat_heading"
                                                            value="{{ p_trans('home_cat_heading', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Subtitle') }}</label>
                                                        <input type="text" class="form-control" name="home_cat_desc"
                                                            value="{{ p_trans('home_cat_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                                <hr><small
                                                    class="text-muted font-weight-bold">{{ __tr('4 Category Cards') }}</small>
                                                @foreach ([1, 2, 3, 4] as $i)
                                                    <div class="form-row mt-2">
                                                        <div class="form-group col-md-1"><label>Icon
                                                                {{ $i }}</label><input type="text"
                                                                class="form-control" name="home_cat{{ $i }}_icon"
                                                                value="{{ p_trans('home_cat' . $i . '_icon', $lang, '') }}"></div>
                                                        <div class="form-group col-md-3"><label>Title
                                                                {{ $i }}</label><input type="text"
                                                                class="form-control" name="home_cat{{ $i }}_title"
                                                                value="{{ p_trans('home_cat' . $i . '_title', $lang, '') }}">
                                                        </div>
                                                        <div class="form-group col-md-8"><label>Desc
                                                                {{ $i }}</label><input type="text"
                                                                class="form-control" name="home_cat{{ $i }}_desc"
                                                                value="{{ p_trans('home_cat' . $i . '_desc', $lang, '') }}"></div>
                                                    </div>
                                                @endforeach
                                            @break

                                            @case('features')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_feat_heading"
                                                            value="{{ p_trans('home_feat_heading', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Subtitle') }}</label>
                                                        <input type="text" class="form-control" name="home_feat_desc"
                                                            value="{{ p_trans('home_feat_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                                <hr><small
                                                    class="text-muted font-weight-bold">{{ __tr('6 Feature Cards') }}</small>
                                                @foreach ([1, 2, 3, 4, 5, 6] as $i)
                                                    <div class="form-row mt-1">
                                                        <div class="form-group col-md-1"><label>Icon
                                                                {{ $i }}</label><input type="text"
                                                                class="form-control" name="home_feat{{ $i }}_icon"
                                                                value="{{ p_trans('home_feat' . $i . '_icon', $lang, '') }}">
                                                        </div>
                                                        <div class="form-group col-md-3"><label>Title
                                                                {{ $i }}</label><input type="text"
                                                                class="form-control" name="home_feat{{ $i }}_title"
                                                                value="{{ p_trans('home_feat' . $i . '_title', $lang, '') }}">
                                                        </div>
                                                        <div class="form-group col-md-8"><label>Desc
                                                                {{ $i }}</label><input type="text"
                                                                class="form-control" name="home_feat{{ $i }}_desc"
                                                                value="{{ p_trans('home_feat' . $i . '_desc', $lang, '') }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @break

                                            @case('pricing')
                                                <div class="alert alert-info mb-2">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ __tr('Plans are pulled from the database. Go to') }}
                                                    <a
                                                        href="{{ route('admin.pricing.plans.list') }}">{{ __tr('Pricing Plans') }}</a>
                                                    {{ __tr('to add or edit plans.') }}
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Section Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_pricing_heading"
                                                            value="{{ p_trans('home_pricing_heading', $lang, 'Plans for every household') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Subtitle') }}</label>
                                                        <input type="text" class="form-control" name="home_pricing_desc"
                                                            value="{{ p_trans('home_pricing_desc', $lang, 'Choose the package that matches your needs.') }}">
                                                    </div>
                                                </div>
                                            @break

                                            @case('reviews')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_reviews_heading"
                                                            value="{{ p_trans('home_reviews_heading', $lang, '') }}">
                                                    </div>
                                                </div>
                                                @foreach ([1, 2, 3] as $i)
                                                    <hr><small class="text-muted font-weight-bold">Review
                                                        {{ $i }}</small>
                                                    <div class="form-group mt-1">
                                                        <label>Text</label>
                                                        <textarea class="form-control" name="home_review{{ $i }}_text" rows="2">{{ p_trans('home_review' . $i . '_text', $lang, '') }}</textarea>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6"><label>Name</label><input type="text"
                                                                class="form-control" name="home_review{{ $i }}_name"
                                                                value="{{ p_trans('home_review' . $i . '_name', $lang, '') }}">
                                                        </div>
                                                        <div class="form-group col-md-6"><label>Location</label><input
                                                                type="text" class="form-control"
                                                                name="home_review{{ $i }}_loc"
                                                                value="{{ p_trans('home_review' . $i . '_loc', $lang, '') }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @break

                                            @case('setup')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_setup_heading"
                                                            value="{{ p_trans('home_setup_heading', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Subtitle') }}</label>
                                                        <input type="text" class="form-control" name="home_setup_desc"
                                                            value="{{ p_trans('home_setup_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                                @foreach ([1, 2, 3] as $i)
                                                    <div class="form-row mt-1">
                                                        <div class="form-group col-md-4"><label>Step {{ $i }}
                                                                Title</label><input type="text" class="form-control"
                                                                name="home_step{{ $i }}_title"
                                                                value="{{ p_trans('home_step' . $i . '_title', $lang, '') }}">
                                                        </div>
                                                        <div class="form-group col-md-8"><label>Step {{ $i }}
                                                                Description</label><input type="text" class="form-control"
                                                                name="home_step{{ $i }}_desc"
                                                                value="{{ p_trans('home_step' . $i . '_desc', $lang, '') }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @break

                                            @case('faq')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_faq_heading"
                                                            value="{{ p_trans('home_faq_heading', $lang, '') }}">
                                                    </div>
                                                </div>
                                                @foreach ([1, 2, 3, 4, 5] as $i)
                                                    <hr><small class="text-muted font-weight-bold">FAQ
                                                        {{ $i }}</small>
                                                    <div class="form-group mt-1">
                                                        <label>Question</label>
                                                        <input type="text" class="form-control"
                                                            name="home_faq{{ $i }}_q"
                                                            value="{{ p_trans('home_faq' . $i . '_q', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Answer</label>
                                                        <textarea class="form-control" name="home_faq{{ $i }}_a" rows="2">{{ p_trans('home_faq' . $i . '_a', $lang, '') }}</textarea>
                                                    </div>
                                                @endforeach
                                            @break

                                            @case('reseller')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_reseller_heading"
                                                            value="{{ p_trans('home_reseller_heading', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Description') }}</label>
                                                        <textarea class="form-control" name="home_reseller_desc" rows="2">{{ p_trans('home_reseller_desc', $lang, '') }}</textarea>
                                                    </div>
                                                </div>
                                            @break

                                            @case('cta')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_cta_heading"
                                                            value="{{ p_trans('home_cta_heading', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Description') }}</label>
                                                        <input type="text" class="form-control" name="home_cta_desc"
                                                            value="{{ p_trans('home_cta_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-4"><label>Button 1</label><input type="text"
                                                            class="form-control" name="home_cta_btn1"
                                                            value="{{ p_trans('home_cta_btn1', $lang, 'View Plans') }}"></div>
                                                    <div class="form-group col-md-4"><label>Button 2</label><input type="text"
                                                            class="form-control" name="home_cta_btn2"
                                                            value="{{ p_trans('home_cta_btn2', $lang, 'Contact Support') }}">
                                                    </div>
                                                </div>
                                            @break

                                            @case('newsletter')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_newsletter_heading"
                                                            value="{{ p_trans('home_newsletter_heading', $lang, '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Description') }}</label>
                                                        <input type="text" class="form-control" name="home_newsletter_desc"
                                                            value="{{ p_trans('home_newsletter_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                            @break

                                            @case('movies')
                                                <div class="alert alert-info mb-2">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ __tr('Movies are pulled from Featured Content (type = Movie). Go to') }}
                                                    <a
                                                        href="{{ route('admin.featured-content.index') }}">{{ __tr('Featured Content') }}</a>
                                                    {{ __tr('to add or edit movies.') }}
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Section Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_movies_heading"
                                                            value="{{ p_trans('home_movies_heading', $lang, 'Featured titles & live events') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Subtitle') }}</label>
                                                        <input type="text" class="form-control" name="home_movies_desc"
                                                            value="{{ p_trans('home_movies_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                            @break

                                            @case('series')
                                                <div class="alert alert-info mb-2">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ __tr('Series are pulled from Featured Content (type = Series). Go to') }}
                                                    <a
                                                        href="{{ route('admin.featured-content.index') }}">{{ __tr('Featured Content') }}</a>
                                                    {{ __tr('to add or edit series.') }}
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Section Heading') }}</label>
                                                        <input type="text" class="form-control" name="home_series_heading"
                                                            value="{{ p_trans('home_series_heading', $lang, 'Binge-Worthy Series') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Subtitle') }}</label>
                                                        <input type="text" class="form-control" name="home_series_desc"
                                                            value="{{ p_trans('home_series_desc', $lang, '') }}">
                                                    </div>
                                                </div>
                                            @break

                                            @case('sport_events')
                                                <div class="alert alert-info mb-2">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ __tr('Sport events are pulled from Featured Content (type = Sports Event). Go to') }}
                                                    <a
                                                        href="{{ route('admin.featured-content.index') }}">{{ __tr('Featured Content') }}</a>
                                                    {{ __tr('to add or edit sport events.') }}
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="font-weight-bold">{{ __tr('Section Heading') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="home_sport_events_heading"
                                                            value="{{ p_trans('home_sport_events_heading', $lang, 'Upcoming Live Events') }}">
                                                    </div>
                                                </div>
                                            @break

                                            @default
                                                <p class="hb-no-fields">{{ __tr('No editable content for this section.') }}</p>
                                            @break
                                        @endswitch

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-save"></i> {{ __tr('Save') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            $("#hb-sortable-list").sortable({
                handle: ".hb-drag-handle",
                axis: "y",
                placeholder: "hb-sort-placeholder",
                tolerance: "pointer",
                update: function() {
                    var sections = [];
                    $("#hb-sortable-list li[data-id]").each(function(index) {
                        sections.push({
                            id: $(this).data("id"),
                            sort_order: (index + 1) * 10
                        });
                    });
                    $.ajax({
                        url: "{{ route('admin.home.builder.order') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            sections: sections
                        },
                        success: function(res) {
                            if (res.success) {
                                var $indicator = $("#order-saving-indicator");
                                $indicator.fadeIn();
                                setTimeout(function() {
                                    $indicator.fadeOut();
                                }, 2500);
                            }
                        },
                        error: function() {
                            toastr.error("{{ __tr('Failed to save order') }}");
                        }
                    });
                }
            });

            $(document).on("change", ".section-toggle", function() {
                var $checkbox = $(this);
                var sectionId = $checkbox.data("section-id");
                var $card = $checkbox.closest(".hb-section-card");
                var $statusText = $checkbox.closest(".hb-toggle-label").find(".toggle-status-text");

                $.ajax({
                    url: "{{ route('admin.home.builder.toggle') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        section_id: sectionId
                    },
                    success: function(res) {
                        if (res.success) {
                            if (res.is_active) {
                                $card.removeClass("section-disabled");
                                $statusText.text("{{ __tr('Visible') }}");
                                toastr.success(res.message);
                            } else {
                                $card.addClass("section-disabled");
                                $statusText.text("{{ __tr('Hidden') }}");
                                toastr.warning(res.message);
                            }
                        }
                    },
                    error: function() {
                        $checkbox.prop("checked", !$checkbox.prop("checked"));
                        toastr.error("{{ __tr('Failed to update section') }}");
                    }
                });
            });

        })(jQuery);

        function toggleSectionBody(btn) {
            var $body = $(btn).closest(".hb-section-card").find(".hb-card-body");
            var isOpen = $body.hasClass("open");
            $body.toggleClass("open", !isOpen);
            $(btn).html(
                isOpen ?
                '<i class="fas fa-chevron-down"></i> {{ __tr('Edit Content') }}' :
                '<i class="fas fa-chevron-up"></i> {{ __tr('Close') }}'
            );
        }

        initMediaManager();

        @if (session('_old_input.section_key'))
            var sectionKey = "{{ session('_old_input.section_key') }}";
            $('input[name="section_key"][value="' + sectionKey + '"]').closest('.hb-section-card').find('.hb-expand-btn')
                .click();
        @endif
    </script>
@endsection
