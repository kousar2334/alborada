@props(['catListing'])

<section class="feature-item" data-padding-top="50" data-padding-bottom="50">
    <div class="container">
        <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
            <h2 class="head3">{{ $catListing['category']->translation('title') }}</h2>
            <a href="{{ route('ad.listing.page', ['cat' => $catListing['category']->id]) }}"
                class="see-all">{{ __tr('See All') }} <i class="las la-angle-right"></i></a>
        </div>
        <div class="slider-inner-margin">
            @foreach ($catListing['ads'] as $ad)
                <x-single-listing :ad="$ad" />
            @endforeach
        </div>
    </div>
</section>
