@extends('frontend.layouts.master')
@section('meta')
    <title>{{ __tr('Ads') }} - {{ get_setting('site_name') }}</title>
    <!-- page css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.css">
@endsection
@section('content')
    <div class="container">
        <div class="sidebar-overlay" id="sidebar_overlay"></div>

        <div class="listing-page-container">
            <!-- Left Content -->
            <div class="listing-filter-area">
                <button type="button" class="sidebar-close-btn d-lg-none" id="sidebar_close_btn">
                    <i class="las la-times"></i>
                </button>
                @if (request()->hasAny([
                        'q',
                        'cat',
                        'subcat',
                        'child_cat',
                        'country',
                        'state',
                        'city',
                        'min_price',
                        'max_price',
                        'condition',
                        'type',
                        'date_posted',
                        'sortby',
                    ]))
                    <div class="reset-btn cmn-filter-btn mb-3">
                        <a href="{{ route('ad.listing.page', $category_slug ?? '') }}" class="btn w-100"
                            style="background-color: var(--primary-color); color: #fff;">
                            <i class="las la-undo-alt"></i> {{ __tr('Reset Filters') }}
                        </a>
                    </div>
                @endif
                <!--Search any title filter start -->
                <div class="filter-item mb-4">
                    <div class="filter-item-content w-100">
                        <div class="single-category-service">
                            <div class="single-select position-relative">
                                <input type="text" class="search-input form-control pe-5" id="search_by_query"
                                    placeholder="{{ __tr('Search here you want') }}" value="{{ request('q') }}">
                                <button type="button" id="search_by_query_btn" class="btn position-absolute border-0">
                                    <i class="las la-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Search any title filter end -->
                <!-- Sort By Dropdown -->

                <form id="search_listings_form" method="GET"
                    action="{{ route('ad.listing.page', $category_slug ?? '') }}">
                    <!-- Hidden inputs for filter state -->
                    <input type="hidden" name="q" id="search_q_hidden" value="{{ request('q') }}">
                    <input type="hidden" name="min_price" id="min_price" value="{{ request('min_price') }}">
                    <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price') }}">
                    <input type="hidden" name="condition" id="condition" value="{{ request('condition') }}">
                    <input type="hidden" name="type" id="type" value="{{ request('type') }}">
                    <input type="hidden" name="date_posted" id="date_posted" value="{{ request('date_posted') }}">
                    <input type="hidden" name="sortby" id="sortby_hidden" value="{{ request('sortby') }}">

                    <div class="filter-items-wrapper">
                        <!-- All Categories -->
                        <div class="filter-item mb-4">
                            <div class="filter-item-content w-100">
                                <h5 class="cateTitle mb-2 postdateTitle">{{ __tr('Categories') }}</h5>
                                <input type="hidden" name="cat_id" id="selected_category_id"
                                    value="{{ request('cat_id') }}">

                                <!-- Category Section -->
                                <div id="category-section">
                                    <!-- Back button (hidden for root level) -->
                                    <div id="back-button-container" class="mb-2" style="display: none;">
                                        <a href="javascript:void(0)" id="back-button" class="text-primary small">
                                            <i class="las la-arrow-left"></i> <span
                                                id="back-button-text">{{ __tr('Back to Categories') }}</span>
                                        </a>
                                    </div>

                                    <!-- Selected Category Display (when has children) -->
                                    @if ($selectedCategory && $selectedCategoryId)
                                        <div id="selected-category-display" class="selected-cat-display">
                                            <strong class="selected-cat-name">
                                                <i class="las la-folder-open icon-mr-sm"></i>
                                                {{ $selectedCategory->translation('title') }}
                                            </strong>
                                        </div>
                                    @endif

                                    <!-- Category List -->
                                    <ul class="category-list" id="category-list">
                                        @foreach ($categories as $category)
                                            <li class="category-item {{ request('cat_id') == $category->id ? 'active' : '' }}"
                                                data-category-id="{{ $category->id }}"
                                                data-category-name="{{ $category->translation('title') }}"
                                                data-parent-id="{{ $category->parent_id ?? '' }}"
                                                data-parent-name="{{ $category->parent_title ?? '' }}">
                                                <a href="javascript:void(0)" class="category-link">
                                                    <i class="las la-angle-right"></i>
                                                    {{ $category->translation('title') }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Location Filter -->
                        <div class="filter-item mb-4">
                            <div class="filter-item-content w-100">
                                <h5 class="cateTitle mb-2 postdateTitle">{{ __tr('Location') }}</h5>
                                <input type="hidden" name="country" id="selected_country"
                                    value="{{ request('country') }}">
                                <input type="hidden" name="state" id="selected_state" value="{{ request('state') }}">
                                <input type="hidden" name="city" id="selected_city" value="{{ request('city') }}">

                                <!-- Country List -->
                                <div id="country-section">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">{{ __tr('Select Country') }}</span>
                                    </div>
                                    <ul class="location-list" id="country-list">
                                        <!-- Countries will be loaded here -->
                                    </ul>
                                </div>

                                <!-- State List -->
                                <div id="state-section" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <a href="javascript:void(0)" id="back-to-countries" class="text-primary small">
                                            <i class="las la-arrow-left"></i> {{ __tr('Back to Countries') }}
                                        </a>
                                        <span class="text-muted small" id="selected-country-name"></span>
                                    </div>
                                    <ul class="location-list" id="state-list">
                                        <!-- States will be loaded here -->
                                    </ul>
                                </div>

                                <!-- City List -->
                                <div id="city-section" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <a href="javascript:void(0)" id="back-to-states" class="text-primary small">
                                            <i class="las la-arrow-left"></i> {{ __tr('Back to States') }}
                                        </a>
                                        <span class="text-muted small" id="selected-state-name"></span>
                                    </div>
                                    <ul class="location-list" id="city-list">
                                        <!-- Cities will be loaded here -->
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!--price range filter -->
                        <div class="filter-item mb-4">
                            <div class="filter-item-content priceRange">
                                <h5 class="cateTitle mb-2 postdateTitle">{{ __tr('Price Range') }}</h5>
                                <input type="hidden" name="price_range_value" id="price_range_value">
                                <div class="price-input">
                                    <div class="field">
                                        <div class="min_price_range priceRangeWraper">
                                            <span class="site_currency_symbol">$</span>
                                            <input type="number" class="input-min"
                                                value="{{ request('min_price', 0) }}">
                                        </div>
                                    </div>
                                    <div class="separator">-</div>
                                    <div class="field">
                                        <div class="max_price_range priceRangeWraper">
                                            <span class="site_currency_symbol">$</span>
                                            <input type="number" class="input-max"
                                                value="{{ request('max_price', 50000) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="price_range_setup">
                                    <div id="price-slider"></div>
                                </div>
                                <!-- cancel and apply button start -->
                                <div class="cancel_apply_section_start mt-3">
                                    <button type="button" class="filter-btn w-100"
                                        id="price_wise_filter_apply">{{ __tr('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                        <!--price range filter end -->

                        <div class="filter-item mb-4">
                            <div class="filter-item-content">
                                <h5 class="cateTitle mb-2 postdateTitle">{{ __tr('Types') }}</h5>
                                <div class="filter-radio-group">
                                    <label class="filter-radio-label">
                                        <input type="radio" name="type_radio" value="featured"
                                            {{ request('type') == 'featured' ? 'checked' : '' }}>
                                        <span>{{ __tr('Featured') }}</span>
                                    </label>
                                    <label class="filter-radio-label">
                                        <input type="radio" name="type_radio" value="top_listing"
                                            {{ request('type') == 'top_listing' ? 'checked' : '' }}>
                                        <span>{{ __tr('Top Listing') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="filter-item mb-4">
                            <div class="filter-item-content">
                                <h5 class="cateTitle mb-2 postdateTitle">{{ __tr('Condition') }}</h5>
                                <div class="filter-radio-group">
                                    @foreach ($conditions as $condition)
                                        <label class="filter-radio-label">
                                            <input type="radio" name="condition_radio" value="{{ $condition->id }}"
                                                {{ request('condition') == $condition->id ? 'checked' : '' }}>
                                            <span>{{ $condition->translation('title') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="filter-item mb-4">
                            <div class="filter-item-content">
                                <h5 class="cateTitle mb-2 postdateTitle">{{ __tr('Date Posted') }}</h5>
                                <div class="filter-radio-group">
                                    <label class="filter-radio-label">
                                        <input type="radio" name="date_posted_radio" value="today"
                                            {{ request('date_posted') == 'today' ? 'checked' : '' }}>
                                        <span>{{ __tr('Today') }}</span>
                                    </label>
                                    <label class="filter-radio-label">
                                        <input type="radio" name="date_posted_radio" value="yesterday"
                                            {{ request('date_posted') == 'yesterday' ? 'checked' : '' }}>
                                        <span>{{ __tr('Yesterday') }}</span>
                                    </label>
                                    <label class="filter-radio-label">
                                        <input type="radio" name="date_posted_radio" value="last_week"
                                            {{ request('date_posted') == 'last_week' ? 'checked' : '' }}>
                                        <span>{{ __tr('Last Week') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Right Content -->
            <div class="listing-content-area">

                <div class="listing-items-wrapper">
                    <div class="content-part">
                        <div class="items-list">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="listing-header p-2 gap-3">
                                        <div class="search-top-row d-flex justify-content-between align-items-center">
                                            <div class="content-title">
                                                <h4>
                                                    @if ($breadcrumbChildCategory)
                                                        {{ $breadcrumbChildCategory->translation('title') }}
                                                    @elseif($breadcrumbSubcategory)
                                                        {{ $breadcrumbSubcategory->translation('title') }}
                                                    @elseif($breadcrumbCategory)
                                                        {{ $breadcrumbCategory->translation('title') }}
                                                    @else
                                                        {{ __tr('All Ads') }}
                                                    @endif
                                                </h4>
                                                <p class="mb-0">{{ $ads->total() }}
                                                    {{ $ads->total() == 1 ? __tr('Ad') : __tr('Ads') }}
                                                    {{ __tr('found') }}</p>
                                            </div>
                                            <div class="sidebar-btn d-lg-none">
                                                <a href="javascript:void(0)"><i class="las la-bars"></i></a>
                                            </div>
                                        </div>
                                        <div class="sort-by-wrapper d-flex justify-content-end">
                                            <select id="search_by_sorting" class="form-select">
                                                <option value="">{{ __tr('Sort By') }}</option>
                                                <option value="latest_listing"
                                                    {{ request('sortby') == 'latest_listing' ? 'selected' : '' }}>
                                                    {{ __tr('Latest Listing') }}
                                                </option>
                                                <option value="lowest_price"
                                                    {{ request('sortby') == 'lowest_price' ? 'selected' : '' }}>
                                                    {{ __tr('Lowest Price') }}
                                                </option>
                                                <option value="highest_price"
                                                    {{ request('sortby') == 'highest_price' ? 'selected' : '' }}>
                                                    {{ __tr('Highest Price') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Advertisements: listing_top --}}
                        @include('frontend.components.ad-slot', ['position' => 'listing_top'])
                        {{-- End Advertisements --}}

                        <div class="grid-view custom-tab-content active">
                            <div class="listing-card-wrapper d-flex" id="ads-container">
                                @forelse($ads as $ad)
                                    <x-single-listing :ad="$ad" />
                                @empty
                                    <x-empty-result :title="__tr('No Ads found')" :message="__tr('Try adjusting your filters to find what you\'re looking for')" />
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Pagination -->
                @if ($ads->hasPages())
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="pagination mt-60">
                                <div class="blog-pagination">
                                    <div class="custom-pagination mt-4 mt-lg-5">
                                        {{ $ads->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>
    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                const SUBCATEGORY_ENDPOINT = '/ad/subcategories';

                // ========== Price Range Slider ==========
                const priceSliderEl = document.getElementById('price-slider');
                if (priceSliderEl) {
                    const minVal = parseInt($('.input-min').val()) || 0;
                    const maxVal = parseInt($('.input-max').val()) || 50000;

                    noUiSlider.create(priceSliderEl, {
                        start: [minVal, maxVal],
                        connect: true,
                        range: {
                            'min': 0,
                            'max': 50000
                        },
                        step: 100,
                        format: {
                            to: function(value) {
                                return Math.round(value);
                            },
                            from: function(value) {
                                return Number(value);
                            }
                        }
                    });

                    // Sync slider to inputs
                    priceSliderEl.noUiSlider.on('update', function(values) {
                        $('.input-min').val(values[0]);
                        $('.input-max').val(values[1]);
                    });

                    // Sync inputs to slider
                    $('.input-min').on('change', function() {
                        priceSliderEl.noUiSlider.set([$(this).val(), null]);
                    });
                    $('.input-max').on('change', function() {
                        priceSliderEl.noUiSlider.set([null, $(this).val()]);
                    });
                }
                const COUNTRIES_ENDPOINT = '/ad/countries';
                const STATES_ENDPOINT = '/ad/states';
                const CITIES_ENDPOINT = '/ad/cities';

                const TRANS_BACK_TO = '{{ __tr('Back to') }}';
                const TRANS_CATEGORIES = '{{ __tr('Categories') }}';
                const TRANS_NO_COUNTRIES = '{{ __tr('No countries available') }}';

                // ========== Category Navigation System ==========

                let categoryHierarchy = {}; // Store parent relationships as we navigate

                // Handle category click
                $(document).on('click', '#category-list .category-link', function(e) {
                    e.preventDefault();
                    const $item = $(this).closest('.category-item');
                    const categoryId = $item.data('category-id');
                    const categoryName = $item.data('category-name');
                    const parentId = $item.data('parent-id');
                    const parentName = $item.data('parent-name');

                    // Store parent relationship for back navigation
                    if (parentId) {
                        categoryHierarchy[categoryId] = {
                            parentId: parentId,
                            parentName: parentName
                        };
                    }

                    // Set selected category and submit form
                    $('#selected_category_id').val(categoryId);
                    $('#search_listings_form').submit();
                });

                // Handle back button click
                $('#back-button').on('click', function() {
                    const selectedCategoryId = '{{ request('cat_id') }}';
                    const parentInfo = categoryHierarchy[selectedCategoryId];

                    if (parentInfo && parentInfo.parentId) {
                        // Go to parent category
                        $('#selected_category_id').val(parentInfo.parentId);
                    } else {
                        // Go to root (clear category filter)
                        $('#selected_category_id').val('');
                    }
                    $('#search_listings_form').submit();
                });

                // Load and display categories on page load
                function loadCategories() {
                    const selectedCategoryId = '{{ request('cat_id') }}';

                    if (!selectedCategoryId) {
                        // No category selected - already showing root categories from backend
                        $('#back-button-container').hide();
                        return;
                    }

                    // Try to load children of selected category
                    $.get(SUBCATEGORY_ENDPOINT, {
                        parent_id: selectedCategoryId
                    }, function(children) {
                        const $categoryList = $('#category-list');

                        if (children.length > 0) {
                            // Selected category HAS children - show them
                            $categoryList.empty();
                            // Remove any existing selected category display
                            $('#selected-category-display').remove();

                            // Extract parent info from first child (all children share same parent)
                            const currentCategoryTitle = children[0].parent_title ||
                                '{{ __tr('Selected Category') }}';
                            const currentCategoryParentId = children[0].parent_id || null;
                            const currentCategoryGrandparentTitle = children[0].grandparent_title ||
                                null;

                            // Show selected category display
                            let selectedCategoryHtml = `
                                <div id="selected-category-display" style="margin-left: 12px;">
                                    <strong style="color: var(--main-color-one); font-size: 16px;">
                                        <i class="las la-folder-open" style="margin-right: 5px;"></i>
                                        ${currentCategoryTitle}
                                    </strong>
                                </div>
                            `;
                            $('#back-button-container').after(selectedCategoryHtml);

                            children.forEach(function(child) {
                                $categoryList.append(`
                                    <li class="category-item"
                                        data-category-id="${child.id}"
                                        data-category-name="${child.title}"
                                        data-parent-id="${selectedCategoryId}"
                                        data-parent-name="${currentCategoryTitle}">
                                        <a href="javascript:void(0)" class="category-link">
                                            <i class="las la-angle-right"></i>
                                            ${child.title}
                                        </a>
                                    </li>
                                `);

                                // Store parent relationship for each child
                                categoryHierarchy[child.id] = {
                                    parentId: selectedCategoryId,
                                    parentName: currentCategoryTitle
                                };
                            });

                            // Store current category's parent relationship for back navigation
                            if (currentCategoryParentId) {
                                categoryHierarchy[selectedCategoryId] = {
                                    parentId: currentCategoryParentId,
                                    parentName: currentCategoryGrandparentTitle || TRANS_CATEGORIES
                                };
                                $('#back-button-text').text(TRANS_BACK_TO + ' ' +
                                    currentCategoryGrandparentTitle);
                            } else {
                                // Current category is at root level
                                categoryHierarchy[selectedCategoryId] = {
                                    parentId: null,
                                    parentName: TRANS_CATEGORIES
                                };
                                $('#back-button-text').text(TRANS_BACK_TO + ' ' + TRANS_CATEGORIES);
                            }

                            $('#back-button-container').show();
                        } else {
                            // Selected category has NO children - keep showing current level with selected active
                            // Remove selected category display if exists
                            $('#selected-category-display').remove();

                            $('#category-list .category-item').removeClass('active');
                            $(`.category-item[data-category-id="${selectedCategoryId}"]`).addClass(
                                'active');

                            // Get parent info from the list item
                            const $selectedItem = $(
                                `.category-item[data-category-id="${selectedCategoryId}"]`);
                            const parentId = $selectedItem.data('parent-id');
                            const parentName = $selectedItem.data('parent-name');

                            if (parentName) {
                                $('#back-button-text').text(TRANS_BACK_TO + ' ' + parentName);

                                // Store for back navigation
                                if (!categoryHierarchy[selectedCategoryId]) {
                                    categoryHierarchy[selectedCategoryId] = {
                                        parentId: parentId,
                                        parentName: parentName
                                    };
                                }
                            } else {
                                $('#back-button-text').text(TRANS_BACK_TO + ' ' + TRANS_CATEGORIES);
                            }

                            $('#back-button-container').show();
                        }
                    });
                }

                // Store parent relationships from initial page load
                $('#category-list .category-item').each(function() {
                    const catId = $(this).data('category-id');
                    const parentId = $(this).data('parent-id');
                    const parentName = $(this).data('parent-name');

                    if (parentId) {
                        categoryHierarchy[catId] = {
                            parentId: parentId,
                            parentName: parentName
                        };
                    }
                });

                // Initialize categories on page load
                loadCategories();

                // ========== Location Hierarchical Selection ==========

                // Load countries on page load
                function loadCountries(callback) {
                    $.get(COUNTRIES_ENDPOINT, function(data) {
                        const $countryList = $('#country-list');
                        $countryList.empty();

                        if (data.length > 0) {
                            data.forEach(function(country) {
                                const isActive = '{{ request('country') }}' == country.id ?
                                    'active' : '';
                                $countryList.append(`
                                    <li class="location-item ${isActive}" data-country-id="${country.id}" data-country-name="${country.text}">
                                        <a href="javascript:void(0)" class="location-link country-link">
                                            <i class="las la-angle-right"></i>
                                            ${country.text}
                                        </a>
                                    </li>
                                `);
                            });
                        } else {
                            $countryList.append(
                                `<li class="text-muted small p-2">${TRANS_NO_COUNTRIES}</li>`);
                        }

                        if (callback) callback();
                    });
                }

                // Function to restore location filter based on URL parameters
                function restoreLocationFilter() {
                    const selectedCountryId = '{{ request('country') }}';
                    const selectedStateId = '{{ request('state') }}';
                    const selectedCityId = '{{ request('city') }}';

                    // Always load countries first so back navigation works
                    loadCountries(function() {
                        if (selectedCityId && selectedStateId && selectedCountryId) {
                            // Restore city view
                            $.get(COUNTRIES_ENDPOINT, function(countries) {
                                const country = countries.find(c => c.id == selectedCountryId);
                                if (country) {
                                    $('#selected-country-name').text(country.text);
                                }
                            });

                            $.get(STATES_ENDPOINT, {
                                country_id: selectedCountryId
                            }, function(states) {
                                // Populate state list for back navigation
                                const $stateList = $('#state-list');
                                $stateList.empty();
                                states.forEach(function(state) {
                                    const isActive = selectedStateId == state.id ?
                                        'active' : '';
                                    $stateList.append(`
                                        <li class="location-item ${isActive}" data-state-id="${state.id}" data-state-name="${state.text}">
                                            <a href="javascript:void(0)" class="location-link state-link">
                                                <i class="las la-angle-right"></i>
                                                ${state.text}
                                            </a>
                                        </li>
                                    `);
                                });

                                const state = states.find(s => s.id == selectedStateId);
                                if (state) {
                                    $('#selected-state-name').text(state.text);
                                }
                            });

                            $.get(CITIES_ENDPOINT, {
                                state_id: selectedStateId
                            }, function(cities) {
                                const $cityList = $('#city-list');
                                $cityList.empty();
                                cities.forEach(function(city) {
                                    const isActive = selectedCityId == city.id ?
                                        'active' : '';
                                    $cityList.append(`
                                        <li class="location-item ${isActive}" data-city-id="${city.id}">
                                            <a href="javascript:void(0)" class="location-link city-link">
                                                <i class="las la-map-marker"></i>
                                                ${city.text}
                                            </a>
                                        </li>
                                    `);
                                });

                                $('#country-section').hide();
                                $('#state-section').hide();
                                $('#city-section').show();
                            });

                        } else if (selectedStateId && selectedCountryId) {
                            // Restore city view for selected state
                            $.get(COUNTRIES_ENDPOINT, function(countries) {
                                const country = countries.find(c => c.id == selectedCountryId);
                                if (country) {
                                    $('#selected-country-name').text(country.text);
                                }
                            });

                            // Populate state list for back navigation
                            $.get(STATES_ENDPOINT, {
                                country_id: selectedCountryId
                            }, function(states) {
                                const $stateList = $('#state-list');
                                $stateList.empty();
                                states.forEach(function(state) {
                                    const isActive = selectedStateId == state.id ?
                                        'active' : '';
                                    $stateList.append(`
                                        <li class="location-item ${isActive}" data-state-id="${state.id}" data-state-name="${state.text}">
                                            <a href="javascript:void(0)" class="location-link state-link">
                                                <i class="las la-angle-right"></i>
                                                ${state.text}
                                            </a>
                                        </li>
                                    `);
                                });

                                const state = states.find(s => s.id == selectedStateId);
                                if (state) {
                                    $('#selected-state-name').text(state.text);
                                }
                            });

                            // Check if selected state has cities
                            $.get(CITIES_ENDPOINT, {
                                state_id: selectedStateId
                            }, function(cities) {
                                if (cities.length > 0) {
                                    const $cityList = $('#city-list');
                                    $cityList.empty();
                                    cities.forEach(function(city) {
                                        $cityList.append(`
                                            <li class="location-item" data-city-id="${city.id}">
                                                <a href="javascript:void(0)" class="location-link city-link">
                                                    <i class="las la-map-marker"></i>
                                                    ${city.text}
                                                </a>
                                            </li>
                                        `);
                                    });

                                    $('#country-section').hide();
                                    $('#state-section').hide();
                                    $('#city-section').show();
                                } else {
                                    // No cities - show states with selected one active
                                    $('#country-section').hide();
                                    $('#state-section').show();
                                }
                            });

                        } else if (selectedCountryId) {
                            // Check if selected country has states
                            $.get(STATES_ENDPOINT, {
                                country_id: selectedCountryId
                            }, function(states) {
                                if (states.length > 0) {
                                    const $stateList = $('#state-list');
                                    $stateList.empty();
                                    states.forEach(function(state) {
                                        $stateList.append(`
                                            <li class="location-item" data-state-id="${state.id}" data-state-name="${state.text}">
                                                <a href="javascript:void(0)" class="location-link state-link">
                                                    <i class="las la-angle-right"></i>
                                                    ${state.text}
                                                </a>
                                            </li>
                                        `);
                                    });

                                    $.get(COUNTRIES_ENDPOINT, function(countries) {
                                        const country = countries.find(c => c.id ==
                                            selectedCountryId);
                                        if (country) {
                                            $('#selected-country-name').text(country
                                                .text);
                                        }
                                    });

                                    $('#country-section').hide();
                                    $('#state-section').show();
                                }
                                // If no states, countries are already shown with active state from loadCountries
                            });
                        }
                        // If no location selected, countries are already loaded and shown
                    });
                }

                // Load countries on page load and restore filter
                restoreLocationFilter();

                // Handle country click - always filter by country
                $(document).on('click', '.country-link', function(e) {
                    e.preventDefault();
                    const $item = $(this).closest('.location-item');
                    const countryId = $item.data('country-id');

                    $('#selected_country').val(countryId);
                    $('#selected_state').val('');
                    $('#selected_city').val('');
                    $('#search_listings_form').submit();
                });

                // Handle state click - always filter by state
                $(document).on('click', '.state-link', function(e) {
                    e.preventDefault();
                    const $item = $(this).closest('.location-item');
                    const stateId = $item.data('state-id');

                    $('#selected_state').val(stateId);
                    $('#selected_city').val('');
                    $('#search_listings_form').submit();
                });

                // Handle city click
                $(document).on('click', '.city-link', function(e) {
                    e.preventDefault();
                    const $item = $(this).closest('.location-item');
                    const cityId = $item.data('city-id');

                    $('.location-item').removeClass('active');
                    $item.addClass('active');

                    $('#selected_city').val(cityId);
                    $('#search_listings_form').submit();
                });

                // Back to countries - clear all location filters and reload
                $('#back-to-countries').on('click', function() {
                    $('#selected_country').val('');
                    $('#selected_state').val('');
                    $('#selected_city').val('');
                    $('#search_listings_form').submit();
                });

                // Back to states - clear state/city and reload with country only
                $('#back-to-states').on('click', function() {
                    $('#selected_state').val('');
                    $('#selected_city').val('');
                    $('#search_listings_form').submit();
                });

                // ========== Other Filters (keeping existing functionality) ==========

                // Price Range Filter
                $('#price_wise_filter_apply').on('click', function() {
                    const minPrice = $('.input-min').val();
                    const maxPrice = $('.input-max').val();

                    $('#min_price').val(minPrice);
                    $('#max_price').val(maxPrice);

                    $('#search_listings_form').submit();
                });

                // Condition Filter
                $('input[name="condition_radio"]').on('change', function() {
                    $('#condition').val($(this).val());
                    $('#search_listings_form').submit();
                });

                // Type Filter
                $('input[name="type_radio"]').on('change', function() {
                    $('#type').val($(this).val());
                    $('#search_listings_form').submit();
                });

                // Date Posted Filter
                $('input[name="date_posted_radio"]').on('change', function() {
                    $('#date_posted').val($(this).val());
                    $('#search_listings_form').submit();
                });

                // Sorting Filter
                $('#search_by_sorting').on('change', function() {
                    const sortValue = $(this).val();
                    $('#sortby_hidden').val(sortValue);
                    $('#search_listings_form').submit();
                });

                // Active filter states are handled by checked attribute on radio buttons

            });
        })(jQuery);
    </script>
@endsection
