@php
    $links = [['title' => __tr('Movies & TV Shows'), 'route' => '', 'active' => true]];
    $typeColors = ['movie' => 'danger', 'tv_show' => 'primary'];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Movies & TV Shows') }}
@endsection
@section('page-style')
    <style>
        .mc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 22px;
        }

        .mc-card {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
            transition: box-shadow .25s, transform .25s;
            display: flex;
            flex-direction: column;
        }

        .mc-card:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, .13);
            transform: translateY(-3px);
        }

        .mc-thumb {
            position: relative;
            height: 170px;
            background: #0f172a;
            overflow: hidden;
            flex-shrink: 0;
        }

        .mc-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .mc-card:hover .mc-thumb img {
            transform: scale(1.04);
        }

        .mc-thumb-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, .6) 0%, transparent 55%);
            pointer-events: none;
        }

        .mc-thumb-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: rgba(255, 255, 255, .18);
            font-size: 2.6rem;
        }

        .mc-thumb-badges {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .mc-rating-chip {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, .65);
            color: #fbbf24;
            font-size: .72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .mc-home-chip {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(99, 91, 255, .75);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            letter-spacing: .03em;
        }

        .mc-card-body {
            padding: 14px 16px 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mc-title {
            font-weight: 700;
            font-size: .92rem;
            color: #111827;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mc-subtitle {
            font-size: .75rem;
            color: #9ca3af;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mc-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 6px;
        }

        .mc-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: .7rem;
            color: #374151;
            font-weight: 500;
        }

        .mc-chip i {
            color: #9ca3af;
            font-size: .62rem;
        }

        .mc-card-footer {
            padding: 10px 12px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 8px;
        }

        .mc-btn-edit {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: .76rem;
            font-weight: 600;
            padding: 6px 0;
            border-radius: 8px;
            border: none;
            background: #fef9ec;
            color: #b45309;
            text-decoration: none;
            transition: background .2s;
        }

        .mc-btn-edit:hover {
            background: #fde68a;
            color: #92400e;
            text-decoration: none;
        }

        .mc-btn-delete {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: .76rem;
            font-weight: 600;
            padding: 6px 0;
            border-radius: 8px;
            border: none;
            background: #fef2f2;
            color: #b91c1c;
            cursor: pointer;
            width: 100%;
            transition: background .2s;
        }

        .mc-btn-delete:hover {
            background: #fecaca;
            color: #991b1b;
        }

        .mc-empty {
            text-align: center;
            padding: 70px 20px;
            color: #9ca3af;
        }

        .mc-empty i {
            font-size: 3rem;
            margin-bottom: 14px;
            display: block;
            opacity: .4;
        }

        .mc-empty h5 {
            color: #6b7280;
            margin-bottom: 6px;
        }

        .mc-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .mc-stat-pill {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: .8rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .mc-stat-pill .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mc-filter-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .mc-filter-tab {
            padding: 5px 16px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-size: .78rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all .2s;
        }

        .mc-filter-tab.active,
        .mc-filter-tab:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="{{ __tr('Movies & TV Shows') }}" :links="$links" />

    <section class="content">
        <div class="container-fluid">

            {{-- Toolbar --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
                <div class="mc-stats">
                    <span class="mc-stat-pill">
                        <span class="dot" style="background:#635bff;"></span>
                        {{ $items->count() }} {{ __tr('total') }}
                    </span>
                    <span class="mc-stat-pill">
                        <span class="dot" style="background:#dc2626;"></span>
                        {{ $items->where('type', 'movie')->count() }} {{ __tr('movies') }}
                    </span>
                    <span class="mc-stat-pill">
                        <span class="dot" style="background:#2563eb;"></span>
                        {{ $items->where('type', 'tv_show')->count() }} {{ __tr('TV shows') }}
                    </span>
                    <span class="mc-stat-pill">
                        <span class="dot" style="background:#16a34a;"></span>
                        {{ $items->where('is_active', true)->count() }} {{ __tr('active') }}
                    </span>
                    <span class="mc-stat-pill">
                        <span class="dot" style="background:#9ca3af;"></span>
                        {{ $items->where('featured_on_home', true)->count() }} {{ __tr('on home') }}
                    </span>
                </div>
                <a href="{{ route('admin.media-content.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __tr('Add Movie / Show') }}
                </a>
            </div>

            @if ($items->isEmpty())
                <div class="card">
                    <div class="card-body mc-empty">
                        <i class="fas fa-film"></i>
                        <h5>{{ __tr('No movies or TV shows yet') }}</h5>
                        <p>{{ __tr('Click "Add Movie / Show" to get started.') }}</p>
                        <a href="{{ route('admin.media-content.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> {{ __tr('Add Movie / Show') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="mc-grid">
                    @foreach ($items as $item)
                        <div class="mc-card">

                            {{-- Thumbnail --}}
                            <div class="mc-thumb">
                                @if ($item->thumbnail)
                                    <img src="{{ asset(getFilePath($item->thumbnail, true)) }}" alt="{{ $item->title }}">
                                @else
                                    <div class="mc-thumb-placeholder">
                                        <i class="fas {{ $item->type_icon }}"></i>
                                    </div>
                                @endif

                                <div class="mc-thumb-overlay"></div>

                                <div class="mc-thumb-badges">
                                    <span class="badge badge-{{ $typeColors[$item->type] ?? 'secondary' }}"
                                        style="font-size:.66rem;">
                                        {{ $item->type_label }}
                                    </span>
                                    <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}"
                                        style="font-size:.66rem;">
                                        {{ $item->is_active ? __tr('Active') : __tr('Hidden') }}
                                    </span>
                                </div>

                                @if ($item->rating)
                                    <span class="mc-rating-chip">⭐ {{ number_format($item->rating, 1) }}</span>
                                @endif

                                @if ($item->featured_on_home)
                                    <span class="mc-home-chip">🏠 {{ __tr('On Home') }}</span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="mc-card-body">
                                <p class="mc-title" title="{{ $item->title }}">{{ $item->title }}</p>
                                @if ($item->subtitle)
                                    <p class="mc-subtitle">{{ $item->subtitle }}</p>
                                @endif
                                <div class="mc-meta">
                                    @if ($item->release_year)
                                        <span class="mc-chip"><i class="fas fa-calendar"></i>
                                            {{ $item->release_year }}</span>
                                    @endif
                                    @if ($item->genre)
                                        <span class="mc-chip"><i class="fas fa-tag"></i>
                                            {{ Str::limit($item->genre, 20) }}</span>
                                    @endif
                                    @if ($item->type === 'tv_show' && $item->seasons)
                                        <span class="mc-chip"><i class="fas fa-layer-group"></i> {{ $item->seasons }}
                                            {{ __tr('S') }}</span>
                                    @endif
                                    @if ($item->youtube_embed_id)
                                        <span class="mc-chip"><i class="fab fa-youtube" style="color:#dc2626;"></i>
                                            {{ __tr('Trailer') }}</span>
                                    @endif
                                    @if ($item->badge_text)
                                        <span class="badge badge-dark"
                                            style="font-size:.66rem;">{{ $item->badge_text }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mc-card-footer">
                                <a href="{{ route('admin.media-content.edit', $item) }}" class="mc-btn-edit">
                                    <i class="fas fa-pen"></i> {{ __tr('Edit') }}
                                </a>
                                <form action="{{ route('admin.media-content.destroy', $item) }}" method="POST"
                                    style="flex:1;display:flex;"
                                    onsubmit="return confirm('{{ __tr('Delete this item?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="mc-btn-delete">
                                        <i class="fas fa-trash"></i> {{ __tr('Delete') }}
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>
@endsection
