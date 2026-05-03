@props(['recentListings'])

@if ($recentListings->count() > 0)
    <section class="feature-item" data-padding-top="50" data-padding-bottom="100"
        style="background-color:rgb(255, 255, 255)">
        <div class="container">
            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                <h2 class="head3">{{ p_trans('home_recent_title', null, 'Recent Listing') }}</h2>
                <a href="{{ route('ad.listing.page', ['sortby' => 'latest_listing']) }}"
                    class="see-all">{{ __tr('See All') }} <i class="las la-angle-right"></i></a>
            </div>
            <div class="slider-inner-margin">
                @foreach ($recentListings as $ad)
                    <x-single-listing :ad="$ad" />
                @endforeach
            </div>
        </div>
    </section>
@endif
