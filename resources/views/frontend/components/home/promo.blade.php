<section class="promoSection">
    <div class="container">
        <div class="promoWrapper">
            <div class="promo-orb-1"></div>
            <div class="promo-orb-2"></div>

            <div class="promo-compact-content wow fadeInUp" data-wow-delay="0.1s">
                <span class="promo-badge">{{ p_trans('home_promo_badge', null, 'Your Local Marketplace') }}</span>
                <h2 class="promo-heading">
                    {{ p_trans('home_promo_heading', null, 'Earn cash by selling or find anything you desire') }}
                </h2>
                <p class="promo-text">
                    {{ p_trans('home_promo_text', null, 'List your pre-loved or new items in minutes, or browse thousands of ads to find exactly what you need — all in one place.') }}
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="{{ route('ad.post.page') }}" class="promo-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path
                                d="M8 1a.5.5 0 0 1 .5.5V7h5.5a.5.5 0 0 1 0 1H8.5v5.5a.5.5 0 0 1-1 0V8H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z" />
                        </svg>
                        {{ p_trans('home_promo_btn_primary', null, 'Post Your Ad') }}
                    </a>
                    <a href="{{ route('ad.listing.page') }}" class="promo-btn-outline">
                        {{ p_trans('home_promo_btn_secondary', null, 'Browse Ads') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
