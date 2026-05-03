@props(['categories', 'totalAdsCount'])

<section class="home-banner-v2"
    style="background-image: url({{ asset(get_setting('banner_bg_image', 'public/uploads/media-uploader/header1717328732.png')) }});">
    <div class="banner-v2-overlay"></div>

    <div class="banner-orb banner-orb-1"></div>
    <div class="banner-orb banner-orb-2"></div>
    <div class="banner-orb banner-orb-3"></div>

    <div class="container position-relative" style="z-index:1;">
        <div class="banner-v3-wrap">

            @php $badgeText = p_trans('home_banner_badge', null, get_setting('banner_badge_text', '#1 Classified Ad Platform in the US')); @endphp
            @if ($badgeText)
                <div class="banner-v2-badge wow fadeInDown" data-wow-delay="0.1s">
                    <span class="badge-icon">&#127942;</span>
                    <span>{{ $badgeText }}</span>
                </div>
            @endif

            <h1 class="banner-v2-title wow fadeInUp" data-wow-delay="0.2s">
                {{ p_trans('home_banner_title', null, get_setting('banner_title', 'Buy & Sell Anything, All in One Place')) }}
            </h1>

            <p class="banner-v2-desc wow fadeInUp" data-wow-delay="0.3s">
                {{ p_trans('home_banner_subtitle', null, get_setting('banner_description', "Country's most loved and trusted classified ad listing website. Browse thousands of classified items near you.")) }}
            </p>

            <div class="banner-v2-search-wrap wow fadeInUp" data-wow-delay="0.4s">
                <form action="{{ route('ad.listing.page') }}"
                    class="d-flex align-items-stretch banner-search-location banner-v2-form" method="get">
                    <div class="banner-v2-inputs d-flex align-items-center flex-grow-1">
                        <div class="banner-v2-input-group flex-grow-1" style="position:relative;">
                            <div class="banner-v2-input-icon">
                                <i class="las la-search"></i>
                            </div>
                            <input class="banner-v2-field w-100" type="text" name="q" id="home_search"
                                placeholder="{{ __tr('What are you looking for?') }}">
                            <span id="all_search_result" class="search_with_text_section"></span>
                        </div>
                    </div>
                    <button type="submit" class="banner-v2-btn setLocation_btn">
                        <i class="las la-search"></i>
                        <span class="d-none d-sm-inline">{{ __tr('Search') }}</span>
                    </button>
                </form>
            </div>

            @if ($categories->count() > 0)
                <div class="banner-quick-cats wow fadeInUp" data-wow-delay="0.5s">
                    <span class="banner-quick-label">{{ __tr('Browse:') }}</span>
                    @foreach ($categories->take(8) as $cat)
                        <a href="{{ route('ad.listing.page', $cat->permalink) }}" class="banner-quick-pill">
                            {{ $cat->title }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="banner-v2-stats wow fadeInUp" data-wow-delay="0.6s">
                <div class="stat-pill">
                    <strong>{{ number_format($totalAdsCount) }}+</strong>
                    <span>{{ __tr('Live Ads') }}</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-pill">
                    <strong>{{ $categories->count() }}</strong>
                    <span>{{ __tr('Categories') }}</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-pill">
                    <strong>{{ __tr('Free') }}</strong>
                    <span>{{ __tr('To Post') }}</span>
                </div>
            </div>

        </div>
    </div>
</section>
