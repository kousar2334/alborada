@props(['categories'])

@if ($categories->count() > 0)
    <section class="cat-grid-section">
        <div class="container">
            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                <h2 class="head3">{{ p_trans('home_categories_title', null, 'Browse Categories') }}</h2>
            </div>

            <div class="cat-grid">
                @foreach ($categories as $category)
                    <a href="{{ route('ad.listing.page', $category->permalink) }}"
                        class="cat-grid-card text-decoration-none">
                        <div class="cat-grid-icon-img">
                            <img src="{{ asset(getFilePath($category->icon)) }}"
                                alt="{{ $category->translation('title') }}" />
                        </div>
                        <div class="cat-grid-body">
                            <div class="cat-grid-name">{{ $category->translation('title') }}</div>
                            <div class="cat-grid-count">
                                {{ number_format($category->ads_count) }} {{ __tr('ads') ?? 'ads' }}
                            </div>
                        </div>
                        <span class="cat-grid-arrow"><i class="las la-arrow-right"></i></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
