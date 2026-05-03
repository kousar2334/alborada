@extends('frontend.layouts.master')
@section('meta')
    <title>{{ $ad->title }} - {{ get_setting('site_name') }}</title>
    <link rel="stylesheet" href="{{ asset('public/web-assets/frontend/css/magnific-popup.min.css') }}">

    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
@endsection

@section('content')
    @php $sellerUser = $ad->userInfo; @endphp

    {{-- Advertisements: details_top --}}
    @include('frontend.components.ad-slot', [
        'position' => 'details_top',
        'advertisements' => $advertisementsTop,
    ])
    {{-- End Advertisements --}}

    <!--Listing Details-->
    <div class="container">
        <div class="item-details item-details-content">
            <div class="row justify-content-center">
                {{-- Left Column --}}
                <div class="col-xl-8 col-lg-8 col-md-8">
                    {{-- Title, Price, Date & Location --}}
                    <div class="short-description">
                        <div class="left-part mb-4">
                            <div class="product-name-price">
                                <div class="product-name">{{ $ad->title }}</div>
                                <div class="right-part text-right">
                                    <div class="price text-end">
                                        <span>{{ format_amount($ad->price) }}</span>
                                        @if ($ad->is_negotiable == config('settings.general_status.active'))
                                            <div class="token">{{ __tr('NEGOTIABLE') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="date-location">
                                <span>{{ __tr('Posted on') }}
                                    <span class="posted">{{ $ad->created_at->format('d F Y') }}</span>
                                </span>
                                @if ($ad->categoryInfo)
                                    <span class="vertical-divider"></span>
                                    <span>{{ __tr('Category') }} <span
                                            class="posted">{{ $ad->categoryInfo->title }}</span></span>
                                @endif
                                @php
                                    $locationParts = array_filter([
                                        $ad->cityInfo?->translation('name') ?? null,
                                        $ad->stateInfo?->translation('name') ?? null,
                                        $ad->countryInfo?->translation('name') ?? null,
                                    ]);
                                @endphp
                                @if (count($locationParts) > 0)
                                    <span class="vertical-divider"></span>
                                    <span>{{ __tr('Location') }}
                                        <span class="posted">{{ implode(', ', $locationParts) }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Image Gallery Slider --}}
                    @php
                        $allImages = collect();
                        if ($ad->thumbnail_image) {
                            $allImages->push($ad->thumbnail_image);
                        }
                        foreach ($ad->galleryImages as $galleryImage) {
                            $allImages->push($galleryImage->image_path);
                        }
                    @endphp

                    @if ($allImages->count() > 0)
                        <div class="product-view-wrap">
                            <div class="details-gallery-slider slider-inner-margin">
                                @foreach ($allImages as $image)
                                    <div class="single-main-image">
                                        <a href="javascript:void(0)" data-mfp-src="{{ asset(getFilePath($image, false)) }}"
                                            class="long-img image-link">
                                            <img src="{{ asset(getFilePath($image, false)) }}"
                                                alt="{{ $ad->title }}" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            @if ($allImages->count() > 1)
                                <div class="thumb-wrap">
                                    <div class="details-gallery-nav slider-inner-margin">
                                        @foreach ($allImages as $image)
                                            <div class="single-thumb">
                                                <img src="{{ asset(getFilePath($image, false)) }}"
                                                    alt="{{ $ad->title }}" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="product-view-wrap text-center p-4">
                            <img class="no-image" src="{{ getFilePath(null) }}" alt="No image available" />
                        </div>
                    @endif

                    {{-- Description Section --}}
                    <div class="item-description box-shadow1">
                        {{-- $customFields and $fieldModels passed from controller --}}
                        @if ($ad->condition || $fieldModels->count() > 0)
                            <div class="description-top">
                                <div class="row gy-4">
                                    @if ($ad->condition)
                                        <div class="col-4">
                                            {{ __tr('Condition') }}: <span
                                                class="text-bold">{{ $ad->condition->title }}</span>
                                        </div>
                                    @endif
                                    @if ($customFields)
                                        @foreach ($customFields as $field)
                                            @php
                                                $fieldModel = $fieldModels->get($field['flied_id'] ?? null);
                                            @endphp
                                            @if ($fieldModel && $field['type'] != 7)
                                                <div class="col-4">
                                                    {{ $fieldModel->title }}: <span
                                                        class="text-bold">{{ $field['value'] }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="divider"></div>
                        @endif

                        {{-- Description --}}
                        <div class="description-tab">
                            <h4 class="des-tittle">{{ __tr('Description') }}</h4>
                            <div class="product__details__para" id="description">{!! $ad->description !!}</div>
                            <button id="showMoreButton" class="show-more-btn">{{ __tr('Show More') }}</button>
                        </div>

                        {{-- Tags --}}
                        @if ($ad->tags->count() > 0)
                            <div class="description-bottom">
                                <h4 class="des-tittle">{{ __tr('Tags') }}</h4>
                                <div class="tags">
                                    @foreach ($ad->tags as $tag)
                                        <a
                                            href="{{ route('ad.listing.page') }}?tag_id={{ $tag->id }}">{{ $tag->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Seller Info --}}
                    <div class="seller-part mt-3 d-md-none">
                        @include('frontend.pages.ad._seller_info', ['ad' => $ad, 'isMobile' => true])
                    </div>

                    {{-- Relevant Ads --}}
                    @if ($relevantAds->count() > 0)
                        <div class="relevant-ads box-shadow1">
                            <h4 class="des-tittle">{{ __tr('Relevant Ads') }}</h4>
                            <div class="add-wraper relevant-listing-wrapper">
                                @foreach ($relevantAds as $relAd)
                                    <div class="single-ad-card">
                                        <div class="single-add-image">
                                            <a href="{{ route('ad.details.page', $relAd->uid) }}">
                                                <img src="{{ asset(getFilePath($relAd->thumbnail_image)) }}"
                                                    alt="{{ $relAd->title }}" />
                                            </a>
                                        </div>
                                        <div class="single-add-body">
                                            <h4 class="add-heading head4">
                                                <a
                                                    href="{{ route('ad.details.page', $relAd->uid) }}">{{ $relAd->title }}</a>
                                            </h4>
                                            <div class="btn-wrapper">
                                                @if ($relAd->is_featured == config('settings.general_status.active'))
                                                    <span class="pro-btn2">
                                                        <svg width="7" height="10" viewBox="0 0 7 10" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="white" />
                                                        </svg>
                                                        {{ __tr('FEATURED') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="pricing head4">{{ format_amount($relAd->price) }}</div>
                                            <p class="listing-titled-flex align-items-center gap-1">
                                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M5.99984 7.83332C5.99984 8.36376 6.21055 8.87246 6.58562 9.24754C6.9607 9.62261 7.4694 9.83332 7.99984 9.83332C8.53027 9.83332 9.03898 9.62261 9.41405 9.24754C9.78912 8.87246 9.99984 8.36376 9.99984 7.83332C9.99984 7.30289 9.78912 6.79418 9.41405 6.41911C9.03898 6.04404 8.53027 5.83332 7.99984 5.83332C7.4694 5.83332 6.9607 6.04404 6.58562 6.41911C6.21055 6.79418 5.99984 7.30289 5.99984 7.83332Z"
                                                        stroke="#64748B" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M11.7712 11.6047L8.94251 14.4333C8.6925 14.6831 8.35356 14.8234 8.00017 14.8234C7.64678 14.8234 7.30785 14.6831 7.05784 14.4333L4.22851 11.6047C3.48265 10.8588 2.97473 9.90845 2.76896 8.8739C2.5632 7.83934 2.66883 6.767 3.07251 5.79247C3.47618 4.81795 4.15977 3.98501 5.03683 3.39899C5.91388 2.81297 6.94502 2.50018 7.99984 2.50018C9.05466 2.50018 10.0858 2.81297 10.9629 3.39899C11.8399 3.98501 12.5235 4.81795 12.9272 5.79247C13.3308 6.767 13.4365 7.83934 13.2307 8.8739C13.0249 9.90845 12.517 10.8588 11.7712 11.6047Z"
                                                        stroke="#64748B" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span class="oneLine">
                                                    {{ $relAd->cityInfo->name ?? '' }}{{ $relAd->stateInfo ? ', ' . $relAd->stateInfo->name : '' }}
                                                </span>
                                            </p>
                                            <div class="dates">
                                                <svg width="14" height="13" viewBox="0 0 14 13" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9 7.83333L7 6.5V3.16667M1 6.5C1 7.28793 1.15519 8.06815 1.45672 8.7961C1.75825 9.52405 2.20021 10.1855 2.75736 10.7426C3.31451 11.2998 3.97595 11.7417 4.7039 12.0433C5.43185 12.3448 6.21207 12.5 7 12.5C7.78793 12.5 8.56815 12.3448 9.2961 12.0433C10.0241 11.7417 10.6855 11.2998 11.2426 10.7426C11.7998 10.1855 12.2417 9.52405 12.5433 8.7961C12.8448 8.06815 13 7.28793 13 6.5C13 5.71207 12.8448 4.93185 12.5433 4.2039C12.2417 3.47595 11.7998 2.81451 11.2426 2.25736C10.6855 1.70021 10.0241 1.25825 9.2961 0.956723C8.56815 0.655195 7.78793 0.5 7 0.5C6.21207 0.5 5.43185 0.655195 4.7039 0.956723C3.97595 1.25825 3.31451 1.70021 2.75736 2.25736C2.20021 2.81451 1.75825 3.47595 1.45672 4.2039C1.15519 4.93185 1 5.71207 1 6.5Z"
                                                        stroke="#64748B" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span>{{ $relAd->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="favorite-icon">
                                            <a href="javascript:void(0)" class="click_to_favorite_add_remove"
                                                data-listing_id="{{ $relAd->id }}"
                                                data-toggle-url="{{ route('ad.favorite.toggle') }}"
                                                data-is-auth="{{ auth()->check() ? 'true' : 'false' }}"
                                                data-login-url="{{ route('member.login') }}">
                                                <i class="lar la-heart icon favorite_add_icon"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="divider"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column / Sidebar --}}
                <div class="col-xl-4 col-lg-4 col-md-4">
                    <div class="seller-part">
                        {{-- Desktop Seller Info --}}
                        <div class="d-none d-md-block">
                            @include('frontend.pages.ad._seller_info', ['ad' => $ad, 'isMobile' => false])
                        </div>

                        {{-- Actions: Save, Report, Share --}}
                        <div class="list-info-card p-0">
                            <div class="sid-quick-actions">
                                <a href="javascript:void(0)"
                                    class="sid-quick-btn sid-fav-btn click_to_favorite_add_remove {{ $isFavourited ? 'is-favorite' : '' }}"
                                    data-listing_id="{{ $ad->id }}"
                                    data-toggle-url="{{ route('ad.favorite.toggle') }}"
                                    data-is-auth="{{ auth()->check() ? 'true' : 'false' }}"
                                    data-login-url="{{ route('member.login') }}">
                                    <i class="{{ $isFavourited ? 'las' : 'lar' }} la-heart icon favorite_add_icon"></i>
                                    <span>{{ $isFavourited ? __tr('Saved') : __tr('Save') }}</span>
                                </a>
                                <div class="sid-quick-sep"></div>
                                <a href="javascript:void(0)" class="sid-quick-btn sid-report-btn"
                                    data-is-auth="{{ auth()->check() ? '1' : '0' }}"
                                    data-login-url="{{ route('member.login') }}">
                                    <svg width="15" height="17" viewBox="0 0 16 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 10H15L10.5 5.5L15 1H1V17" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span>{{ __tr('Report') }}</span>
                                </a>
                            </div>
                            <div class="sid-share-row">
                                <span class="sid-share-label">{{ __tr('Share') }}</span>
                                <div class="sid-share-icons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                        target="_blank" rel="noopener" class="sid-share-icon sid-share-fb"
                                        title="Facebook">
                                        <i class="lab la-facebook-f"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($ad->title) }}&url={{ urlencode(request()->url()) }}"
                                        target="_blank" rel="noopener" class="sid-share-icon sid-share-tw"
                                        title="Twitter / X">
                                        <i class="lab la-twitter"></i>
                                    </a>
                                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->url()) }}&description={{ urlencode($ad->title) }}"
                                        target="_blank" rel="noopener" class="sid-share-icon sid-share-pt"
                                        title="Pinterest">
                                        <i class="lab la-pinterest-p"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode($ad->title . ' ' . request()->url()) }}"
                                        target="_blank" rel="noopener" class="sid-share-icon sid-share-wa"
                                        title="WhatsApp">
                                        <i class="lab la-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Advertisements: details_sidebar --}}
                        @include('frontend.components.ad-slot', ['position' => 'details_sidebar'])
                        {{-- End Advertisements --}}

                        {{-- Safety Tips --}}
                        @if (isset($safetyTips) && $safetyTips->count() > 0)
                            <div class="safety-tips">
                                <h3 class="head5">{{ __tr('Safety Tips') }}</h3>
                                <div class="safety-wrapper">
                                    <ol>
                                        @foreach ($safetyTips as $tip)
                                            <li class="safety-tip-item">
                                                <i class="las la-check-circle safety-tip-icon"></i>
                                                <span>{{ $tip->translation('title') }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Message Seller Modal --}}
    @auth
        @if ($sellerUser && auth()->id() !== $sellerUser->id)
            <div class="modal fade" id="seller-message-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z"
                                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <h5 class="modal-title">{{ __tr('Message') }} {{ $sellerUser->name }}</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('member.messages.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ad_id" value="{{ $ad->id }}">
                            <div class="modal-body">
                                <div class="msg-listing-ref">
                                    @if ($ad->thumbnail_image)
                                        <img src="{{ asset(getFilePath($ad->thumbnail_image, false)) }}"
                                            alt="{{ $ad->title }}" class="msg-listing-img">
                                    @endif
                                    <div>
                                        <div class="msg-listing-label">{{ __tr('Ad') }}</div>
                                        <strong>{{ Str::limit($ad->title, 55) }}</strong>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label msg-form-label">{{ __tr('Message') }}</label>
                                    <textarea name="message" class="form-control" rows="5"
                                        placeholder="{{ __tr('Hi, I am interested in your listing. Is it still available?') }}" required
                                        maxlength="2000"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-bs-dismiss="modal">{{ __tr('Cancel') }}</button>
                                <button type="submit" class="modal-send-btn">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __tr(' Send Message') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    {{-- Report Modal --}}
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">{{ __tr(key: 'Report this ad') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        @csrf
                        <input type="hidden" name="ad_id" value="{{ $ad->id }}">
                        <div class="form-group mb-20">
                            <label for="reportReason">{{ __tr('Reason') }}</label>
                            <select class="form-select" id="reportReason" name="reason_id">
                                @forelse ($reportReasons as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->translation('title') }}</option>
                                @empty
                                    <option value="" disabled>{{ __tr('No reasons found') }}</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group mb-20">
                            <label for="reportMessage">{{ __tr('Message') }}</label>
                            <textarea class="form-control" id="reportMessage" name="message" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __tr('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="submitReport">{{ __tr('Submit Report') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/web-assets/frontend/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('/public/web-assets/backend/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {

                /* =============================
                 * Select2 Safe Destroy
                 * ============================= */
                if ($.fn.select2 && $('#reportReason').data('select2')) {
                    $('#reportReason').select2('destroy');
                }

                /* =============================
                 * Phone Reveal
                 * ============================= */
                $(document).on('click', '.sid-phone-trigger, .sid-phone-triggerForResponsive', function(e) {
                    const $el = $(this);
                    if ($el.data('revealed')) return;

                    e.preventDefault();

                    const phone = $el.data('phone');
                    const prefix = $el.data('prefix');

                    $el.find(`.sid-phone-display${prefix}`).text(phone);
                    $el.find(`.sid-phone-hint${prefix}`).text('Tap to call');

                    $el.attr('href', `tel:${phone}`);
                    $el.data('revealed', true);
                });

                /* =============================
                 * Show More / Less
                 * ============================= */
                const $description = $('#description');
                const $toggleBtn = $('#showMoreButton');

                if ($description.length) {
                    if ($description[0].scrollHeight <= 200) {
                        $toggleBtn.hide();
                    }

                    $toggleBtn.on('click', function() {
                        const expanded = $description.hasClass('expanded');

                        $description.toggleClass('expanded')
                            .css('max-height', expanded ? '' : 'none');

                        $(this).text(expanded ? 'Show More' : 'Show Less');
                    });
                }

                /* =============================
                 * Report Button
                 * ============================= */
                $(document).on('click', '.sid-report-btn', function() {
                    const isAuth = $(this).data('is-auth') == 1;

                    if (isAuth) {
                        $('#reportModal').modal('show');
                    } else {
                        window.location.href = $(this).data('login-url');
                    }
                });

                /* =============================
                 * Report Submit
                 * ============================= */
                $('#submitReport').on('click', function() {
                    const $btn = $(this);

                    $btn.prop('disabled', true).text('Submitting...');

                    $.ajax({
                        url: '{{ route('ad.report') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ad_id: $('#reportForm input[name="ad_id"]').val(),
                            reason_id: $('#reportReason').val(),
                            message: $('#reportMessage').val(),
                        },
                        success: function(res) {
                            $('#reportModal').modal('hide');
                            toastr.success(res.message);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ||
                                'Something went wrong.');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).text('Submit Report');
                        }
                    });
                });

                /* =============================
                 * favorite Toggle
                 * ============================= */
                $(document).on('click', '.click_to_favorite_add_remove', function(e) {
                    e.preventDefault();

                    const $btn = $(this);

                    if (!$btn.data('is-auth')) {
                        window.location.href = $btn.data('login-url');
                        return;
                    }

                    $.post($btn.data('toggle-url'), {
                            _token: '{{ csrf_token() }}',
                            ad_id: $btn.data('listing_id')
                        })
                        .done(function(res) {
                            const saved = res.saved;

                            $btn.toggleClass('is-favorite', saved);
                            $btn.find('.favorite_add_icon')
                                .toggleClass('las', saved)
                                .toggleClass('lar', !saved);

                            $btn.find('span').text(saved ? 'Saved' : 'Save');

                            toastr[saved ? 'success' : 'warning'](res.message);
                        })
                        .fail(function() {
                            toastr.error('Something went wrong.');
                        });
                });

                /* =============================
                 * Slick Slider
                 * ============================= */
                const $main = $('.details-gallery-slider');
                const $nav = $('.details-gallery-nav');

                if ($main.length && $.fn.slick) {

                    // Prevent double init
                    if ($main.hasClass('slick-initialized')) $main.slick('unslick');
                    if ($nav.hasClass('slick-initialized')) $nav.slick('unslick');

                    $main.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        fade: true,
                        asNavFor: $nav.length ? '.details-gallery-nav' : null,
                        prevArrow: '<div class="prev-icon"><i class="las la-angle-left"></i></div>',
                        nextArrow: '<div class="next-icon"><i class="las la-angle-right"></i></div>'
                    });

                    if ($nav.length) {
                        $nav.slick({
                            slidesToShow: 6,
                            slidesToScroll: 1,
                            asNavFor: '.details-gallery-slider',
                            focusOnSelect: true,
                            arrows: false,
                            responsive: [{
                                    breakpoint: 1200,
                                    settings: {
                                        slidesToShow: 5
                                    }
                                },
                                {
                                    breakpoint: 992,
                                    settings: {
                                        slidesToShow: 4
                                    }
                                },
                                {
                                    breakpoint: 450,
                                    settings: {
                                        slidesToShow: 3
                                    }
                                },
                                {
                                    breakpoint: 350,
                                    settings: {
                                        slidesToShow: 2
                                    }
                                }
                            ]
                        });
                    }
                }

                /* =============================
                 * Magnific Popup (FINAL FIX)
                 * ============================= */
                if ($.fn.magnificPopup) {

                    $('.details-gallery-slider').magnificPopup({
                        delegate: 'a.image-link',
                        type: 'image',
                        gallery: {
                            enabled: true
                        }
                    });

                }

            });

        })(jQuery);
    </script>
@endsection
