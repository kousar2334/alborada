@php $isSidebar = isset($position) && $position === 'details_sidebar'; @endphp
@if (isset($advertisements) && $advertisements->count() > 0)
    <div class="ad-slot-wrapper ad-slot-{{ $position }}"
        style="{{ $isSidebar ? 'margin-bottom:16px;' : 'padding:20px 0;' }}">
        @if (!$isSidebar)
            <div class="container">
        @endif
        @foreach ($advertisements as $ad)
            @if ($ad->type === 'image' && $ad->image_path)
                <div class="text-center mb-2" data-ad-id="{{ $ad->id }}" data-ad-track="impression">
                    @if ($ad->click_url)
                        <a href="{{ $ad->click_url }}" target="_blank" rel="noopener sponsored"
                            data-ad-id="{{ $ad->id }}" data-ad-track="click">
                            <img src="{{ asset(getFilePath($ad->image_path, false)) }}" alt="{{ $ad->title }}"
                                class="ad-slot-img">
                        </a>
                    @else
                        <img src="{{ asset(getFilePath($ad->image_path, false)) }}" alt="{{ $ad->title }}"
                            class="ad-slot-img">
                    @endif
                </div>
            @elseif ($ad->type === 'html' && $ad->html_code)
                <div class="{{ $isSidebar ? '' : 'text-center' }} mb-2 ad-html-block" data-ad-id="{{ $ad->id }}"
                    data-ad-track="impression">
                    {!! $ad->html_code !!}
                </div>
            @endif
        @endforeach
        @if (!$isSidebar)
    </div>
@endif
</div>
@endif
