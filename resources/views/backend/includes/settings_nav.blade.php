@php
    $settingsNav = config('settings_sidebar', []);

    $navIsActive = function (array $item) use (&$navIsActive): bool {
        $patterns = (array) ($item['active_routes'] ?? ($item['active_route'] ?? []));
        if (empty($patterns)) {
            return false;
        }
        return request()->routeIs(...$patterns);
    };
@endphp

{{-- Sidebar heading + mobile close button --}}
<div class="settings-nav-heading">
    <span>Settings</span>
    <button class="settings-sidebar-close" onclick="closeSettingsSidebar()" title="Close menu">
        <i class="fas fa-times"></i>
    </button>
</div>

<nav class="settings-nav-vertical">
    @foreach ($settingsNav as $item)
        @php
            $permitted = empty($item['permission']) || (auth()->check() && auth()->user()->can($item['permission']));
        @endphp

        @if (!$permitted)
            @continue
        @endif

        @if ($item['type'] === 'section')
            <div class="settings-nav-section">{{ $item['label'] }}</div>
        @elseif ($item['type'] === 'link')
            @php $active = $navIsActive($item); @endphp
            <a href="{{ route($item['route']) }}" class="settings-nav-link {{ $active ? 'active' : '' }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @elseif ($item['type'] === 'group')
            @php
                $groupPatterns = (array) ($item['active_routes'] ?? []);
                $groupOpen = !empty($groupPatterns) && request()->routeIs(...$groupPatterns);
            @endphp
            <button type="button"
                class="settings-nav-link settings-nav-group-btn {{ $groupOpen ? 'active open' : '' }}"
                onclick="this.classList.toggle('open'); this.nextElementSibling.classList.toggle('open')">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
                <i class="fas fa-chevron-down settings-nav-chevron"></i>
            </button>

            <div class="settings-nav-submenu {{ $groupOpen ? 'open' : '' }}">
                @foreach ($item['children'] as $child)
                    @php
                        $childPermitted =
                            empty($child['permission']) ||
                            (auth()->check() && auth()->user()->can($child['permission']));
                        $childActive = $navIsActive($child);
                    @endphp
                    @if ($childPermitted)
                        <a href="{{ route($child['route']) }}"
                            class="settings-nav-link settings-nav-child {{ $childActive ? 'active' : '' }}">
                            @if (!empty($child['icon']))
                                <i class="{{ $child['icon'] }}"></i>
                            @else
                                <span class="settings-nav-dot"></span>
                            @endif
                            <span>{{ $child['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    @endforeach
</nav>
