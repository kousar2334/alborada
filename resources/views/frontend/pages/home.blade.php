@extends('frontend.layouts.master')

@section('body-class')
    dark-page
@endsection

@section('meta')
    <title>{{ get_setting('site_name', 'Alborada IPTV') }} |
        {{ p_trans('home_meta_tagline', null, 'Premium Streaming Service') }}</title>
    <meta name="description"
        content="{{ p_trans('home_meta_desc', null, get_setting('site_meta_description', 'Premium IPTV service with 40K+ channels and 150K+ VOD titles.')) }}">
@endsection

@section('content')
    <main>

        {{-- ── HERO ── --}}
        @if (!isset($sections['hero']) || $sections['hero']->is_active)
            <section class="hero" id="hero">
                <div class="hero-content-wrap">
                    <div class="hero-left">
                        <h1>{!! p_trans('home_hero_heading', null, 'Best IPTV subscription #1<br>for the USA and Canada.') !!}</h1>
                        <p class="hero-desc">
                            {{ p_trans('home_hero_desc', null, 'Experience unbeatable entertainment with the best IPTV service, offering the fastest and most reliable server in 4K, FHD, HD, and SD quality.') }}
                        </p>
                        <div class="hero-ctas">
                            <a href="#pricing"
                                class="btn btn-pink btn-lg">{{ p_trans('home_hero_btn1', null, 'Get Started') }}</a>
                            <a href="{{ route('member.register') }}"
                                class="btn btn-pink btn-lg">{{ p_trans('home_hero_btn2', null, 'Free Trial') }}</a>
                        </div>
                    </div>
                    <div class="hero-right">
                        @if (get_setting('home_hero_image'))
                            <img src="{{ asset(getFilePath(get_setting('home_hero_image'))) }}"
                                alt="{{ p_trans('home_hero_heading', null, 'Premium IPTV Streaming') }}" class="hero-img">
                        @else
                            <img src="https://picsum.photos/seed/iptv-hero/700/460" alt="Premium IPTV Streaming"
                                class="hero-img">
                        @endif
                        <div class="hero-price-tag">
                            <span class="from-label">{{ p_trans('home_hero_from_label', null, 'FROM') }}</span>
                            <span class="price-num">{{ p_trans('home_hero_from_price', null, '11.99$') }}</span>
                        </div>
                    </div>
                </div>
                <div class="hero-stats-bar">
                    <div class="hero-stats-row">
                        <div class="hero-stat-item">
                            <div class="stat-num">{{ p_trans('home_stat1_num', null, '40K+') }}</div>
                            <div class="stat-label">{{ p_trans('home_stat1_label', null, 'Live Channels') }}</div>
                        </div>
                        <div class="hero-stat-item">
                            <div class="stat-num">{{ p_trans('home_stat2_num', null, '150K+') }}</div>
                            <div class="stat-label">{{ p_trans('home_stat2_label', null, 'Movies / Series') }}</div>
                        </div>
                        <div class="hero-stat-item">
                            <div class="stat-num">{{ p_trans('home_stat3_num', null, '10K+') }}</div>
                            <div class="stat-label">{{ p_trans('home_stat3_label', null, 'Customers Happy') }}</div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── ABOUT ── --}}
        @if (!isset($sections['about']) || $sections['about']->is_active)
            <section id="about">
                <div class="wrap">
                    <div class="about-grid">
                        <div class="about-left">
                            <div class="sec-label sec-label-start">
                                {{ p_trans('home_about_label', null, 'About ' . get_setting('site_name', 'Alborada')) }}
                            </div>
                            <h2 class="about-heading">
                                {{ p_trans('home_about_heading', null, 'The streaming service built for serious viewers') }}
                            </h2>
                            <p class="about-desc">
                                {{ p_trans('home_about_desc', null, 'We built this service with one goal: deliver the most reliable, feature-rich IPTV experience available. We combine the fastest servers, the largest content library, and dedicated 24/7 human support so you never miss a moment.') }}
                            </p>
                            <a href="#features"
                                class="btn btn-outline btn-lg">{{ p_trans('home_about_btn', null, 'Discover More') }}</a>
                        </div>
                        <div class="about-stats">
                            <div class="about-stat-card">
                                <div class="about-stat-val">{{ p_trans('home_about_stat1_val', null, '40K+') }}</div>
                                <div class="about-stat-label">
                                    {{ p_trans('home_about_stat1_label', null, 'Live Channels') }}</div>
                            </div>
                            <div class="about-stat-card">
                                <div class="about-stat-val">{{ p_trans('home_about_stat2_val', null, '150K+') }}</div>
                                <div class="about-stat-label">
                                    {{ p_trans('home_about_stat2_label', null, 'Movies & Series') }}</div>
                            </div>
                            <div class="about-stat-card">
                                <div class="about-stat-val">{{ p_trans('home_about_stat3_val', null, '99.9%') }}</div>
                                <div class="about-stat-label">
                                    {{ p_trans('home_about_stat3_label', null, 'Server Uptime') }}</div>
                            </div>
                            <div class="about-stat-card">
                                <div class="about-stat-val">{{ p_trans('home_about_stat4_val', null, '24/7') }}</div>
                                <div class="about-stat-label">
                                    {{ p_trans('home_about_stat4_label', null, 'Expert Support') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── MOVIES ── --}}
        <section id="movies">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_movies_label', null, 'Content Library') }}</div>
                <div class="sec-head">
                    <h2>{{ p_trans('home_movies_heading', null, 'Featured titles & live events') }}</h2>
                    <p>{{ p_trans('home_movies_desc', null, 'Browse top films, series and sports in a cinematic preview — all instantly available on demand through your ' . get_setting('site_name', 'Alborada') . ' subscription.') }}
                    </p>
                </div>
            </div>
            <div class="wrap">
                <div class="slider-outer">
                    <div class="slider-track" id="movieSlider">
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf01/320/460" alt="Action" class="movie-poster">
                            <div class="movie-meta"><span class="genre-tag">Action</span>
                                <h4>Blockbuster Hits</h4>
                                <p>Live-action thrillers & exclusive premieres.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf02/320/460" alt="Drama" class="movie-poster">
                            <div class="movie-meta"><span class="genre-tag">Drama</span>
                                <h4>Award Dramas</h4>
                                <p>High-quality series instantly on demand.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf03/320/460" alt="Sports" class="movie-poster">
                            <div class="movie-meta"><span class="genre-tag">Sports</span>
                                <h4>Live Sports</h4>
                                <p>Top leagues & tournaments worldwide.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf04/320/460" alt="Kids" class="movie-poster">
                            <div class="movie-meta"><span class="genre-tag">Family</span>
                                <h4>Kids & Family</h4>
                                <p>Cartoons and family-friendly content.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf05/320/460" alt="Documentary" class="movie-poster">
                            <div class="movie-meta"><span class="genre-tag">Documentary</span>
                                <h4>Top Documentaries</h4>
                                <p>Premium true stories curated for you.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf06/320/460" alt="Sci-Fi" class="movie-poster">
                            <div class="movie-meta"><span class="genre-tag">Sci-Fi</span>
                                <h4>Sci-Fi & Fantasy</h4>
                                <p>Mind-bending worlds and epic adventures.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        {{-- ── FEATURED CONTENT ── --}}
        @if ((!isset($sections['featured_content']) || $sections['featured_content']->is_active) && $featuredContent->count())
            <section id="featured-content">
                <div class="wrap">
                    <div class="sec-label sec-label-center">{{ p_trans('home_featured_label', null, "What's Streaming") }}
                    </div>
                    <h2 class="home-sec-heading-center">
                        {{ p_trans('home_featured_heading', null, 'Movies, Series & Live Events') }}
                    </h2>
                    <p class="home-sec-desc-center">
                        {{ p_trans('home_featured_desc', null, 'Get access to thousands of titles. Preview what is available on your Alborada Box subscription.') }}
                    </p>
                    <div class="home-featured-scroll">
                        @foreach ($featuredContent as $fc)
                            <div class="home-content-item">
                                @if ($fc->badge_text)
                                    <span class="home-content-badge">{{ $fc->badge_text }}</span>
                                @endif
                                @if ($fc->thumbnail)
                                    <img src="{{ asset($fc->thumbnail) }}" alt="{{ $fc->title }}"
                                        class="home-content-thumb">
                                @else
                                    <div class="home-content-thumb-placeholder">
                                        <i class="fas {{ $fc->type_icon }} home-content-thumb-icon"></i>
                                    </div>
                                @endif
                                <div class="home-content-body">
                                    <div class="home-content-meta">
                                        {{ $fc->type_label }}{{ $fc->genre ? ' · ' . $fc->genre : '' }}</div>
                                    <div class="home-content-title">{{ $fc->title }}</div>
                                    @if ($fc->youtube_embed_id)
                                        <a href="https://www.youtube.com/watch?v={{ $fc->youtube_embed_id }}"
                                            target="_blank" rel="noopener" class="home-content-preview">
                                            <i class="fas fa-play"></i> {{ p_trans('home_preview_btn', null, 'Preview') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── CONTENT CATEGORIES ── --}}
        @if (!isset($sections['categories']) || $sections['categories']->is_active)
            <section id="categories">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_cat_label', null, 'Content Library') }}</div>
                    <div class="sec-head centered">
                        <h2>{{ p_trans('home_cat_heading', null, 'Something for every viewer') }}</h2>
                        <p>{{ p_trans('home_cat_desc', null, 'From live sports to the latest blockbusters — we cover every content category you love.') }}
                        </p>
                    </div>
                    <div class="categories-grid">
                        <div class="category-card">
                            <span class="category-icon">{{ p_trans('home_cat1_icon', null, '🎬') }}</span>
                            <h4>{{ p_trans('home_cat1_title', null, 'Movies & Series') }}</h4>
                            <p>{{ p_trans('home_cat1_desc', null, '150,000+ on-demand titles including the latest releases and full series box-sets.') }}
                            </p>
                        </div>
                        <div class="category-card">
                            <span class="category-icon">{{ p_trans('home_cat2_icon', null, '⚽') }}</span>
                            <h4>{{ p_trans('home_cat2_title', null, 'Sporting Events') }}</h4>
                            <p>{{ p_trans('home_cat2_desc', null, 'Live coverage of every major league — Premier League, NFL, NBA, UFC and more.') }}
                            </p>
                        </div>
                        <div class="category-card">
                            <span class="category-icon">{{ p_trans('home_cat3_icon', null, '📺') }}</span>
                            <h4>{{ p_trans('home_cat3_title', null, 'TV Shows') }}</h4>
                            <p>{{ p_trans('home_cat3_desc', null, 'Catch-up and live TV from hundreds of channels across the USA, UK, Canada and beyond.') }}
                            </p>
                        </div>
                        <div class="category-card">
                            <span class="category-icon">{{ p_trans('home_cat4_icon', null, '🎥') }}</span>
                            <h4>{{ p_trans('home_cat4_title', null, 'Documentaries') }}</h4>
                            <p>{{ p_trans('home_cat4_desc', null, 'Award-winning documentaries on nature, history, science, true crime and culture.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── FEATURES ── --}}
        @if (!isset($sections['features']) || $sections['features']->is_active)
            <section id="features">
                <div class="wrap">
                    <div class="sec-label">
                        {{ p_trans('home_feat_label', null, 'Why ' . get_setting('site_name', 'Alborada')) }}</div>
                    <div class="sec-head">
                        <h2>{{ p_trans('home_feat_heading', null, 'Everything your streaming setup needs') }}</h2>
                        <p>{{ p_trans('home_feat_desc', null, 'Professional delivery, robust infrastructure, and dedicated support — built for viewers who don\'t compromise.') }}
                        </p>
                    </div>
                    <div class="features-grid">
                        <div class="feat-card">
                            <div class="feat-icon g">{{ p_trans('home_feat1_icon', null, '📱') }}</div>
                            <h3>{{ p_trans('home_feat1_title', null, 'Fully Compatible') }}</h3>
                            <p>{{ p_trans('home_feat1_desc', null, 'Works on every device — Smart TVs, Firestick, Android, iOS, Kodi, TiviMate and more.') }}
                            </p>
                        </div>
                        <div class="feat-card">
                            <div class="feat-icon r">{{ p_trans('home_feat2_icon', null, '📡') }}</div>
                            <h3>{{ p_trans('home_feat2_title', null, 'High Availability Servers') }}</h3>
                            <p>{{ p_trans('home_feat2_desc', null, 'Resilient redundant infrastructure with advanced anti-freeze technology for near-zero interruption.') }}
                            </p>
                        </div>
                        <div class="feat-card">
                            <div class="feat-icon w">{{ p_trans('home_feat3_icon', null, '🎬') }}</div>
                            <h3>{{ p_trans('home_feat3_title', null, 'Invaluable Content') }}</h3>
                            <p>{{ p_trans('home_feat3_desc', null, '40,000+ live channels and 150,000+ VOD titles spanning every genre, language and region.') }}
                            </p>
                        </div>
                        <div class="feat-card">
                            <div class="feat-icon g">{{ p_trans('home_feat4_icon', null, '🔄') }}</div>
                            <h3>{{ p_trans('home_feat4_title', null, 'Free Updates') }}</h3>
                            <p>{{ p_trans('home_feat4_desc', null, 'Channel lists and VOD libraries are updated automatically — no manual action required.') }}
                            </p>
                        </div>
                        <div class="feat-card">
                            <div class="feat-icon r">{{ p_trans('home_feat5_icon', null, '💳') }}</div>
                            <h3>{{ p_trans('home_feat5_title', null, 'Money Back Guarantee') }}</h3>
                            <p>{{ p_trans('home_feat5_desc', null, 'Not satisfied? We offer a full refund within the guarantee window — zero questions asked.') }}
                            </p>
                        </div>
                        <div class="feat-card">
                            <div class="feat-icon w">{{ p_trans('home_feat6_icon', null, '🔒') }}</div>
                            <h3>{{ p_trans('home_feat6_title', null, '100% Secure Payment') }}</h3>
                            <p>{{ p_trans('home_feat6_desc', null, 'All transactions are encrypted end-to-end. Your payment details are never stored on our servers.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── PRICING ── --}}
        @if (!isset($sections['pricing']) || $sections['pricing']->is_active)
            <section id="pricing">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_pricing_label', null, 'Pricing') }}</div>
                    <div class="sec-head centered">
                        <h2>{{ p_trans('home_pricing_heading', null, 'Plans for every household') }}</h2>
                        <p>{{ p_trans('home_pricing_desc', null, 'Choose the package that matches your needs. Instant activation. Cancel any time.') }}
                        </p>
                    </div>

                    @if ($plans->isNotEmpty())
                        <div class="pricing-grid" id="pricingGrid">
                            @foreach ($plans as $plan)
                                @php
                                    $isFeatured = $loop->iteration === 2 || $plans->count() === 1;
                                    $priceWhole = floor($plan->price);
                                    $priceDec = '.' . str_pad((int) (($plan->price - $priceWhole) * 100), 2, '0');
                                    $btnClass = $isFeatured
                                        ? 'btn-green'
                                        : ($loop->last
                                            ? 'btn-primary'
                                            : 'btn-outline');
                                    $btnText = $isFeatured
                                        ? p_trans('home_pricing_btn_featured', null, 'Choose Plan')
                                        : p_trans('home_pricing_btn', null, 'Order Now');
                                @endphp
                                <div class="price-card {{ $isFeatured ? 'featured' : '' }}">
                                    @if ($plan->is_trial)
                                        <div class="trial-badge">★ {{ $plan->trial_days }}-Day Free Trial Available</div>
                                    @endif
                                    @if ($isFeatured)
                                        <div class="plan-badge popular">★
                                            {{ p_trans('home_pricing_popular_badge', null, 'Most Popular') }}</div>
                                    @else
                                        <div class="plan-badge basic">{{ $plan->title }}</div>
                                    @endif
                                    <div class="plan-name">{{ $plan->max_connections ?? 1 }}
                                        {{ ($plan->max_connections ?? 1) > 1 ? 'Devices' : 'Device' }}</div>
                                    <div class="plan-price-row">
                                        <div class="plan-price">${{ $priceWhole }}<span
                                                class="plan-price-dec">{{ $priceDec }}</span></div>
                                        <div class="plan-period">/ {{ $plan->duration_days }} days</div>
                                    </div>
                                    <div class="plan-desc">{{ $plan->description ?? '' }}</div>
                                    <div class="plan-divider"></div>
                                    <ul class="plan-features">
                                        <li><span class="check">✓</span> {{ $plan->max_connections ?? 1 }} concurrent
                                            screen(s)</li>
                                        <li><span class="check">✓</span> {{ $plan->streaming_quality ?? 'HD' }} quality
                                        </li>
                                        @if ($plan->catchup_days)
                                            <li><span class="check">✓</span> {{ $plan->catchup_days }}-day catch-up TV
                                            </li>
                                        @endif
                                        @if ($plan->dvr_enabled)
                                            <li><span class="check">✓</span> DVR recording</li>
                                        @endif
                                        <li><span class="check">✓</span> Instant activation</li>
                                    </ul>
                                    <div class="plan-expiry-note">
                                        <span class="expiry-dot"></span>
                                        {{ p_trans('home_pricing_expiry_note', null, 'Expiry tracked in your dashboard') }}
                                    </div>
                                    <div class="plan-divider"></div>
                                    <a href="{{ route('subscription.confirm', $plan->id) }}"
                                        class="btn {{ $btnClass }}">{{ $btnText }}</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="pricing-grid" id="pricingGrid">
                            <div class="price-card">
                                <div class="plan-badge basic">Starter</div>
                                <div class="plan-name">1 Device</div>
                                <div class="plan-price-row">
                                    <div class="plan-price">$11<span class="plan-price-dec">.99</span></div>
                                    <div class="plan-period">/ month</div>
                                </div>
                                <div class="plan-desc">Perfect for solo viewers who want the full library.</div>
                                <div class="plan-divider"></div>
                                <a href="{{ route('pricing.plans') }}" class="btn btn-outline">View Plans</a>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── UPCOMING EVENTS ── --}}
        @if ((!isset($sections['upcoming_events']) || $sections['upcoming_events']->is_active) && $upcomingEvents->count())
            <section id="upcoming-events" class="upcoming-events-section">
                <div class="wrap">
                    <div class="sec-label sec-label-center">
                        <i class="fas fa-trophy card-title-icon"></i>
                        {{ p_trans('home_events_label', null, 'Live Sports') }}
                    </div>
                    <h2 class="events-heading">
                        {{ p_trans('home_events_heading', null, 'Upcoming Live Events') }}
                    </h2>
                    <div class="events-grid">
                        @foreach ($upcomingEvents as $ev)
                            <div class="event-item">
                                @if ($ev->thumbnail)
                                    <img src="{{ asset($ev->thumbnail) }}" alt="{{ $ev->title }}"
                                        class="event-thumb">
                                @else
                                    <div class="event-thumb-placeholder">
                                        <i class="fas fa-trophy event-thumb-icon"></i>
                                    </div>
                                @endif
                                <div class="event-info">
                                    @if ($ev->badge_text)
                                        <span class="event-badge">{{ $ev->badge_text }}</span>
                                    @endif
                                    <div class="event-title">{{ $ev->title }}</div>
                                    @if ($ev->genre)
                                        <div class="event-genre">{{ $ev->genre }}</div>
                                    @endif
                                    @if ($ev->event_date)
                                        <div class="event-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $ev->event_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── REVIEWS ── --}}
        @if (!isset($sections['reviews']) || $sections['reviews']->is_active)
            <section id="reviews">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_reviews_label', null, 'Customer Reviews') }}</div>
                    <div class="sec-head">
                        <h2>{{ p_trans('home_reviews_heading', null, 'Trusted by thousands worldwide') }}</h2>
                        <p>{{ p_trans('home_reviews_desc', null, 'See why customers across Europe, North America, and the Middle East rely on us daily.') }}
                        </p>
                    </div>
                    <div class="reviews-grid">
                        <div class="review-card">
                            <div class="stars">★★★★★</div>
                            <p class="review-text">
                                "{{ p_trans('home_review1_text', null, 'Completely transformed my streaming setup. The channel selection is outstanding and the service never drops — even during peak hours.') }}"
                            </p>
                            <div class="review-author-row">
                                <div class="avatar">
                                    {{ strtoupper(substr(p_trans('home_review1_name', null, 'Alex M.'), 0, 2)) }}</div>
                                <div>
                                    <div class="author-name">{{ p_trans('home_review1_name', null, 'Alex M.') }}</div>
                                    <div class="author-loc">{{ p_trans('home_review1_loc', null, 'London, UK') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="review-card">
                            <div class="stars">★★★★★</div>
                            <p class="review-text">
                                "{{ p_trans('home_review2_text', null, 'The movie library is incredible and setup was instant. Support responded in minutes. Nothing else compares at this price point.') }}"
                            </p>
                            <div class="review-author-row">
                                <div class="avatar">
                                    {{ strtoupper(substr(p_trans('home_review2_name', null, 'Jessica K.'), 0, 2)) }}</div>
                                <div>
                                    <div class="author-name">{{ p_trans('home_review2_name', null, 'Jessica K.') }}</div>
                                    <div class="author-loc">{{ p_trans('home_review2_loc', null, 'Toronto, CA') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="review-card">
                            <div class="stars">★★★★★</div>
                            <p class="review-text">
                                "{{ p_trans('home_review3_text', null, 'Best IPTV I\'ve used. Works perfectly on Firestick, my smart TV, and mobile with zero buffering. Highly recommended.') }}"
                            </p>
                            <div class="review-author-row">
                                <div class="avatar">
                                    {{ strtoupper(substr(p_trans('home_review3_name', null, 'Omar S.'), 0, 2)) }}</div>
                                <div>
                                    <div class="author-name">{{ p_trans('home_review3_name', null, 'Omar S.') }}</div>
                                    <div class="author-loc">{{ p_trans('home_review3_loc', null, 'Dubai, AE') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── PORTAL PREVIEW ── --}}
        @if (!isset($sections['portal']) || $sections['portal']->is_active)
            <section id="portal">
                <div class="wrap">
                    <div class="sec-label">Client Portal</div>
                    <div class="portal-grid">
                        <div class="portal-mock">
                            <div class="portal-mock-bar">
                                <div class="mock-dot red"></div>
                                <div class="mock-dot amber"></div>
                                <div class="mock-dot green"></div>
                                <span
                                    class="mock-bar-label">dashboard.{{ get_setting('site_domain', 'alborada.tv') }}</span>
                            </div>
                            <div class="portal-mock-body">
                                <div class="mock-stat-row">
                                    <div class="mock-stat-card">
                                        <div class="mock-stat-val mock-stat-val-green">Active</div>
                                        <div class="mock-stat-label">Plan Status</div>
                                    </div>
                                    <div class="mock-stat-card">
                                        <div class="mock-stat-val">2</div>
                                        <div class="mock-stat-label">Screens</div>
                                    </div>
                                    <div class="mock-stat-card">
                                        <div class="mock-stat-val">47d</div>
                                        <div class="mock-stat-label">Remaining</div>
                                    </div>
                                </div>
                                <div class="mock-plan-row">
                                    <div>
                                        <div class="mock-plan-name">2 Devices — Quarterly</div>
                                        <div class="mock-plan-exp">Expires: Aug 22, 2026</div>
                                    </div>
                                    <div class="mock-plan-status">Live</div>
                                </div>
                                <div class="mock-m3u-row">
                                    <div>
                                        <div class="mock-m3u-label">M3U Playlist URL</div>
                                        <div class="mock-m3u-url">
                                            http://stream.{{ get_setting('site_domain', 'alborada.tv') }}/get.php?username=…
                                        </div>
                                    </div>
                                    <button class="mock-copy-btn">Copy</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="sec-head sec-head-left">
                                <h2>Your streaming portal, all in one place</h2>
                                <p>Log in to manage active subscriptions, copy your M3U
                                    codes, track expiry dates, and open support tickets — from one clean dashboard.</p>
                            </div>
                            <ul class="portal-feature-list">
                                <li>
                                    <div class="portal-feature-icon">👤</div>
                                    <div class="portal-feature-text"><strong>Profile &amp; Account</strong><span>Update
                                            your email, password and contact details any time.</span></div>
                                </li>
                                <li>
                                    <div class="portal-feature-icon">📋</div>
                                    <div class="portal-feature-text"><strong>Subscription History</strong><span>See active,
                                            expired and past plans with full expiry tracking.</span></div>
                                </li>
                                <li>
                                    <div class="portal-feature-icon">🔗</div>
                                    <div class="portal-feature-text"><strong>M3U &amp; Credentials</strong><span>Instantly
                                            copy your M3U URL and login credentials to any player.</span></div>
                                </li>
                                <li>
                                    <div class="portal-feature-icon">🎧</div>
                                    <div class="portal-feature-text"><strong>Support Tickets</strong><span>Submit and track
                                            support requests directly from your account.</span></div>
                                </li>
                            </ul>
                            <div class="portal-buttons">
                                <a href="{{ route('member.login') }}" class="btn btn-primary btn-lg">Login to
                                    Portal</a>
                                <a href="{{ route('member.register') }}" class="btn btn-ghost btn-lg">Create
                                    Account</a>
                                <a href="{{ route('reseller.login') }}"
                                    class="btn btn-outline btn-lg btn-reseller-login">Reseller Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── DEVICES ── --}}
        @if (!isset($sections['devices']) || $sections['devices']->is_active)
            <section id="devices">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_devices_label', null, 'Compatibility') }}</div>
                    <div class="sec-head">
                        <h2>{{ p_trans('home_devices_heading', null, 'Works on any device') }}</h2>
                        <p>{{ p_trans('home_devices_desc', null, get_setting('site_name', 'Alborada') . ' runs on every major platform — from 65-inch smart TVs to pocket-sized phones.') }}
                        </p>
                    </div>
                    <div class="devices-grid">
                        <div class="device-card"><span class="device-emoji">📺</span>
                            <h4>{{ p_trans('home_dev1_title', null, 'Smart TVs') }}</h4>
                            <p>{{ p_trans('home_dev1_desc', null, 'Samsung, LG, Sony, Android TV and all major brands.') }}
                            </p>
                        </div>
                        <div class="device-card"><span class="device-emoji">🔥</span>
                            <h4>{{ p_trans('home_dev2_title', null, 'Firestick') }}</h4>
                            <p>{{ p_trans('home_dev2_desc', null, 'Fast setup and smooth playback on Amazon Fire devices.') }}
                            </p>
                        </div>
                        <div class="device-card"><span class="device-emoji">📱</span>
                            <h4>{{ p_trans('home_dev3_title', null, 'Mobile') }}</h4>
                            <p>{{ p_trans('home_dev3_desc', null, 'Android and iOS — watch anywhere, any time.') }}</p>
                        </div>
                        <div class="device-card"><span class="device-emoji">💻</span>
                            <h4>{{ p_trans('home_dev4_title', null, 'Desktop') }}</h4>
                            <p>{{ p_trans('home_dev4_desc', null, 'VLC, Kodi, TiviMate and all major IPTV players.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── HOW TO ORDER ── --}}
        @if (!isset($sections['setup']) || $sections['setup']->is_active)
            <section id="setup">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_setup_label', null, 'How to Order') }}</div>
                    <div class="sec-head centered">
                        <h2>{{ p_trans('home_setup_heading', null, 'Start streaming in three simple steps') }}</h2>
                        <p>{{ p_trans('home_setup_desc', null, 'Getting started takes less than 5 minutes. Your subscription activates instantly after payment.') }}
                        </p>
                    </div>
                    <div class="order-steps-grid">
                        <div class="order-step">
                            <div class="order-step-num">01</div>
                            <span class="order-step-emoji">{{ p_trans('home_step1_icon', null, '🛒') }}</span>
                            <h4>{{ p_trans('home_step1_title', null, 'Select Your Plan') }}</h4>
                            <p>{{ p_trans('home_step1_desc', null, 'Choose the subscription period and number of devices that best fits your household. All plans include the full channel and VOD library.') }}
                            </p>
                        </div>
                        <div class="order-step">
                            <div class="order-step-num">02</div>
                            <span class="order-step-emoji">{{ p_trans('home_step2_icon', null, '📧') }}</span>
                            <h4>{{ p_trans('home_step2_title', null, 'Receive Your Credentials') }}</h4>
                            <p>{{ p_trans('home_step2_desc', null, 'After payment, your M3U URL and login credentials are sent to your email instantly. No waiting, no manual processing.') }}
                            </p>
                        </div>
                        <div class="order-step">
                            <div class="order-step-num">03</div>
                            <span class="order-step-emoji">{{ p_trans('home_step3_icon', null, '🎉') }}</span>
                            <h4>{{ p_trans('home_step3_title', null, 'Enjoy Unlimited Access') }}</h4>
                            <p>{{ p_trans('home_step3_desc', null, 'Open your favourite IPTV app, enter your credentials, and start streaming 40,000+ channels and 150,000+ titles right away.') }}
                            </p>
                        </div>
                    </div>
                    <div class="setup-cta-row setup-cta-row-center">
                        <a href="#pricing"
                            class="btn btn-primary btn-lg">{{ p_trans('home_setup_btn1', null, 'Choose a Plan') }}</a>
                        <a href="{{ route('contact') }}"
                            class="btn btn-ghost btn-lg">{{ p_trans('home_setup_btn2', null, 'Contact Support') }}</a>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── WHY CHOOSE ── --}}
        @if (!isset($sections['why']) || $sections['why']->is_active)
            <section id="why">
                <div class="wrap">
                    <div class="sec-label">
                        {{ p_trans('home_why_label', null, 'Why ' . get_setting('site_name', 'Alborada')) }}</div>
                    <div class="sec-head centered">
                        <h2>{{ p_trans('home_why_heading', null, 'Why thousands choose us') }}</h2>
                        <p>{{ p_trans('home_why_desc', null, 'We built the service we always wanted — reliable, flexible, and packed with content you actually watch.') }}
                        </p>
                    </div>
                    <div class="why-grid">
                        <div class="why-card">
                            <div class="why-icon r">🎛️</div>
                            <h3>{{ p_trans('home_why1_title', null, 'Customizable Packages') }}</h3>
                            <p>{{ p_trans('home_why1_desc', null, 'Choose from 1 to 4 devices, pick your billing period from 1 to 12 months, and only pay for what you need. Upgrade or change plan any time from your dashboard.') }}
                            </p>
                        </div>
                        <div class="why-card">
                            <div class="why-icon g">📲</div>
                            <h3>{{ p_trans('home_why2_title', null, 'Multi-Device Compatibility') }}</h3>
                            <p>{{ p_trans('home_why2_desc', null, 'One subscription works across Smart TVs, Firestick, Android, iOS, Kodi, TiviMate, VLC and every major M3U-compatible player — no extra setup needed.') }}
                            </p>
                        </div>
                        <div class="why-card">
                            <div class="why-icon w">✨</div>
                            <h3>{{ p_trans('home_why3_title', null, 'High-Quality Streaming') }}</h3>
                            <p>{{ p_trans('home_why3_desc', null, 'Stream in true 4K, FHD, HD and SD depending on your connection. Our anti-freeze technology ensures smooth playback even during peak hours.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── CHANNELS ── --}}
        @if (!isset($sections['channels']) || $sections['channels']->is_active)
            <section id="channels">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_channels_label', null, 'Channel Lineup') }}</div>
                    <div class="sec-head">
                        <h2>{{ p_trans('home_channels_heading', null, 'Premium channels, curated') }}</h2>
                        <p>{{ p_trans('home_channels_desc', null, 'Scroll through our top channel categories — 40,000+ live feeds available instantly.') }}
                        </p>
                    </div>
                    <div class="channels-grid">
                        <div class="ch-card">
                            <div class="ch-icon sports">⚽</div><span class="ch-name">Sports Hub</span>
                            <div class="ch-sub">Live sports channels</div>
                        </div>
                        <div class="ch-card">
                            <div class="ch-icon movies">🎬</div><span class="ch-name">Movie Max</span>
                            <div class="ch-sub">Blockbusters & premieres</div>
                        </div>
                        <div class="ch-card">
                            <div class="ch-icon kids">🧒</div><span class="ch-name">Kids TV</span>
                            <div class="ch-sub">Family entertainment</div>
                        </div>
                        <div class="ch-card">
                            <div class="ch-icon music">🎵</div><span class="ch-name">Music Live</span>
                            <div class="ch-sub">Top music channels</div>
                        </div>
                        <div class="ch-card">
                            <div class="ch-icon news">📰</div><span class="ch-name">News 24/7</span>
                            <div class="ch-sub">Global news coverage</div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── FAQ ── --}}
        @if (!isset($sections['faq']) || $sections['faq']->is_active)
            <section id="faq">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_faq_label', null, 'FAQ') }}</div>
                    <div class="sec-head centered">
                        <h2>{{ p_trans('home_faq_heading', null, 'Common questions answered') }}</h2>
                        <p>{{ p_trans('home_faq_desc', null, 'Everything you need to know before getting started.') }}</p>
                    </div>
                    <div class="faq-list">
                        <div class="faq-item">
                            <details open>
                                <summary>{{ p_trans('home_faq1_q', null, 'What internet speed do I need?') }} <span
                                        class="faq-chevron">▾</span></summary>
                                <div class="faq-body">
                                    {{ p_trans('home_faq1_a', null, 'For HD streaming we recommend at least 10 Mbps. For 4K quality or multiple simultaneous devices, 30+ Mbps is ideal for a smooth experience.') }}
                                </div>
                            </details>
                        </div>
                        <div class="faq-item">
                            <details>
                                <summary>{{ p_trans('home_faq2_q', null, 'How fast is account activation?') }} <span
                                        class="faq-chevron">▾</span></summary>
                                <div class="faq-body">
                                    {{ p_trans('home_faq2_a', null, 'Most accounts are activated instantly after payment. Your credentials are delivered by email within minutes — sometimes seconds.') }}
                                </div>
                            </details>
                        </div>
                        <div class="faq-item">
                            <details>
                                <summary>
                                    {{ p_trans('home_faq3_q', null, 'Can I stream on multiple devices simultaneously?') }}
                                    <span class="faq-chevron">▾</span>
                                </summary>
                                <div class="faq-body">
                                    {{ p_trans('home_faq3_a', null, 'Yes. Choose a multi-device plan to stream on 2 or 4 screens at the same time. Each device can watch something different from the full library.') }}
                                </div>
                            </details>
                        </div>
                        <div class="faq-item">
                            <details>
                                <summary>{{ p_trans('home_faq4_q', null, 'Which IPTV apps are supported?') }} <span
                                        class="faq-chevron">▾</span></summary>
                                <div class="faq-body">
                                    {{ p_trans('home_faq4_a', null, 'Works with all major IPTV players including TiviMate, IPTV Smarters, GSE Smart IPTV, VLC, Kodi, and most other M3U-compatible players.') }}
                                </div>
                            </details>
                        </div>
                        <div class="faq-item">
                            <details>
                                <summary>{{ p_trans('home_faq5_q', null, 'Is there a free trial available?') }} <span
                                        class="faq-chevron">▾</span></summary>
                                <div class="faq-body">
                                    {{ p_trans('home_faq5_a', null, 'Contact our support team to ask about trial access. We want you to be confident before committing to a plan.') }}
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-divider"></div>

        {{-- ── RESELLER ── --}}
        @if (!isset($sections['reseller']) || $sections['reseller']->is_active)
            <section id="reseller">
                <div class="wrap">
                    <div class="sec-label">{{ p_trans('home_reseller_label', null, 'Reseller Program') }}</div>
                    <div class="reseller-grid">
                        <div>
                            <div class="sec-head sec-head-left">
                                <h2>
                                    {{ p_trans('home_reseller_heading', null, 'Grow a business on our infrastructure') }}
                                </h2>
                                <p>
                                    {{ p_trans('home_reseller_desc', null, 'Buy credits, provision sub-accounts for your clients, and manage everything from one reseller dashboard. REST API access included.') }}
                                </p>
                            </div>
                            <div class="reseller-feat-list">
                                <div class="reseller-feat">
                                    <div class="reseller-feat-icon">💳</div>
                                    <div class="reseller-feat-text"><strong>Credit-Based System</strong>
                                        <p>Top up your reseller wallet. Each subscription you create deducts credits — no
                                            monthly fees, no surprise invoices.</p>
                                    </div>
                                </div>
                                <div class="reseller-feat">
                                    <div class="reseller-feat-icon g">👥</div>
                                    <div class="reseller-feat-text"><strong>Sub-User Management</strong>
                                        <p>Create, renew and suspend client accounts from your panel. Full visibility on
                                            active vs. expired lines.</p>
                                    </div>
                                </div>
                                <div class="reseller-feat">
                                    <div class="reseller-feat-icon">⚙️</div>
                                    <div class="reseller-feat-text"><strong>API Automation</strong>
                                        <p>Use our REST API to automate provisioning, renewals and status checks. Build your
                                            own front-end on top.</p>
                                    </div>
                                </div>
                                <div class="reseller-feat">
                                    <div class="reseller-feat-icon g">📈</div>
                                    <div class="reseller-feat-text"><strong>Bulk Pricing Tiers</strong>
                                        <p>Higher credit top-ups unlock better wholesale rates — the more you sell, the
                                            lower your cost per line.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="portal-buttons">
                                <a href="{{ route('reseller.register') }}" class="btn btn-primary btn-lg">Become a
                                    Reseller</a>
                                <a href="{{ route('reseller.login') }}" class="btn btn-ghost btn-lg">Reseller Login</a>
                            </div>
                        </div>
                        <div class="reseller-mock">
                            <div class="reseller-mock-header">
                                <span class="reseller-mock-title">Reseller Dashboard</span>
                                <span class="reseller-credit-pill">💳 Credits: 142</span>
                            </div>
                            <div class="reseller-mock-body">
                                <div class="reseller-sub-row">
                                    <div>
                                        <div class="reseller-sub-name">client_john_doe</div>
                                        <div class="reseller-sub-expiry">Exp: Jul 15, 2026 · 2 screens</div>
                                    </div><span class="reseller-status-active">Active</span>
                                </div>
                                <div class="reseller-sub-row">
                                    <div>
                                        <div class="reseller-sub-name">client_sara_k</div>
                                        <div class="reseller-sub-expiry">Exp: Jun 30, 2026 · 1 screen</div>
                                    </div><span class="reseller-status-active">Active</span>
                                </div>
                                <div class="reseller-sub-row">
                                    <div>
                                        <div class="reseller-sub-name">client_omar_77</div>
                                        <div class="reseller-sub-expiry">Exp: May 01, 2026 · 4 screens</div>
                                    </div><span class="reseller-status-exp">Expired</span>
                                </div>
                                <div class="reseller-sub-row">
                                    <div>
                                        <div class="reseller-sub-name">client_lena_m</div>
                                        <div class="reseller-sub-expiry">Exp: Aug 12, 2026 · 2 screens</div>
                                    </div><span class="reseller-status-active">Active</span>
                                </div>
                            </div>
                            <div class="reseller-mock-footer">
                                <span class="reseller-api-note">GET /api/reseller/lines</span>
                                <a href="{{ route('contact') }}" class="btn btn-ghost btn-sm-ghost">Request API
                                    Access</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ── CTA ── --}}
        @if (!isset($sections['cta']) || $sections['cta']->is_active)
            <section>
                <div class="wrap">
                    <div class="cta-banner" id="cta">
                        <div class="cta-inner">
                            <div class="sec-label sec-label-center mb-3">
                                {{ p_trans('home_cta_label', null, 'Get Started Today') }}</div>
                            <h2>{{ p_trans('home_cta_heading', null, 'Ready to experience ' . get_setting('site_name', 'Alborada') . '?') }}
                            </h2>
                            <p>{{ p_trans('home_cta_desc', null, 'Join thousands of viewers who switched to the most reliable IPTV service available. Instant activation. No contracts.') }}
                            </p>
                            <div class="cta-buttons">
                                <a href="{{ route('pricing.plans') }}"
                                    class="btn btn-primary btn-lg">{{ p_trans('home_cta_btn1', null, 'View Plans') }}</a>
                                <a href="{{ route('contact') }}"
                                    class="btn btn-outline btn-lg">{{ p_trans('home_cta_btn2', null, 'Contact Support') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ── NEWSLETTER ── --}}
        @if (!isset($sections['newsletter']) || $sections['newsletter']->is_active)
            <section>
                <div class="wrap">
                    <div class="newsletter-section">
                        <div class="sec-label sec-label-center mb-3">
                            {{ p_trans('home_newsletter_label', null, 'Stay Updated') }}</div>
                        <h2>{{ p_trans('home_newsletter_heading', null, 'Get exclusive deals & updates') }}</h2>
                        <p>{{ p_trans('home_newsletter_desc', null, 'Subscribe to our newsletter for special offers, new channel announcements, and IPTV tips delivered straight to your inbox.') }}
                        </p>
                        <form class="newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}">
                            @csrf
                            <input type="email" name="email" class="newsletter-input"
                                placeholder="{{ p_trans('home_newsletter_placeholder', null, 'Enter your email address') }}"
                                required>
                            <button type="submit"
                                class="btn btn-primary btn-lg">{{ p_trans('home_newsletter_btn', null, 'Subscribe') }}</button>
                        </form>
                        <p class="newsletter-note">
                            {{ p_trans('home_newsletter_note', null, 'No spam. Unsubscribe any time.') }}</p>
                    </div>
                </div>
            </section>
        @endif

    </main>
@endsection

@section('js')
    <script src="{{ asset('public/web-assets/frontend/js/home-iptv.js') }}"></script>
@endsection
