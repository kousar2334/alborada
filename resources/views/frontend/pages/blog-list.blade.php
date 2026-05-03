@extends('frontend.layouts.master')

@section('meta')
    <title>{{ __tr('Blog') }} - {{ get_setting('site_name') }}</title>
    <meta name="description" content="{{ __tr('Read our latest articles, news and insights.') }}">
@endsection

@section('content')

    {{-- ══════════════════ HERO BANNER ══════════════════ --}}
    <section class="contact-breadcrumb-area">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <span class="page-tag">{{ __tr('Our Blog') }}</span>
                <h1>{{ __tr('Latest Articles') }}</h1>
                <p class="sub-text">{{ __tr('Read our latest articles, news and insights.') }}</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">{{ __tr('Home') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __tr('Blog') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    {{-- ══════════════════ END HERO ══════════════════════ --}}

    {{-- ══════════════════ BLOG LISTING ══════════════════ --}}
    <section class="blog-list-section" data-padding-top="60" data-padding-bottom="80">
        <div class="container">

            {{-- Search / filter bar --}}
            <div class="row mb-4">
                <div class="col-lg-6 offset-lg-3">
                    <form action="{{ route('frontend.blog.list') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="input-style" value="{{ request('search') }}"
                            placeholder="{{ __tr('Search articles...') }}">
                        <button type="submit" class="cmn-btn">
                            <i class="las la-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('frontend.blog.list') }}" class="cmn-btn cmn-btn-outline">
                                <i class="las la-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($blogs as $blog)
                    <div class="col-xl-4 col-md-6">
                        <div class="blog-card h-100">

                            {{-- Thumbnail --}}
                            <a href="{{ route('frontend.new.details', ['permalink' => $blog->permalink]) }}"
                                class="blog-card-img-wrap d-block overflow-hidden">
                                <img src="{{ getFilePath($blog->thumbnail) }}" alt="{{ $blog->translation('title') }}"
                                    class="img-fluid blog-card-img">
                            </a>

                            <div class="blog-card-body">

                                {{-- Categories --}}
                                @if ($blog->categories->count())
                                    <div class="blog-card-cats mb-2">
                                        @foreach ($blog->categories->take(2) as $cat)
                                            <a href="{{ route('frontend.blog.list', ['category' => $cat->slug]) }}"
                                                class="blog-cat-badge">
                                                {{ $cat->translation('title') }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Title --}}
                                <h3 class="blog-card-title">
                                    <a href="{{ route('frontend.new.details', ['permalink' => $blog->permalink]) }}">
                                        {{ $blog->translation('title') }}
                                    </a>
                                </h3>

                                {{-- Short description --}}
                                @if ($blog->translation('short_description'))
                                    <p class="blog-card-desc pera">
                                        {{ Str::limit($blog->translation('short_description'), 120) }}
                                    </p>
                                @endif

                                {{-- Meta --}}
                                <div class="blog-card-meta d-flex align-items-center gap-3 mt-3">
                                    @if ($blog->authorInfo)
                                        <span class="blog-meta-item">
                                            <i class="las la-user-circle"></i>
                                            {{ $blog->authorInfo->name }}
                                        </span>
                                    @endif
                                    <span class="blog-meta-item">
                                        <i class="las la-calendar-alt"></i>
                                        {{ $blog->created_at->format('M d, Y') }}
                                    </span>
                                </div>

                                <a href="{{ route('frontend.new.details', ['permalink' => $blog->permalink]) }}"
                                    class="blog-read-more mt-3 d-inline-flex align-items-center gap-1">
                                    {{ __tr('Read More') }}
                                    <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="las la-newspaper" style="font-size: 3rem; color: #94a3b8;"></i>
                        <p class="pera mt-3">{{ __tr('No articles found.') }}</p>
                        @if (request('search'))
                            <a href="{{ route('frontend.blog.list') }}" class="cmn-btn mt-2">
                                {{ __tr('Clear Search') }}
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($blogs->hasPages())
                <div class="row mt-5">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $blogs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>
    </section>
    {{-- ══════════════════ END BLOG LISTING ═══════════════ --}}

@endsection
