@php
    $settingsNav = config('settings_sidebar', []);

    $navIsActive = function (array $item): bool {
        $patterns = (array) ($item['active_routes'] ?? []);
        return !empty($patterns) && request()->routeIs(...$patterns);
    };
@endphp

<nav class="sl-nav">
    @foreach ($settingsNav as $item)
        @php
            $permitted = empty($item['permission']) || (auth()->check() && auth()->user()->can($item['permission']));
        @endphp

        @if (!$permitted)
            @continue
        @endif

        @if ($item['type'] === 'link')
            @php $active = $navIsActive($item); @endphp
            <a href="{{ route($item['route']) }}" class="sl-nav-item {{ $active ? 'active' : '' }}">
                <i class="{{ $item['icon'] }} sl-nav-icon"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @elseif ($item['type'] === 'group')
            @php
                $groupPatterns = (array) ($item['active_routes'] ?? []);
                $groupOpen = !empty($groupPatterns) && request()->routeIs(...$groupPatterns);
            @endphp
            <button type="button" class="sl-nav-item sl-nav-group-btn {{ $groupOpen ? 'active open' : '' }}"
                onclick="this.classList.toggle('open'); this.nextElementSibling.classList.toggle('open')">
                <i class="{{ $item['icon'] }} sl-nav-icon"></i>
                <span>{{ $item['label'] }}</span>
                <i class="fas fa-chevron-down sl-nav-chevron"></i>
            </button>

            <div class="sl-nav-submenu {{ $groupOpen ? 'open' : '' }}">
                @foreach ($item['children'] as $child)
                    @php
                        $childPermitted =
                            empty($child['permission']) ||
                            (auth()->check() && auth()->user()->can($child['permission']));
                        $childActive = $navIsActive($child);
                    @endphp
                    @if ($childPermitted)
                        <a href="{{ route($child['route']) }}"
                            class="sl-nav-item sl-nav-child {{ $childActive ? 'active' : '' }}">
                            @if (!empty($child['icon']))
                                <i class="{{ $child['icon'] }} sl-nav-icon"></i>
                            @else
                                <span class="sl-nav-dot"></span>
                            @endif
                            <span>{{ $child['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    @endforeach
</nav>
