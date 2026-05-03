@extends('frontend.layouts.master')
@section('meta')
    <title>{{ get_setting('site_name') }} - {{ get_setting('site_tagline') }}</title>
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ get_setting('site_meta_title') }}">
    <meta name="description" content="{{ get_setting('site_meta_description') }}">
    <meta name="keywords" content="{{ get_setting('site_meta_keys') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ get_setting('site_meta_title') }}">
    <meta property="og:description" content="{{ get_setting('site_meta_description') }}">
    <meta property="og:image" content="{{ asset(getFilePath(get_setting('site_meta_image'))) }}">

    <!-- Twitter -->
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="{{ get_setting('site_meta_title') }}">
    <meta property="twitter:description" content="{{ get_setting('site_meta_description') }}">
    <meta property="twitter:card" content="{{ asset(getFilePath(get_setting('site_meta_image'))) }}">
@endsection
@section('content')
    @php
        $promoPrinted = false;
    @endphp

    @foreach ($homeSections as $section)
        @switch($section->key)
            @case('banner')
                <x-home.banner :categories="$categories" :totalAdsCount="$totalAdsCount" />
            @break

            @case('ad_slot')
                @include('frontend.components.ad-slot', ['position' => 'home_top'])
            @break

            @case('categories')
                <x-home.categories :categories="$categories" />
            @break

            @case('top_listings')
                <x-home.top-listings :topListings="$topListings" />
            @break

            @case('promo')
                @php $promoPrinted = true; @endphp
                <x-home.promo />
            @break

            @case('pricing_plans')
                <x-home.pricing-plans :pricingPlans="$pricingPlans" />
            @break

            @case('featured_ads')
                <x-home.featured-ads :featuredAds="$featuredAds" />
            @break

            @case('recent_listings')
                <x-home.recent-listings :recentListings="$recentListings" />
            @break
        @endswitch
    @endforeach

    {{-- Category-wise listings are always rendered after top_listings --}}
    @foreach ($categoryWiseListings as $index => $catListing)
        <x-home.category-listing :catListing="$catListing" />

        {{-- Insert promo section after first category listing if promo section is not active --}}
        @if ($index === 0 && !$promoPrinted)
            <x-home.promo />
        @endif
    @endforeach

    {{-- Show promo standalone if no category listings and promo section is not active --}}
    @if (count($categoryWiseListings) === 0 && !$promoPrinted)
        <x-home.promo />
    @endif
@endsection
