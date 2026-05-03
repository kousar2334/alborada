@extends('frontend.layouts.master')

@section('meta')
    <title>Pricing Plans - {{ get_setting('site_name') }}</title>
@endsection

@section('content')
    <!-- Breadcrumb Section -->
    <section class="contact-breadcrumb-area">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <span class="page-tag">{{ __tr('Our Plans') }}</span>
                <h1>{{ __tr('Pricing Plans') }}</h1>
                <p class="sub-text">
                    {{ __tr('Select a membership plan that suits your needs') }}
                </p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">{{ __tr('Home') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __tr('Pricing Plans') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- Pricing Cards Section -->
    <section class="pricing-card" data-padding-top="50" data-padding-bottom="100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-7 col-md-10 col-sm-10">
                    <div class="section-tittle text-center mb-50">
                        <h2 class="head3">{{ __tr('Choose Your Plan') }}</h2>
                        <p class="pera mt-3">{{ __tr('Select a membership plan that suits your needs') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @forelse ($pricingPlans as $plan)
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        @include('frontend.includes.pricing-card', ['plan' => $plan])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center">
                            <p class="pera">{{ __tr('No pricing plans available at the moment.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
