@props(['topListings'])

@if ($topListings->count() > 0)
    <section class="feature-item" data-padding-top="50" data-padding-bottom="50">
        <div class="container">
            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                <h2 class="head3">{{ p_trans('home_top_listings_title', null, 'Top Listings') }}</h2>
                <a href="{{ route('ad.listing.page', ['listing_type_preferences' => 'top_listing']) }}"
                    class="see-all">{{ __tr('See All') }} <i class="las la-angle-right"></i></a>
            </div>
            <div class="slider-inner-margin">
                @foreach ($topListings as $ad)
                    <x-single-listing :ad="$ad" />
                @endforeach
            </div>
        </div>
    </section>
@endif
