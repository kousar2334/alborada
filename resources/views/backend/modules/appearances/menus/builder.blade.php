@php
    $links = [
        [
            'title' => 'Appearances',
            'route' => null,
            'active' => false,
        ],
        [
            'title' => 'Menus',
            'route' => '',
            'active' => true,
        ],
    ];

    $action = request()->has('action') ? request()->get('action') : 'create';
    $menu_id = request()->has('item') ? request()->get('item') : null;
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Menus') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .input-area {
            max-height: 200px;
            overflow-y: scroll;
            margin-bottom: 10px;
        }

        .gap-10 {
            gap: 10px;
        }

        .menu-selector {
            max-width: 150px !important;
        }

        .disabled-area {
            pointer-events: none;
            opacity: 0.8;
        }

        .menu-item {
            list-style: none;
            max-width: 100%;
            overflow: hidden;
            cursor: pointer;
        }

        .menu-item-content-body {
            display: none;
        }


        .dd {
            position: relative;
            display: block;
            margin: 0;
            padding: 0;
            max-width: 600px;
            list-style: none;
            font-size: 13px;
            line-height: 20px;
        }

        .dd-list {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dd-list .dd-list {
            padding-left: 30px;
        }

        .dd-collapsed .dd-list {
            display: none;
        }

        .dd-item,
        .dd-empty,
        .dd-placeholder {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            min-height: 20px;
            font-size: 13px;
            line-height: 20px;
        }

        .dd-handle {
            display: block;
            height: 30px;
            margin: 5px 0;
            padding: 5px 10px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ccc;
            background: #fafafa;
            background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: linear-gradient(top, #fafafa 0%, #eee 100%);
            -webkit-border-radius: 3px;
            border-radius: 3px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .dd-handle:hover {
            color: #2ea8e5;
            background: #fff;
        }

        .dd-item>button {
            display: block;
            position: relative;
            cursor: pointer;
            float: left;
            width: 25px;
            height: 20px;
            margin: 5px 0;
            padding: 0;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 0;
            background: transparent;
            font-size: 12px;
            line-height: 1;
            text-align: center;
            font-weight: bold;
        }

        .dd-placeholder,
        .dd-empty {
            margin: 5px 0;
            padding: 0;
            min-height: 30px;
            background: #f2fbff;
            border: 1px dashed #b6bcbf;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .dd-empty {
            border: 1px dashed #bbb;
            min-height: 100px;
            background-color: #e5e5e5;
            background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }

        .dd-dragel {
            position: absolute;
            pointer-events: none;
            z-index: 9999;
        }

        .dd-dragel>.dd-item .dd-handle {
            margin-top: 0;
        }

        .dd-dragel .dd-handle {
            -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
            box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
        }

        /**
                                                                                                                                                                        * Nestable Extras
                                                                                                                                                                        */

        .nestable-lists {
            display: block;
            clear: both;
            padding: 30px 0;
            width: 100%;
            border: 0;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
        }

        #nestable-menu {
            padding: 0;
            margin: 20px 0;
        }

        @media only screen and (min-width: 700px) {

            .dd {
                float: left;
                width: 48%;
            }

            .dd+.dd {
                margin-left: 2%;
            }

        }

        .dd-hover>.dd-handle {
            background: #2ea8e5 !important;
        }

        /**
                                                                                                                                                                        * Nestable Draggable Handles
                                                                                                                                                                        */
        .dd-item-header {
            margin-left: 43px;
        }

        .dd3-content {
            display: block;
            height: auto;
            margin: 5px 0;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ccc;
            background: #fafafa;
            background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: linear-gradient(top, #fafafa 0%, #eee 100%);
            -webkit-border-radius: 3px;
            border-radius: 3px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .dd3-content:hover {
            color: #2ea8e5;
            background: #fff;
        }

        .dd-dragel>.dd3-item>.dd3-content {
            margin: 0;
        }

        .dd3-item>button {
            margin-left: 30px;
        }

        .dd3-handle {
            position: absolute;
            padding: 9px 12px;
            margin: 0;
            left: 0;
            top: 0;
            cursor: pointer;
            width: 40px;
            height: 40px;
            white-space: nowrap;
            overflow: hidden;
            border: 1px solid #aaa;
            background: #ddd;
            background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
            background: -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
            background: linear-gradient(top, #ddd 0%, #bbb 100%);
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .dd3-handle:hover {
            background: #ddd;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="Menus" :links="$links" />
    <section class="content min-vh-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-tabs">
                        <div class="card-header p-0 pt-1 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="edit-menus-tab" data-toggle="pill" href="#edit-menus"
                                        role="tab" aria-controls="edit-menus"
                                        aria-selected="true">{{ __tr('Edit Menus') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-three-tabContent">
                                <div class="tab-pane fade show active" id="edit-menus" role="tabpanel"
                                    aria-labelledby="edit-menus-tab">
                                    <div class="row m-0">
                                        <div class="col-12 mb-20">
                                            <form method="get"
                                                class="align-items-baseline d-flex gap-10 mb-2 flex-wrap mb-3">
                                                <p class="mb-0">{{ __tr('Select a menu to edit') }}</p>
                                                <input type="hidden" value="edit" name="action">
                                                <select class="form-control menu-selector" name="item">
                                                    <option value="">{{ __tr('Select menu') }}</option>
                                                    @foreach ($all_menus as $menu)
                                                        <option value="{{ $menu->id }}" @selected($selected_menu != null && $selected_menu->id == $menu->id)>
                                                            {{ $menu->title }}</option>
                                                    @endforeach
                                                </select>
                                                @php
                                                    $languages = activeLanguages();
                                                    $selected_language = request()->has('lang')
                                                        ? request()->get('lang')
                                                        : defaultLangCode();
                                                @endphp
                                                <select class="form-control menu-selector" name="lang">
                                                    <option value="">{{ __tr('Select Language') }}</option>
                                                    @foreach ($languages as $language)
                                                        <option value="{{ $language->code }}" @selected($selected_language != null && $selected_language == $language->code)>
                                                            {{ $language->native_title }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button class="btn btn-outline-primary btn-sm"
                                                    type="submit">{{ __tr('Select') }}</button>
                                                <p class="mb-0">{{ __tr('or') }} <a
                                                        href="{{ route('admin.appearance.menu.builder', ['action' => 'create']) }}">{{ __tr('Create a new menu') }}</a>
                                                </p>
                                            </form>
                                        </div>
                                        <!--Menu Item Options-->
                                        <div
                                            class="col-md-3 mb-4 menu-items-options-wraper {{ request()->has('action') && request()->get('action') == 'create' ? 'disabled-area' : '' }}">
                                            <div class="accordion {{ $selected_language != defaultLangCode() ? 'disabled-area' : '' }}"
                                                id="accordionExample">
                                                <!--Custom Link-->
                                                <div class="card mb-0">
                                                    <div id="CustomAccordianHeading"
                                                        class="align-items-center border-0 d-flex justify-content-between p-1 px-2">
                                                        <h5 class="mb-0 h6">
                                                            {{ __tr('Custom Link') }}
                                                        </h5>
                                                        <button type="button" data-toggle="collapse"
                                                            data-target="#customLinkAccordion" aria-expanded="false"
                                                            aria-controls="customLinkAccordion"
                                                            class="btn btn-link text-dark">+</button>
                                                    </div>
                                                    <div id="customLinkAccordion" aria-labelledby="CustomAccordianHeading"
                                                        data-parent="#accordionExample" class="collapse border-top">
                                                        <div class="card-body p-2">
                                                            <div class="input-area">
                                                                <div class="form-group">
                                                                    <label>{{ __tr('Text') }}</label>
                                                                    <input type="text" class="form-control"
                                                                        name="text" id="custom-item-text"
                                                                        placeholder="{{ __tr('Enter Text') }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>{{ __tr('URL') }}</label>
                                                                    <input type="text" class="form-control"
                                                                        id="custom-item-link" name="link"
                                                                        placeholder="{{ __tr('https://') }}">
                                                                </div>
                                                            </div>
                                                            <div class="action-area">
                                                                <button
                                                                    class="btn btn-outline-primary btn-sm item-add-btn-to-menu"
                                                                    data-item="custom">{{ __tr('Add to Menu') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--End Custom Link-->
                                                <!--Page Link Items-->
                                                <div class="card mb-0">
                                                    <div id="PageAccordianheading"
                                                        class="align-items-center border-0 d-flex justify-content-between p-1 px-2">
                                                        <h5 class="mb-0 h6">
                                                            {{ __tr('Pages') }}
                                                        </h5>
                                                        <button type="button" data-toggle="collapse"
                                                            data-target="#pageItemAccordion" aria-expanded="false"
                                                            aria-controls="pageItemAccordion"
                                                            class="btn btn-link text-dark">+</button>
                                                    </div>
                                                    <div id="pageItemAccordion" aria-labelledby="PageAccordianheading"
                                                        data-parent="#accordionExample" class="collapse border-top">
                                                        <div class="card-body p-2">
                                                            <div class="input-area">
                                                                <ul class="page-items p-0">
                                                                    @foreach ($pages as $page)
                                                                        <li>
                                                                            <label class="manu-item-input-label">
                                                                                <input type="checkbox" name="pages[]"
                                                                                    class="menu-item-checkbox page-input"
                                                                                    value="{{ $page->id }}">
                                                                                <span class="h6">
                                                                                    {{ $page->title }}
                                                                                </span>
                                                                            </label>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="action-area">
                                                                <button
                                                                    class="btn btn-outline-primary btn-sm item-add-btn-to-menu"
                                                                    data-item="page">{{ __tr('Add to Menu') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--End Page Link Items-->
                                                <!--Product category Items-->
                                                <div class="card mb-0">
                                                    <div id="categoryAccordianheading"
                                                        class="align-items-center border-0 d-flex justify-content-between p-1 px-2">
                                                        <h5 class="mb-0 h6">
                                                            {{ __tr('Categories') }}
                                                        </h5>
                                                        <button type="button" data-toggle="collapse"
                                                            data-target="#categoryItemAccordion" aria-expanded="false"
                                                            aria-controls="categoryItemAccordion"
                                                            class="btn btn-link text-dark">+</button>
                                                    </div>
                                                    <div id="categoryItemAccordion"
                                                        aria-labelledby="categoryAccordianheading"
                                                        data-parent="#accordionExample" class="collapse border-top">
                                                        <div class="card-body p-2">
                                                            <div class="input-area">
                                                                <ul class="page-items p-0">
                                                                    @foreach ($product_categories as $category)
                                                                        <li>
                                                                            <label class="manu-item-input-label">
                                                                                <input type="checkbox" name="categories[]"
                                                                                    class="menu-item-checkbox category-input"
                                                                                    value="{{ $category->id }}">
                                                                                <span class="h6">
                                                                                    {{ $category->title }}
                                                                                </span>
                                                                            </label>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="action-area">
                                                                <button
                                                                    class="btn btn-outline-primary btn-sm item-add-btn-to-menu"
                                                                    data-item="category">{{ __tr('Add to Menu') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--End --Product category Items-->
                                            </div>
                                        </div>
                                        <!--End Item Options-->
                                        <!--Menu Structure-->
                                        <div class="col-md-9">
                                            <form id="menu-builder-form" method="post"
                                                action="{{ route('admin.appearance.menu.builder.menu.management') }}">
                                                @csrf
                                                <textarea style="display: none;" name="menu-items" id="nestable-output"></textarea>
                                                <input type="hidden" name="menu_id" id="menuId"
                                                    value="{{ $menu_id }}">
                                                <input type="hidden" name="action" id="action"
                                                    value="{{ $action }}">
                                                <div class="card card-outline">
                                                    <div class="card-header">
                                                        <div class="form-group row mb-0">
                                                            <label
                                                                class="col-form-label font-weight-normal mr-3">{{ __tr('Menu Name') }}</label>
                                                            <div class="menu-name">
                                                                <input type="text" class="form-control"
                                                                    name="menu_name" placeholder="Menu Name"
                                                                    value="{{ $selected_menu != null ? $selected_menu->title : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body d-flex flex-column p-2">
                                                        @if (request()->has('action') && request()->get('action') == 'create')
                                                            <p>{{ __tr('Enter menu name and click on save menu button') }}
                                                            </p>
                                                        @endif
                                                        @if (request()->has('action') && request()->get('action') == 'edit')
                                                            @if ($selected_menu != null)

                                                                <div class="dd" id="nestable">
                                                                    @if ($selected_menu->items->count() > 0)
                                                                        <ol class="dd-list">
                                                                            @foreach ($selected_menu->items as $key => $item)
                                                                                <li class="dd-item dd3-item"
                                                                                    data-id="{{ $item->id }}">
                                                                                    <div class="dd-handle dd3-handle">
                                                                                        <i class="fas fa-arrows-alt"></i>
                                                                                    </div>
                                                                                    <div class="dd3-content">
                                                                                        <!--Menu Item Header-->
                                                                                        <div
                                                                                            class="align-items-center d-flex justify-content-between dd-item-header">
                                                                                            <div class="menu-item-title">
                                                                                                <h6 class="mb-0">
                                                                                                    {{ $item->translation('title', $selected_language) }}
                                                                                                </h6>
                                                                                            </div>
                                                                                            <div class="menu-item-actions">
                                                                                                <a href="#"
                                                                                                    class="btn menu-item-content-toggle-btn">
                                                                                                    <i
                                                                                                        class="fas fa-angle-down"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!--End Menu Item Header--->

                                                                                        <!--Menu Item Content-->
                                                                                        <div
                                                                                            class="border bg-white menu-item-content-body p-2">
                                                                                            <div class="menu-item-content">
                                                                                                <div class="form-group">
                                                                                                    <label>{{ __tr('Text') }}</label>
                                                                                                    <input type="text"
                                                                                                        id="text-{{ $item->id }}"
                                                                                                        class="form-control"
                                                                                                        name="text"
                                                                                                        value="{{ $item->translation('title', $selected_language) }}"
                                                                                                        placeholder="{{ __tr('Enter Text') }}">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label>{{ __tr('URL') }}</label>
                                                                                                    <input type="text"
                                                                                                        id="link-{{ $item->id }}"
                                                                                                        class="form-control"
                                                                                                        name="link"
                                                                                                        value="{{ $item->link() }}"
                                                                                                        {{ $item->linkable_type != null ? 'disabled' : '' }}
                                                                                                        placeholder="{{ __tr('https://') }}">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div
                                                                                                class="d-flex justify-content-between menu-item-update-actions">
                                                                                                <button
                                                                                                    class="btn btn-outline-danger btn-sm menu-item-remove-btn"
                                                                                                    data-id="{{ $item->id }}">
                                                                                                    {{ __tr('Remove Item') }}
                                                                                                </button>
                                                                                                <button
                                                                                                    class="btn btn-outline-primary btn-sm menu-item-update-btn"
                                                                                                    data-id="{{ $item->id }}">
                                                                                                    {{ __tr('Save Change') }}
                                                                                                </button>

                                                                                            </div>
                                                                                        </div>
                                                                                        <!--End Menu Item Content-->
                                                                                    </div>
                                                                                    <!--Child Items-->
                                                                                    @if ($item->children->count() > 0)
                                                                                        @include(
                                                                                            'backend.modules.appearances.menus.includes.sub_menu',
                                                                                            [
                                                                                                'children' =>
                                                                                                    $item->children,
                                                                                            ]
                                                                                        )
                                                                                    @endif
                                                                                    <!--End Child Items-->
                                                                                </li>
                                                                            @endforeach
                                                                        </ol>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endif

                                                        <!--Menu Position Settings-->
                                                        <div class="menu-settings border-top pt-2 mt-2">
                                                            <h5>{{ __tr('Menu Settings') }}</h5>
                                                            <div
                                                                class="d-flex display-location-settings flex-wrap gap-10 mt-4">
                                                                <h6>{{ __tr('Display Locations') }}</h6>
                                                                <div
                                                                    class="d-flex display-location-options flex-column ml-lg-5">
                                                                    <!--Header Menu-->
                                                                    <label class="display-location mb-20">
                                                                        <input type="checkbox"
                                                                            class="display-location-checkbox"
                                                                            name="header_menu"
                                                                            @checked($menu_id != null && $header_menu != null && $header_menu->menu_id == $menu_id)>
                                                                        <span class="h6">
                                                                            {{ __tr('Header Menu') }}
                                                                            <span>
                                                                                {{ $header_menu != null ? __('(Currently set to: ' . $header_menu->menu->title . ')') : 'No Menu set' }}
                                                                            </span>
                                                                        </span>
                                                                    </label>
                                                                    <!--End Header Menu-->
                                                                    <!--Footer Menu-->
                                                                    <label class="display-location mb-20">
                                                                        <input type="checkbox"
                                                                            class="display-location-checkbox"
                                                                            name="footer_menu"
                                                                            @checked($menu_id != null && $footer_menu != null && $footer_menu->menu_id == $menu_id)>
                                                                        <span class="h6">
                                                                            {{ __tr('Footer Menu') }}
                                                                            <span>
                                                                                {{ $footer_menu != null ? __('(Currently set to: ' . $footer_menu->menu->title . ')') : 'No Menu set' }}
                                                                            </span>
                                                                        </span>
                                                                    </label>
                                                                    <!--End Footer Menu-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--End Menu Position Settings-->

                                                    </div>

                                                    <!--Card Footer-->
                                                    @if (request()->has('action') && request()->get('action') == 'create')
                                                        <div class="bg-light d-flex justify-content-end p-2">
                                                            <button class="btn btn-primary">
                                                                {{ __tr('Save Menu') }}
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="bg-light d-flex justify-content-between p-2">
                                                            <button type="button"
                                                                class="btn btn-link text-danger delete-menu-btn">
                                                                {{ __tr('Delete Menu') }}
                                                            </button>
                                                            <button class="btn btn-primary">
                                                                {{ __tr('Update Menu') }}
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <!--End Card Foter--->
                                                </div>
                                            </form>
                                        </div>
                                        <!--End Menu Structure-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Modal-->
        <div class="modal fade" id="menu-delete-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete menu ?') }}</h4>
                        <form method="POST" action="{{ route('admin.appearance.menu.builder.delete.menu') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $menu_id }}">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End Delete Modal-->
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/web-assets/backend/plugins/jquery-nestable/jquery.nestable.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            /**
             * Delete menu
             * 
             **/
            $(".delete-menu-btn").on('click', function(e) {
                e.preventDefault();
                $("#menu-delete-modal").modal('show');
            });
            /**
             * Update menu item
             * 
             **/
            $(".menu-item-update-btn").on("click", function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let text = $("#text-" + id).val();
                let link = $("#link-" + id).val();
                let lang = "{{ $selected_language }}";
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        'id': id,
                        'text': text,
                        'link': link,
                        'lang': lang
                    },
                    url: '{{ route('admin.appearance.menu.builder.update.menu.item') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Menu item updated successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error('Menu item update failed', 'Error')
                        }
                    },
                    error: function(response) {
                        toastr.error('Menu item update failed', 'Error');
                    }
                });
            });
            /**
             * Remove menu item
             * 
             **/
            $(".menu-item-remove-btn").on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        'data': id
                    },
                    url: '{{ route('admin.appearance.menu.builder.remove.menu.item') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Menu item removed successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error('Menu item remove failed', 'Error')
                        }
                    },
                    error: function(response) {
                        toastr.error('Menu item remove failed', 'Error');
                    }
                });
            });
            /**
             * Add Item to menu
             * 
             **/
            $(".item-add-btn-to-menu").on('click', function(e) {
                let item = $(this).data('item');
                let menu_id = $("#menuId").val();
                let inputData = {};
                if (item == 'custom') {
                    let text = $("#custom-item-text").val();
                    let link = $("#custom-item-link").val();
                    inputData = {
                        'item': item,
                        'link': link,
                        'text': text,
                        'menu_id': menu_id
                    }

                }
                if (item == 'page') {
                    let pages = $(".page-input:checked").map(function() {
                        return $(this).val();
                    }).get();
                    inputData = {
                        'item': item,
                        'pages': pages,
                        'menu_id': menu_id
                    }
                }

                if (item == 'category') {
                    let categories = $(".category-input:checked").map(function() {
                        return $(this).val();
                    }).get();
                    inputData = {
                        'item': item,
                        'categories': categories,
                        'menu_id': menu_id
                    }
                }


                let jsonInputData = JSON.stringify(inputData);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        'data': jsonInputData
                    },
                    url: '{{ route('admin.appearance.menu.builder.add.menu.items') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Menu item added successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error(response.message, 'Error')
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>');
                            })
                        } else {
                            toastr.error('Menu item add failed', 'Error');
                        }
                    }
                });
            });
            /**
             * Toggle menu item content body
             * 
             **/
            $('.menu-item-content-toggle-btn').on('click', function(e) {
                e.preventDefault();
                let menuContentBody = $(this).closest('.dd-item-header').next('.menu-item-content-body');
                let icon = $(this).find('i');
                let currentIconClass = icon.attr('class');

                if (currentIconClass == 'fas fa-angle-down') {
                    icon.removeClass('fas fa-angle-down');
                    icon.addClass('fas fa-angle-up');
                    menuContentBody.slideDown();

                }

                if (currentIconClass == 'fas fa-angle-up') {
                    icon.removeClass('fas fa-angle-up');
                    icon.addClass('fas fa-angle-down');
                    menuContentBody.slideToggle();
                }
            });

            /**
             * Jquery nestable
             **/
            let updateOutput = function(e) {
                let action = $("#action").val();
                if (action === 'edit') {
                    let list = e.length ? e : $(e.target);
                    let output = list.data('output');
                    if (window.JSON) {
                        output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
                    } else {
                        output.val('JSON browser support required for this demo.');
                    }
                }

            };

            $('.dd').nestable({
                    group: 1,
                    items: 'li',
                    handle: 'div.inner',

                })
                .on('change', updateOutput);

            updateOutput($('.dd').data('output', $('#nestable-output')));

        })(jQuery);
    </script>
@endsection
