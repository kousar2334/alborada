<footer>
    <div class="footer-wrapper footerStyleOne">

        {{-- ── Main footer columns ── --}}
        <div class="footer-area footer-padding">
            <div class="container">
                <div class="row gy-4">

                    {{-- Brand + Contact --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-brand">
                            @if (get_setting('site_dark_logo'))
                                <img src="{{ asset(getFilePath(get_setting('site_dark_logo'))) }}"
                                    alt="{{ get_setting('site_name') }}" class="mb-3 footer-logo-img">
                            @else
                                <h3 class="footer-site-name mb-3">{{ get_setting('site_name') }}</h3>
                            @endif

                            <p class="footer-slogan mb-3">{{ get_setting('site_tagline', 'Where every Stream sparkles') }}</p>

                            @if (get_setting('footer_address') || get_setting('footer_phone_number') || get_setting('footer_hotline'))
                                <ul class="footer-contact-list">
                                    @if (get_setting('footer_address'))
                                        <li>
                                            <i class="las la-map-marker"></i>
                                            {{ get_setting('footer_address') }}
                                        </li>
                                    @endif
                                    @if (get_setting('footer_address_2'))
                                        <li>
                                            <i class="las la-map-marker-alt"></i>
                                            {{ get_setting('footer_address_2') }}
                                        </li>
                                    @endif
                                    @if (get_setting('footer_phone_number'))
                                        <li>
                                            <i class="las la-phone-square"></i>
                                            {{ get_setting('footer_phone_number') }}
                                        </li>
                                    @endif
                                    @if (get_setting('footer_hotline'))
                                        <li>
                                            <i class="las la-headset"></i>
                                            {{ get_setting('footer_hotline') }}
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            {{-- Social Links --}}
                            @php
                                $socials = [
                                    'site_fb_link' => 'lab la-facebook-f',
                                    'site_linkedin_link' => 'lab la-linkedin-in',
                                    'site_youtube_link' => 'lab la-youtube',
                                    'site_instagram_link' => 'lab la-instagram',
                                ];
                                $hasSocial = collect($socials)->keys()->contains(fn($k) => (bool) get_setting($k));
                            @endphp
                            @if ($hasSocial)
                                <div class="footer-social-links mt-3">
                                    @foreach ($socials as $key => $icon)
                                        @if (get_setting($key))
                                            <a href="{{ get_setting($key) }}" class="footer-social-btn" target="_blank"
                                                rel="noopener noreferrer">
                                                <i class="{{ $icon }}"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>



                    {{-- Quick Links --}}
                    <div class="col-lg-2 col-md-3 col-6">
                        <h6 class="footer-tittle">{{ __tr('Quick Links') }}</h6>
                        <ul class="footer-links footer-links-icons">
                            <li><a href="{{ route('home') }}"><i class="las la-home"></i> {{ __tr('Home') }}</a></li>
                            <li><a href="{{ route('pricing.plans') }}"><i class="las la-tags"></i>
                                    {{ __tr('Pricing Plans') }}</a></li>
                            <li><a href="{{ route('frontend.blog.list') }}"><i class="las la-newspaper"></i>
                                    {{ __tr('Blog') }}</a></li>
                            <li><a href="{{ route('contact') }}"><i class="las la-envelope"></i>
                                    {{ __tr('Contact Us') }}</a></li>
                            <li><a href="{{ route('member.tickets.index') }}"><i class="las la-headset"></i>
                                    {{ __tr('Support') }}</a></li>
                            <li><a href="{{ route('member.setup.guide') }}"><i class="las la-book"></i>
                                    {{ __tr('Documentation') }}</a></li>
                        </ul>
                    </div>

                    {{-- Portals --}}
                    <div class="col-lg-2 col-md-3 col-6">
                        <h6 class="footer-tittle">{{ __tr('Portals') }}</h6>
                        <ul class="footer-links footer-links-icons">
                            <li><a href="{{ route('member.login') }}"><i class="las la-user"></i>
                                    {{ __tr('Member Login') }}</a></li>
                            <li><a href="{{ route('member.register') }}"><i class="las la-user-plus"></i>
                                    {{ __tr('Create Account') }}</a></li>
                            <li><a href="{{ route('reseller.register') }}"><i class="las la-briefcase"></i>
                                    {{ __tr('Become a Reseller') }}</a></li>
                            <li><a href="{{ route('reseller.login') }}"><i class="las la-store"></i>
                                    {{ __tr('Reseller Login') }}</a></li>
                            <li><a href="{{ route('admin.auth.login') }}"><i class="las la-user-shield"></i>
                                    {{ __tr('Admin Login') }}</a></li>
                            <li><a href="{{ route('member.download.app') }}"><i class="las la-download"></i>
                                    {{ __tr('Download App') }}</a></li>
                        </ul>
                    </div>

                    {{-- Footer Menu — each parent with children = its own column --}}
                    @if ($footer_menu_items->isNotEmpty())
                        @php
                            $grouped_items = $footer_menu_items->filter(fn($i) => $i->children->isNotEmpty());
                            $flat_items = $footer_menu_items->filter(fn($i) => $i->children->isEmpty());
                        @endphp

                        @foreach ($grouped_items as $item)
                            <div class="col-lg-2 col-md-3 col-6">
                                <h6 class="footer-tittle">{{ $item->translation('title') }}</h6>
                                <ul class="footer-links">
                                    @foreach ($item->children as $child)
                                        <li>
                                            <a href="{{ $child->link() }}"
                                                @if ($child->target) target="_blank" rel="noopener noreferrer" @endif>
                                                {{ $child->translation('title') }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach

                        @if ($flat_items->isNotEmpty())
                            <div class="col-lg-2 col-md-3 col-6">
                                <ul class="footer-links">
                                    @foreach ($flat_items as $item)
                                        <li>
                                            <a href="{{ $item->link() }}"
                                                @if ($item->target) target="_blank" rel="noopener noreferrer" @endif>
                                                {{ $item->translation('title') }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif
                    {{-- Newsletter Column --}}
                    <div class="col-lg-3 col-md-6">
                        <h6 class="footer-tittle">{{ __tr('Join Newsletter') }}</h6>
                        <p class="footer-newsletter-desc mb-10">
                            {{ __tr('Subscribe to the newsletter for all the latest updates') }}
                        </p>
                        <form id="footer-newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}">
                            @csrf
                            <div class="newsletter-input-wrap">
                                <input type="email" name="email" id="newsletter-email" class="newsletter-input"
                                    placeholder="{{ __tr('Enter your email') }}" required>
                                <button type="submit" class="newsletter-submit-btn">
                                    <i class="las la-paper-plane"></i> {{ __tr('Subscribe') }}
                                </button>
                            </div>
                            <div id="newsletter-msg" class="footer-newsletter-msg"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Copyright bar ── --}}
        <div class="footer-bottom-area">
            <div class="container">
                <div class="footer-border">
                    <div class="footer-copy-right text-center">
                        @if (get_setting('site_copy_right_text'))
                            <p class="pera">{!! get_setting('site_copy_right_text') !!}</p>
                        @else
                            <p class="pera">
                                {{ __tr('All copyright') }} &copy; {{ date('Y') }}
                                {{ get_setting('site_name') }}. {{ __tr('All Rights Reserved.') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</footer>
