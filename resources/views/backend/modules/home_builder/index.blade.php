@extends('backend.layouts.dashboard_layout')

@section('page-title')
    {{ __tr('Home Page Builder') }}
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.css') }}">
    <style>
        .hb-section-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 12px;
            transition: box-shadow 0.2s;
        }

        .hb-section-card.ui-sortable-helper {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .hb-section-card.section-disabled {
            opacity: 0.55;
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
            transition: 0.25s;
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
            transition: 0.25s;
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

        .hb-lang-tabs .nav-link {
            font-size: 13px;
            padding: 6px 14px;
        }

        .hb-no-fields {
            color: #6c757d;
            font-size: 13px;
            font-style: italic;
        }

        #hb-sortable-list {
            min-height: 40px;
        }

        .order-saving-indicator {
            display: none;
            font-size: 13px;
            color: #28a745;
        }
    </style>
@endsection

@section('page-content')
    <x-admin-page-header title="" :links="$links" />

    <section class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="lang-switcher-wrap">
                    <div class="lang-switcher-label">
                        <i class="fas fa-globe-americas"></i>
                        <span>{{ __tr('Language') }}</span>
                    </div>
                    <div class="lang-switcher-tabs">
                        @foreach (activeLanguages() as $language)
                            <a href="{{ route('admin.home.builder', ['lang' => $language->code]) }}"
                                class="lang-switcher-btn {{ $language->code == $lang ? 'active' : '' }}">
                                <span class="lang-dot"></span>
                                {{ $language->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <span class="order-saving-indicator" id="order-saving-indicator">
                    <i class="fas fa-check-circle"></i> {{ __tr('Order saved') }}
                </span>
            </div>

            {{-- Section Builder --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __tr('Home Page Sections') }}</h5>
                    <small class="text-muted">
                        <i class="fas fa-arrows-alt"></i> {{ __tr('Drag to reorder') }}
                    </small>
                </div>
                <div class="card-body">
                    <ul id="hb-sortable-list" class="list-unstyled mb-0">
                        @foreach ($sections as $section)
                            <li class="hb-section-card {{ !$section->is_active ? 'section-disabled' : '' }}"
                                data-id="{{ $section->id }}">

                                {{-- Card Header --}}
                                <div class="hb-card-header">
                                    <i class="fas fa-grip-vertical hb-drag-handle"></i>

                                    <span class="hb-section-title">{{ $section->title }}</span>

                                    {{-- Active Toggle --}}
                                    <label class="hb-toggle-label">
                                        <span class="hb-toggle-switch">
                                            <input type="checkbox" class="section-toggle"
                                                data-section-id="{{ $section->id }}"
                                                {{ $section->is_active ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </span>
                                        <span class="toggle-status-text">
                                            {{ $section->is_active ? __tr('Visible') : __tr('Hidden') }}
                                        </span>
                                    </label>

                                    {{-- Expand Button (only sections with content fields) --}}
                                    @if ($section->key !== 'ad_slot')
                                        <button type="button" class="hb-expand-btn" onclick="toggleSectionBody(this)">
                                            <i class="fas fa-chevron-down"></i> {{ __tr('Edit Content') }}
                                        </button>
                                    @endif
                                </div>

                                {{-- Card Body: Content Fields --}}
                                @if ($section->key !== 'ad_slot')
                                    <div class="hb-card-body">
                                        <form method="POST" action="{{ route('admin.home.builder.content') }}">
                                            @csrf
                                            <input type="hidden" name="lang" value="{{ $lang }}">
                                            <input type="hidden" name="section_key" value="{{ $section->key }}">

                                            @switch($section->key)
                                                @case('banner')
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Badge Text') }}</label>
                                                        <input type="text" class="form-control" name="home_banner_badge"
                                                            placeholder="{{ __tr('e.g. #1 Classified Platform') }}"
                                                            value="{{ p_trans('home_banner_badge', $lang, get_setting('banner_badge_text')) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Main Title') }}</label>
                                                        <input type="text" class="form-control" name="home_banner_title"
                                                            placeholder="{{ __tr('Enter banner title') }}"
                                                            value="{{ p_trans('home_banner_title', $lang, get_setting('banner_title')) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            class="font-weight-bold">{{ __tr('Subtitle / Description') }}</label>
                                                        <textarea class="form-control" name="home_banner_subtitle" rows="2"
                                                            placeholder="{{ __tr('Enter banner description') }}">{{ p_trans('home_banner_subtitle', $lang, get_setting('banner_description')) }}</textarea>
                                                    </div>
                                                @break

                                                @case('categories')
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Section Title') }}</label>
                                                        <input type="text" class="form-control" name="home_categories_title"
                                                            placeholder="{{ __tr('e.g. Categories') }}"
                                                            value="{{ p_trans('home_categories_title', $lang, 'Categories') }}">
                                                    </div>
                                                @break

                                                @case('top_listings')
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Section Title') }}</label>
                                                        <input type="text" class="form-control" name="home_top_listings_title"
                                                            placeholder="{{ __tr('e.g. Top Listings') }}"
                                                            value="{{ p_trans('home_top_listings_title', $lang, 'Top Listings') }}">
                                                    </div>
                                                @break

                                                @case('promo')
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label class="font-weight-bold">{{ __tr('Badge') }}</label>
                                                            <input type="text" class="form-control" name="home_promo_badge"
                                                                placeholder="{{ __tr('e.g. Your Local Marketplace') }}"
                                                                value="{{ p_trans('home_promo_badge', $lang, 'Your Local Marketplace') }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="font-weight-bold">{{ __tr('Heading') }}</label>
                                                            <input type="text" class="form-control" name="home_promo_heading"
                                                                placeholder="{{ __tr('Enter promo heading') }}"
                                                                value="{{ p_trans('home_promo_heading', $lang, 'Earn cash by selling or find anything you desire') }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Paragraph Text') }}</label>
                                                        <textarea class="form-control" name="home_promo_text" rows="2"
                                                            placeholder="{{ __tr('Enter promo description') }}">{{ p_trans('home_promo_text', $lang, 'List your pre-loved or new items in minutes, or browse thousands of ads to find exactly what you need — all in one place.') }}</textarea>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label
                                                                class="font-weight-bold">{{ __tr('Primary Button Text') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="home_promo_btn_primary"
                                                                placeholder="{{ __tr('e.g. Post Your Ad') }}"
                                                                value="{{ p_trans('home_promo_btn_primary', $lang, 'Post Your Ad') }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label
                                                                class="font-weight-bold">{{ __tr('Secondary Button Text') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="home_promo_btn_secondary"
                                                                placeholder="{{ __tr('e.g. Browse Ads') }}"
                                                                value="{{ p_trans('home_promo_btn_secondary', $lang, 'Browse Ads') }}">
                                                        </div>
                                                    </div>
                                                @break

                                                @case('pricing_plans')
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Section Title') }}</label>
                                                        <input type="text" class="form-control" name="home_pricing_title"
                                                            placeholder="{{ __tr('e.g. Membership') }}"
                                                            value="{{ p_trans('home_pricing_title', $lang, 'Membership') }}">
                                                    </div>
                                                @break

                                                @case('featured_ads')
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Section Title') }}</label>
                                                        <input type="text" class="form-control" name="home_featured_ads_title"
                                                            placeholder="{{ __tr('e.g. Featured Ads') }}"
                                                            value="{{ p_trans('home_featured_ads_title', $lang, 'Featured Ads') }}">
                                                    </div>
                                                @break

                                                @case('recent_listings')
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __tr('Section Title') }}</label>
                                                        <input type="text" class="form-control" name="home_recent_title"
                                                            placeholder="{{ __tr('e.g. Recent Listings') }}"
                                                            value="{{ p_trans('home_recent_title', $lang, 'Recent Listing') }}">
                                                    </div>
                                                @break
                                            @endswitch

                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save"></i> {{ __tr('Save') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

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

            // ── Drag-and-drop ordering ──────────────────────────────────────
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

            // ── Toggle active state ─────────────────────────────────────────
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
                        // Revert checkbox on error
                        $checkbox.prop("checked", !$checkbox.prop("checked"));
                        toastr.error("{{ __tr('Failed to update section') }}");
                    }
                });
            });

        })(jQuery);

        // ── Expand/collapse content editor ──────────────────────────────────
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

        // Auto-open section if it had a form error/success redirect
        @if (session('_old_input.section_key'))
            var sectionKey = "{{ session('_old_input.section_key') }}";
            $('input[name="section_key"][value="' + sectionKey + '"]').closest('.hb-section-card').find('.hb-expand-btn')
                .click();
        @endif
    </script>
@endsection
