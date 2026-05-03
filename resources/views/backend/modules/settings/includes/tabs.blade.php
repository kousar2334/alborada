<div class="nav flex-column border-right2 py-3" aria-orientation="vertical">
    {{-- @if (auth()->user()->can('Manage General Settings'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.general']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.general') }}">
            <i class="icofont-ui-settings" title="{{ __tr('General') }}"></i>
            <span>{{ __tr('General') }}</span>
        </a>
    @endif --}}
    @if (auth()->user()->can('Manage Member Settings'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.member']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.member') }}">
            <i class="icofont-users-alt-1" title="{{ __tr('Member settings') }}"></i>
            <span>{{ __tr('Member settings') }}</span>
        </a>
    @endif

    @if (auth()->user()->can('Manage Currency Settings'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.currency']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.currency') }}">
            <i class="icofont-money" title="{{ __tr('Currency Settings') }}"></i>
            <span>{{ __tr('Currency Settings') }}</span>
        </a>
    @endif

    @if (auth()->user()->can('Manage Ads Settings'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.ads']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.ads') }}">
            <i class="icofont-horn" title="{{ __tr('Ads Settings') }}"></i>
            <span>{{ __tr('Ads Settings') }}</span>
        </a>
    @endif
    @if (auth()->user()->can('Manage Safety Tips'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.safety.tips.list']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.safety.tips.list') }}">
            <i class="icofont-safety" title="{{ __tr('Safety Tips') }}"></i>
            <span>{{ __tr('Safety Tips') }}</span>
        </a>
    @endif
    @if (auth()->user()->can('Manage Quick Sell Tips'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.quick.sell.tips.list']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.quick.sell.tips.list') }}">
            <i class="icofont-sale-discount" title="{{ __tr('Quick Sell Tips') }}"></i>
            <span>{{ __tr('Quick Sell Tips') }}</span>
        </a>
    @endif
    @if (auth()->user()->can('Manage Ad Share Options'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.share.options.list']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.share.options.list') }}">
            <i class="icofont-share-alt" title="{{ __tr('Share Options') }}"></i>
            <span>{{ __tr('Share Options') }}</span>
        </a>
    @endif
    @if (auth()->user()->can('Manage Map Settings'))
        <a class="nav-link {{ Request::routeIs(['classified.settings.map']) ? 'active ' : '' }}"
            href="{{ route('classified.settings.map') }}">
            <i class="icofont-google-map" title="{{ __tr('Map settings') }}"></i>
            <span>{{ __tr('Map settings') }}</span>
        </a>
    @endif
</div>
