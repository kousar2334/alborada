@php
    $links = [
        [
            'title' => 'Colors Setup',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Colors Setup') }}
@endsection
@section('page-style')
    <style>
        .color-var-badge {
            font-family: monospace;
            font-size: 11px;
            background: #f4f4f4;
            color: #555;
            padding: 1px 6px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }

        .color-group-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #888;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eee;
        }

        .color-swatch-label {
            padding: 0;
            overflow: hidden;
            cursor: pointer;
        }

        .color-swatch-label input[type="color"] {
            width: 40px;
            height: 100%;
            min-height: 36px;
            border: none;
            padding: 3px;
            cursor: pointer;
            background: transparent;
            display: block;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Colors Setup') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-sm-3">
                                    @include('backend.modules.appearances.site-setting.setting_tabs')
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="tab-content">
                                        <div
                                            class="tab-pane text-left fade {{ Request::routeIs(['admin.appearance.site.setting.colors']) ? 'show active' : '' }}">
                                            <h4>{{ __tr('Colors Setup') }}</h4>
                                            <hr>
                                            <form method="POST"
                                                action="{{ route('admin.appearance.site.setting.colors.update') }}">
                                                @csrf

                                                {{-- Primary Colors --}}
                                                <p class="color-group-title">{{ __tr('Primary Colors') }}</p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Primary Color') }} <span
                                                                    class="color-var-badge">--primary-color</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_primary_color"
                                                                    value="{{ get_setting('site_primary_color', '#260ac4') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_primary_color', '#260ac4') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Primary Dark') }} <span
                                                                    class="color-var-badge">--primary-color-dark</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_primary_color_dark"
                                                                    value="{{ get_setting('site_primary_color_dark', '#e84e1b') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_primary_color_dark', '#e84e1b') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Primary Light') }} <span
                                                                    class="color-var-badge">--primary-color-light</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_primary_color_light"
                                                                    value="{{ get_setting('site_primary_color_light', '#ff9a5c') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_primary_color_light', '#ff9a5c') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Primary Lighter') }} <span
                                                                    class="color-var-badge">--primary-color-lighter</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_primary_color_lighter"
                                                                    value="{{ get_setting('site_primary_color_lighter', '#ffb347') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_primary_color_lighter', '#ffb347') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Primary Shadow') }} <span
                                                                    class="color-var-badge">--primary-color-shadow</span></label>
                                                            <input type="text" class="form-control"
                                                                name="site_primary_color_shadow"
                                                                value="{{ get_setting('site_primary_color_shadow', '#f766310f') }}"
                                                                placeholder="#rrggbbaa — 8-digit hex with alpha">
                                                            <small
                                                                class="text-muted">{{ __tr('Supports 8-digit hex with alpha (e.g. #f766310f)') }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Site Header Color') }} <span
                                                                    class="color-var-badge">--header-color</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_header_color"
                                                                    value="{{ get_setting('site_header_color', '#eef7ff') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_header_color', '#eef7ff') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Accent Colors --}}
                                                <p class="color-group-title mt-2">{{ __tr('Accent Colors') }}</p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Main Color Two') }} <span
                                                                    class="color-var-badge">--main-color-two</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_main_color_two"
                                                                    value="{{ get_setting('site_main_color_two', '#524eb7') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_main_color_two', '#524eb7') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Main Color Three') }} <span
                                                                    class="color-var-badge">--main-color-three</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_main_color_three"
                                                                    value="{{ get_setting('site_main_color_three', '#00cad5') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_main_color_three', '#00cad5') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Text Colors --}}
                                                <p class="color-group-title mt-2">{{ __tr('Text Colors') }}</p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Heading Color') }} <span
                                                                    class="color-var-badge">--heading-color</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_heading_color"
                                                                    value="{{ get_setting('site_heading_color', '#333333') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_heading_color', '#333333') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>{{ __tr('Base / Secondary Color') }} <span
                                                                    class="color-var-badge">--secondary-color</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control color-text"
                                                                    name="site_base_color"
                                                                    value="{{ get_setting('site_base_color', '#fba260') }}">
                                                                <div class="input-group-append">
                                                                    <label class="input-group-text color-swatch-label">
                                                                        <input type="color" class="color-swatch"
                                                                            value="{{ get_setting('site_base_color', '#fba260') }}">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end mt-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __tr('Save Changes') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

            // Native picker → update text input
            $(document).on('input', '.color-swatch', function() {
                $(this).closest('.input-group').find('.color-text').val($(this).val());
            });

            // Text input → update native picker (only valid 6-digit hex)
            $(document).on('input', '.color-text', function() {
                var val = $(this).val().trim();
                if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                    $(this).closest('.input-group').find('.color-swatch').val(val);
                }
            });
        })(jQuery);
    </script>
@endsection
