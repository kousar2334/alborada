@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('My Ads') }} - {{ get_setting('site_name') }}</title>
@endsection
@section('dashboard-content')
    <div class="my-listings-header">
        <h1>{{ __tr('My Ads') }}</h1>
        <a href="{{ route('ad.post.page') }}" class="cmn-btn">
            <span>+</span> {{ __tr('Post New Ad') }}
        </a>
    </div>

    <div class="listing-tabs">
        <a href="{{ route('member.my.listings') }}" class="listing-tab {{ !request('status') ? 'active' : '' }}">
            {{ __tr('All') }} <span class="count">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('member.my.listings', ['status' => 'active']) }}"
            class="listing-tab {{ request('status') == 'active' ? 'active' : '' }}">
            {{ __tr('Active') }} <span class="count">{{ $activeCount }}</span>
        </a>
        <a href="{{ route('member.my.listings', ['status' => 'inactive']) }}"
            class="listing-tab {{ request('status') == 'inactive' ? 'active' : '' }}">
            {{ __tr('Inactive') }} <span class="count">{{ $inactiveCount }}</span>
        </a>
        <a href="{{ route('member.my.listings', ['status' => 'sold']) }}"
            class="listing-tab {{ request('status') == 'sold' ? 'active' : '' }}">
            {{ __tr('Sold') }} <span class="count">{{ $soldCount }}</span>
        </a>
    </div>

    <form id="listings-filter-form" method="GET" action="{{ route('member.my.listings') }}">
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="filters-row">
            <div class="search-box">
                <span class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7.33333 12.6667C10.2789 12.6667 12.6667 10.2789 12.6667 7.33333C12.6667 4.38781 10.2789 2 7.33333 2C4.38781 2 2 4.38781 2 7.33333C2 10.2789 4.38781 12.6667 7.33333 12.6667Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M14 14L11.1 11.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <input type="text" name="q" placeholder="{{ __tr('Search your listings...') }}"
                    value="{{ request('q') }}">
            </div>
            <select name="sortby" class="sort-select" onchange="this.form.submit()">
                <option value="">{{ __tr('Sort by: Latest') }}</option>
                <option value="oldest" {{ request('sortby') == 'oldest' ? 'selected' : '' }}>
                    {{ __tr('Oldest First') }}</option>
                <option value="price_low" {{ request('sortby') == 'price_low' ? 'selected' : '' }}>
                    {{ __tr('Price: Low to High') }}
                </option>
                <option value="price_high" {{ request('sortby') == 'price_high' ? 'selected' : '' }}>
                    {{ __tr('Price: High to Low') }}
                </option>
            </select>
        </div>
    </form>

    @if ($ads->count() > 0)
        <div class="listings-grid">
            @foreach ($ads as $ad)
                <div class="listing-card">
                    <div class="listing-image">
                        <a href="{{ route('ad.details.page', $ad->uid) }}">
                            <img src="{{ asset(getFilePath($ad->thumbnail_image)) }}" alt="{{ $ad->title }}">
                        </a>

                        @if ($ad->is_sold == config('settings.general_status.active'))
                            <span class="listing-status sold">{{ __tr('Sold') }}</span>
                        @elseif($ad->is_featured == config('settings.general_status.active'))
                            <span class="listing-status featured">{{ __tr('Featured') }}</span>
                        @elseif($ad->status == config('settings.general_status.active'))
                            <span class="listing-status active">{{ __tr('Active') }}</span>
                        @else
                            <span class="listing-status inactive">{{ __tr('Inactive') }}</span>
                        @endif

                        <div class="listing-actions-overlay">
                            <a href="{{ route('ad.details.page', $ad->uid) }}" class="listing-action-btn" title="View">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 8C1 8 3.5 3 8 3C12.5 3 15 8 15 8C15 8 12.5 13 8 13C3.5 13 1 8 1 8Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="{{ route('member.ad.edit', $ad->uid) }}" class="listing-action-btn" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.333 2.00004C11.5081 1.82494 11.716 1.68605 11.9447 1.59129C12.1735 1.49653 12.4187 1.44775 12.6663 1.44775C12.914 1.44775 13.1592 1.49653 13.3879 1.59129C13.6167 1.68605 13.8246 1.82494 13.9997 2.00004C14.1748 2.17513 14.3137 2.383 14.4084 2.61178C14.5032 2.84055 14.552 3.08575 14.552 3.33337C14.552 3.58099 14.5032 3.82619 14.4084 4.05497C14.3137 4.28374 14.1748 4.49161 13.9997 4.66671L4.99967 13.6667L1.33301 14.6667L2.33301 11L11.333 2.00004Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="listing-content">
                        <h3 class="listing-title">
                            <a href="{{ route('ad.details.page', $ad->uid) }}">{{ $ad->title }}</a>
                        </h3>

                        <div class="listing-meta">
                            <svg width="14" height="14" viewBox="0 0 16 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.99984 7.83332C5.99984 8.36376 6.21055 8.87246 6.58562 9.24754C6.9607 9.62261 7.4694 9.83332 7.99984 9.83332C8.53027 9.83332 9.03898 9.62261 9.41405 9.24754C9.78912 8.87246 9.99984 8.36376 9.99984 7.83332C9.99984 7.30289 9.78912 6.79418 9.41405 6.41911C9.03898 6.04404 8.53027 5.83332 7.99984 5.83332C7.4694 5.83332 6.9607 6.04404 6.58562 6.41911C6.21055 6.79418 5.99984 7.30289 5.99984 7.83332Z"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M11.7712 11.6047L8.94251 14.4333C8.6925 14.6831 8.35356 14.8234 8.00017 14.8234C7.64678 14.8234 7.30785 14.6831 7.05784 14.4333L4.22851 11.6047C3.48265 10.8588 2.97473 9.90845 2.76896 8.8739C2.5632 7.83934 2.66883 6.767 3.07251 5.79247C3.47618 4.81795 4.15977 3.98501 5.03683 3.39899C5.91388 2.81297 6.94502 2.50018 7.99984 2.50018C9.05466 2.50018 10.0858 2.81297 10.9629 3.39899C11.8399 3.98501 12.5235 4.81795 12.9272 5.79247C13.3308 6.767 13.4365 7.83934 13.2307 8.8739C13.0249 9.90845 12.517 10.8588 11.7712 11.6047Z"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>{{ $ad->cityInfo?->name ? $ad->cityInfo->name . ', ' . $ad->stateInfo?->name : __tr('Unknown Location') }}</span>
                        </div>

                        <div class="listing-footer">
                            <span class="listing-price">{{ format_amount($ad->price) }}</span>
                            <span class="listing-views">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 8C1 8 3.5 3 8 3C12.5 3 15 8 15 8C15 8 12.5 13 8 13C3.5 13 1 8 1 8Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                {{ $ad->view_counter ?? 0 }}
                            </span>
                        </div>

                        <div class="listing-date">
                            {{ __tr('Posted') }} {{ $ad->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($ads->hasPages())
            <div class="pagination-wrapper">
                {{ $ads->links() }}
            </div>
        @endif
    @else
        <div class="empty-listings">
            <div class="icon">📦</div>
            <h3>{{ __tr('No listings found') }}</h3>
            <p>
                @if (request('status') || request('q'))
                    {{ __tr('No listings match your current filters. Try adjusting your search criteria.') }}
                @else
                    {{ __tr("You haven't posted any listings yet. Start by creating your first ad!") }}
                @endif
            </p>
            @if (!request('status') && !request('q'))
                <a href="{{ route('ad.post.page') }}" class="cmn-btn">
                    <span>+</span> {{ __tr('Post Your First Ad') }}
                </a>
            @else
                <a href="{{ route('member.my.listings') }}" class="cmn-btn">
                    {{ __tr('View All Listings') }}
                </a>
            @endif
        </div>
    @endif
@endsection
@section('dashboard-js')
    <script>
        (function($) {
            "use strict";

            // Search on enter key
            $('.search-box input').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#listings-filter-form').submit();
                }
            });
        })(jQuery);
    </script>
@endsection
