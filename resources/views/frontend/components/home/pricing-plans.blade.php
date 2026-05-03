@props(['pricingPlans'])

@if ($pricingPlans->count() > 0)
    <section class="pricing-card" data-padding-top="50" data-padding-bottom="50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-7 col-md-10 col-sm-10">
                    <div class="section-tittle text-center mb-50">
                        <h2 class="head3">{{ p_trans('home_pricing_title', null, __tr('Membership')) }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($pricingPlans as $plan)
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        @include('frontend.includes.pricing-card', ['plan' => $plan])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
