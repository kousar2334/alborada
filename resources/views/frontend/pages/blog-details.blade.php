@extends('frontend.layouts.master')

@section('meta')
    <title>{{ $blog->translation('title') }} - {{ get_setting('site_name') }}</title>
    @if ($blog->meta_title)
        <meta name="title" content="{{ $blog->meta_title }}">
    @endif
    @if ($blog->meta_description)
        <meta name="description" content="{{ $blog->meta_description }}">
    @endif
    @if ($blog->meta_image)
        <meta property="og:image" content="{{ getFilePath($blog->meta_image) }}">
    @endif
@endsection

@section('content')

    {{-- ══════════════════ HERO BANNER ══════════════════ --}}
    <section class="contact-breadcrumb-area">
        <div class="container">
            <div class="breadcrumb-content text-center">
                @if ($blog->categories->count())
                    <span class="page-tag">{{ $blog->categories->first()->translation('title') }}</span>
                @endif
                <h1>{{ $blog->translation('title') }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">{{ __tr('Home') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('frontend.blog.list') }}">{{ __tr('Blog') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ Str::limit($blog->translation('title'), 50) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    {{-- ══════════════════ END HERO ══════════════════════ --}}

    {{-- ══════════════════ BLOG BODY ══════════════════════ --}}
    <section class="blog-details-section" data-padding-top="60" data-padding-bottom="80">
        <div class="container">
            <div class="row g-4">

                {{-- ── Main Content ───────────────────────── --}}
                <div class="col-lg-8">

                    {{-- Featured image --}}
                    @if ($blog->featured_image)
                        <div class="blog-details-feat-img mb-4">
                            <img src="{{ getFilePath($blog->featured_image) }}" alt="{{ $blog->translation('title') }}"
                                class="img-fluid w-100 rounded">
                        </div>
                    @endif

                    {{-- Meta row --}}
                    <div class="blog-details-meta d-flex flex-wrap align-items-center gap-3 mb-4">
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
                        @if ($blog->comments->count())
                            <span class="blog-meta-item">
                                <i class="las la-comments"></i>
                                {{ $blog->comments->count() }} {{ __tr('Comments') }}
                            </span>
                        @endif
                    </div>

                    {{-- Categories & tags --}}
                    @if ($blog->categories->count() || $blog->tags->count())
                        <div class="blog-taxonomy mb-4 d-flex flex-wrap gap-2 align-items-center">
                            @foreach ($blog->categories as $cat)
                                <a href="{{ route('frontend.blog.list', ['category' => $cat->slug]) }}"
                                    class="blog-cat-badge">
                                    {{ $cat->translation('title') }}
                                </a>
                            @endforeach
                            @foreach ($blog->tags as $tag)
                                <span class="blog-tag-badge">
                                    <i class="las la-tag"></i> {{ $tag->title }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Content --}}
                    <div class="blog-details-content">
                        {!! $blog->translation('content') !!}
                    </div>

                    {{-- Video --}}
                    @if ($blog->video)
                        <div class="blog-video-wrap mt-4">
                            <a href="{{ $blog->video }}" target="_blank" rel="noopener noreferrer"
                                class="cmn-btn d-inline-flex align-items-center gap-2">
                                <i class="las la-play-circle"></i>
                                {{ __tr('Watch Video') }}
                            </a>
                        </div>
                    @endif

                    {{-- ══ COMMENTS ══════════════════════════════ --}}
                    <div class="blog-comments-section mt-5">

                        @if ($blog->comments->count())
                            <h4 class="blog-section-heading mb-4">
                                {{ $blog->comments->count() }} {{ __tr('Comments') }}
                            </h4>

                            @foreach ($blog->comments as $comment)
                                <div class="blog-comment-item d-flex gap-3 mb-4">
                                    <div class="blog-comment-avatar flex-shrink-0">
                                        @if ($comment->commentAuthor && $comment->commentAuthor->image)
                                            <img src="{{ getFilePath($comment->commentAuthor->image) }}"
                                                alt="{{ $comment->commentAuthor->name }}" class="rounded-circle">
                                        @else
                                            <div
                                                class="blog-comment-avatar-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="las la-user"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="blog-comment-body flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <strong class="blog-comment-author">
                                                {{ $comment->commentAuthor ? $comment->commentAuthor->name : $comment->guest_name }}
                                            </strong>
                                            <small class="text-muted">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="blog-comment-text mb-0">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- Comment form --}}
                        <div class="blog-comment-form mt-5">
                            <h4 class="blog-section-heading mb-4">{{ __tr('Leave a Comment') }}</h4>

                            @if (session('comment_success'))
                                <div class="contact-alert contact-alert-success mb-3">
                                    <i class="las la-check-circle"></i>
                                    <span>{{ session('comment_success') }}</span>
                                </div>
                            @endif

                            <form action="{{ route('frontend.blog.comment.store', ['permalink' => $blog->permalink]) }}"
                                method="POST">
                                @csrf

                                @guest
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="fe-label">
                                                {{ __tr('Name') }} <span class="required">*</span>
                                            </label>
                                            <input type="text" name="guest_name" class="input-style"
                                                value="{{ old('guest_name') }}" placeholder="{{ __tr('Your name') }}"
                                                required>
                                            @error('guest_name')
                                                <span class="fe-invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fe-label">{{ __tr('Email') }}</label>
                                            <input type="email" name="guest_email" class="input-style"
                                                value="{{ old('guest_email') }}"
                                                placeholder="{{ __tr('Your email (optional)') }}">
                                        </div>
                                    </div>
                                @endguest

                                <div class="mb-3">
                                    <label class="fe-label">
                                        {{ __tr('Comment') }} <span class="required">*</span>
                                    </label>
                                    <textarea name="comment" rows="5" class="input-style" placeholder="{{ __tr('Write your comment here...') }}"
                                        required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <span class="fe-invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="cmn-btn">
                                    <i class="las la-paper-plane"></i>
                                    {{ __tr('Post Comment') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- ══ END COMMENTS ══════════════════════════ --}}

                </div>
                {{-- ── END Main Content ───────────────────── --}}

                {{-- ── Sidebar ─────────────────────────────── --}}
                <div class="col-lg-4">

                    {{-- Search --}}
                    <div class="blog-sidebar-widget mb-4">
                        <h5 class="blog-sidebar-title">{{ __tr('Search') }}</h5>
                        <form action="{{ route('frontend.blog.list') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="input-style"
                                placeholder="{{ __tr('Search articles...') }}">
                            <button type="submit" class="cmn-btn px-3">
                                <i class="las la-search"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Recent posts --}}
                    @if ($recentBlogs->count())
                        <div class="blog-sidebar-widget mb-4">
                            <h5 class="blog-sidebar-title">{{ __tr('Recent Posts') }}</h5>
                            @foreach ($recentBlogs as $recent)
                                <div class="blog-recent-item d-flex gap-3 mb-3">
                                    <a href="{{ route('frontend.new.details', ['permalink' => $recent->permalink]) }}"
                                        class="blog-recent-thumb flex-shrink-0">
                                        <img src="{{ getFilePath($recent->thumbnail) }}"
                                            alt="{{ $recent->translation('title') }}" class="img-fluid rounded">
                                    </a>
                                    <div>
                                        <a href="{{ route('frontend.new.details', ['permalink' => $recent->permalink]) }}"
                                            class="blog-recent-title">
                                            {{ Str::limit($recent->translation('title'), 55) }}
                                        </a>
                                        <small class="text-muted d-block mt-1">
                                            {{ $recent->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Tags --}}
                    @if ($blog->tags->count())
                        <div class="blog-sidebar-widget mb-4">
                            <h5 class="blog-sidebar-title">{{ __tr('Tags') }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($blog->tags as $tag)
                                    <span class="blog-tag-badge">
                                        <i class="las la-tag"></i> {{ $tag->title }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
                {{-- ── END Sidebar ─────────────────────────── --}}

            </div>
        </div>
    </section>
    {{-- ══════════════════ END BLOG BODY ══════════════════ --}}

@endsection
