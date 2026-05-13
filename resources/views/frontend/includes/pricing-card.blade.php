<div class="single-price mb-24 wow fadeInLeft" data-wow-delay="0.{{ $loop->iteration ?? 1 }}s">
    <h4 class="price">{{ $plan->translation('title') }}</h4>
    <span class="price">{{ format_amount($plan->price) }}
        <span class="sub-tittle">{{ $plan->duration_days }} {{ __tr('days') }}</span>
    </span>
    <div class="btn-wrapper">
        @if ($plan->price == 0)
            @auth
                <form action="{{ route('membership.buy') }}" method="post">
                    @csrf
                    <input type="hidden" name="membership_id" value="{{ $plan->id }}">
                    <button type="submit" class="cmn-btn-outline1">{{ __tr('Get Started') }}</button>
                </form>
            @else
                <a href="{{ route('member.login') }}" class="cmn-btn-outline1">{{ __tr('Get Started') }}</a>
            @endauth
        @else
            @auth
                <a href="{{ route('subscription.confirm', $plan->id) }}" class="cmn-btn-outline1">
                    {{ __tr('Buy Now') }}
                </a>
            @else
                <button class="cmn-btn-outline1 choose_membership_plan" data-bs-toggle="modal"
                    data-id="{{ $plan->id }}" data-price="{{ $plan->price }}" data-bs-target="#loginModal">
                    {{ __tr('Buy Now') }}
                </button>
            @endauth
        @endif
    </div>
    <ul class="listing mt-3">
        @php
            $checkSvg =
                '<svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.38561 11.9999C6.34326 11.9999 6.30137 11.9913 6.26258 11.9748C6.22378 11.9583 6.18891 11.9342 6.16017 11.9039L0.0815684 5.5059C0.0410469 5.46325 0.0141836 5.41003 0.00426649 5.35275C-0.00565067 5.29547 0.00180835 5.23662 0.0257308 5.1834C0.0496532 5.13018 0.0890012 5.08491 0.138959 5.05311C0.188917 5.02131 0.247317 5.00438 0.307013 5.00438H3.23292C3.27685 5.00438 3.32026 5.01356 3.36024 5.03128C3.40022 5.049 3.43582 5.07486 3.46465 5.10712L5.49615 7.38125C5.7157 6.9246 6.1407 6.16424 6.88652 5.23772C7.98909 3.868 10.0399 1.85355 13.5487 0.0350571C13.6165 -8.32163e-05 13.6954 -0.00920352 13.7698 0.00949718C13.8442 0.0281979 13.9086 0.0733601 13.9505 0.136066C13.9923 0.198772 14.0085 0.274464 13.9958 0.348195C13.983 0.421926 13.9424 0.488336 13.8818 0.534313C13.8684 0.5445 12.5155 1.58113 10.9586 3.4799C9.52566 5.22724 7.62085 8.0844 6.68355 11.773C6.66708 11.8378 6.62879 11.8953 6.57477 11.9365C6.52075 11.9776 6.45413 12 6.38552 12L6.38561 11.9999Z" fill="#22C55E"></path></svg>';
            $crossSvg =
                '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L11 11M1 11L11 1" stroke="#DC2626" stroke-width="2" stroke-linecap="round"/></svg>';
        @endphp

        {{-- Connections --}}
        <li class="listItem check">
            <div class="check-icon me-2">{!! $checkSvg !!}</div>
            {{ $plan->max_connections }} {{ __tr($plan->max_connections == 1 ? 'Connection' : 'Connections') }}
        </li>

        {{-- Streaming quality --}}
        <li class="listItem check">
            <div class="check-icon me-2">{!! $checkSvg !!}</div>
            {{ $plan->streaming_quality }} {{ __tr('Quality') }}
        </li>

        {{-- Catch-up --}}
        <li class="listItem {{ $plan->catchup_days > 0 ? 'check' : '' }}">
            @if ($plan->catchup_days > 0)
                <div class="check-icon me-2">{!! $checkSvg !!}</div>
                {{ $plan->catchup_days }} {{ __tr('Days Catch-up') }}
            @else
                <div class="me-2">{!! $crossSvg !!}</div>
                {{ __tr('No Catch-up') }}
            @endif
        </li>

        {{-- DVR --}}
        <li class="listItem {{ $plan->dvr_enabled ? 'check' : '' }}">
            @if ($plan->dvr_enabled)
                <div class="check-icon me-2">{!! $checkSvg !!}</div>
            @else
                <div class="me-2">{!! $crossSvg !!}</div>
            @endif
            {{ __tr('DVR Recording') }}
        </li>
    </ul>
</div>
