@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('My Favorites') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')
    <div class="my-listings-header">
        <h1>{{ __tr('My Favorites') }}</h1>
        <span class="fav-count-badge">{{ $totalCount }} {{ __tr('saved') }}</span>
    </div>

    <form id="fav-filter-form" method="GET" action="{{ route('member.favourites') }}">
        <div class="filters-row">
            <div class="search-box">
                <span class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7.33333 12.6667C10.2789 12.6667 12.6667 10.2789 12.6667 7.33333C12.6667 4.38781 10.2789 2 7.33333 2C4.38781 2 2 4.38781 2 7.33333C2 10.2789 4.38781 12.6667 7.33333 12.6667Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M14 14L11.1 11.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <input type="text" name="q" placeholder="{{ __tr('Search your favourites...') }}"
                    value="{{ request('q') }}">
            </div>
            <select name="sortby" class="sort-select" onchange="this.form.submit()">
                <option value="">{{ __tr('Sort by: Latest') }}</option>
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
        <div class="listings-grid" id="fav-grid">
            @foreach ($ads as $ad)
                <div class="listing-card" id="fav-card-{{ $ad->id }}">
                    <div class="listing-image">
                        <a href="{{ route('ad.details.page', $ad->uid) }}">
                            <img src="{{ asset(getFilePath($ad->thumbnail_image)) }}" alt="{{ $ad->title }}">
                        </a>

                        @if ($ad->is_sold == config('settings.general_status.active'))
                            <span class="listing-status sold">{{ __tr('Sold') }}</span>
                        @elseif ($ad->is_featured == config('settings.general_status.active'))
                            <span class="listing-status featured">{{ __tr('Featured') }}</span>
                        @elseif ($ad->status == config('settings.general_status.active'))
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
                            <button class="listing-action-btn remove-fav" data-ad-id="{{ $ad->id }}"
                                data-toggle-url="{{ route('ad.favorite.toggle') }}" title="Remove from favourites">
                                <i class="las la-heart fav-heart-icon"></i>
                            </button>
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
                        </div>

                        <div class="listing-date">
                            {{ __tr('Saved') }} · {{ __tr('Posted') }}
                            {{ $ad->created_at->diffForHumans() }}
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
            <h3>{{ __tr('No favorites yet') }}</h3>
            <p>
                @if (request('q'))
                    {{ __tr('No saved ads match your search. Try a different keyword.') }}
                @else
                    {{ __tr("Start saving listings you like and they'll appear here.") }}
                @endif
            </p>
            @if (request('q'))
                <a href="{{ route('member.favourites') }}" class="cmn-btn">{{ __tr('View All Favorites') }}</a>
            @else
                <a href="{{ route('ad.listing.page') }}" class="cmn-btn">{{ __tr('Browse Listings') }}</a>
            @endif
        </div>
    @endif
@endsection

@section('dashboard-js')
    <script>
        (function($) {
            "use strict";

            // Search on enter
            $('.search-box input').on('keypress', function(e) {
                if (e.which === 13) $('#fav-filter-form').submit();
            });

            // Remove from favourites
            $(document).on('click', '.remove-fav', function() {
                var $btn = $(this);
                var adId = $btn.data('ad-id');
                var url = $btn.data('toggle-url');
                var $card = $('#fav-card-' + adId);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ad_id: adId
                    },
                    success: function(res) {
                        if (!res.saved) {
                            $card.fadeOut(300, function() {
                                $(this).remove();
                            });
                            toastr_warning_js(res.message);
                        }
                    },
                    error: function() {
                        toastr_error_js('Something went wrong. Please try again.');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
