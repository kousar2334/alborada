@php
    $links = [['title' => __tr('Featured Content'), 'route' => '', 'active' => true]];
    $typeColors = ['movie' => 'danger', 'series' => 'info', 'sports_event' => 'warning', 'new_release' => 'primary'];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Featured Content') }}
@endsection
@section('page-style')
    <style>
        .fc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 22px;
        }

        /* ── Card shell ── */
        .fc-card {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
            transition: box-shadow .25s, transform .25s;
            display: flex;
            flex-direction: column;
        }

        .fc-card:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, .13);
            transform: translateY(-3px);
        }

        /* ── Thumbnail ── */
        .fc-thumb {
            position: relative;
            height: 165px;
            background: #0f172a;
            overflow: hidden;
            flex-shrink: 0;
        }

        .fc-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .fc-card:hover .fc-thumb img {
            transform: scale(1.04);
        }

        .fc-thumb-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, .55) 0%, transparent 55%);
            pointer-events: none;
        }

        .fc-thumb-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: rgba(255, 255, 255, .18);
            font-size: 2.4rem;
        }

        /* badges sit in top corners */
        .fc-thumb-badges {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            pointer-events: none;
        }

        .fc-badge-type {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
        }

        .fc-badge-status {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 3px 9px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
        }

        /* order chip sits bottom-left, over the gradient */
        .fc-thumb-order {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0, 0, 0, .55);
            color: rgba(255, 255, 255, .85);
            font-size: .68rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            letter-spacing: .03em;
        }

        /* ── Body ── */
        .fc-card-body {
            padding: 14px 16px 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .fc-title {
            font-weight: 700;
            font-size: .92rem;
            color: #111827;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }

        .fc-subtitle {
            font-size: .76rem;
            color: #9ca3af;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Meta row ── */
        .fc-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .fc-meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: .71rem;
            color: #374151;
            font-weight: 500;
        }

        .fc-meta-chip i {
            color: #9ca3af;
            font-size: .65rem;
        }

        .fc-badge-label {
            display: inline-flex;
            align-items: center;
            background: #1e293b;
            color: #f8fafc;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .03em;
        }

        /* ── Footer actions ── */
        .fc-card-footer {
            padding: 10px 12px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 8px;
        }

        .fc-btn-edit {
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
            transition: background .2s, color .2s;
        }

        .fc-btn-edit:hover {
            background: #fde68a;
            color: #92400e;
            text-decoration: none;
        }

        .fc-btn-delete {
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
            transition: background .2s, color .2s;
        }

        .fc-btn-delete:hover {
            background: #fecaca;
            color: #991b1b;
        }

        /* ── Empty state ── */
        .fc-empty {
            text-align: center;
            padding: 70px 20px;
            color: #9ca3af;
        }

        .fc-empty i {
            font-size: 3rem;
            margin-bottom: 14px;
            display: block;
            opacity: .4;
        }

        .fc-empty h5 {
            color: #6b7280;
            margin-bottom: 6px;
        }

        /* ── Stat pills ── */
        .fc-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .fc-stat-pill {
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

        .fc-stat-pill .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="{{ __tr('Featured Content') }}" :links="$links" />

    <section class="content">
        <div class="container-fluid">

            {{-- Toolbar --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
                <div class="fc-stats">
                    <span class="fc-stat-pill">
                        <span class="dot" style="background:#635bff;"></span>
                        {{ $items->count() }} {{ __tr('total') }}
                    </span>
                    <span class="fc-stat-pill">
                        <span class="dot" style="background:#16a34a;"></span>
                        {{ $items->where('is_active', true)->count() }} {{ __tr('live') }}
                    </span>
                    <span class="fc-stat-pill">
                        <span class="dot" style="background:#9ca3af;"></span>
                        {{ $items->where('is_active', false)->count() }} {{ __tr('hidden') }}
                    </span>
                </div>
                <a href="{{ route('admin.featured-content.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __tr('Add Content') }}
                </a>
            </div>

            @if ($items->isEmpty())
                <div class="card">
                    <div class="card-body fc-empty">
                        <i class="fas fa-film"></i>
                        <h5>{{ __tr('No featured content yet') }}</h5>
                        <p>{{ __tr('Click "Add Content" to get started.') }}</p>
                        <a href="{{ route('admin.featured-content.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> {{ __tr('Add Content') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="fc-grid">
                    @foreach ($items as $item)
                        <div class="fc-card">

                            {{-- Thumbnail --}}
                            <div class="fc-thumb">
                                @if ($item->thumbnail)
                                    <img src="{{ asset(getFilePath($item->thumbnail, true)) }}" alt="{{ $item->title }}">
                                @else
                                    <div class="fc-thumb-placeholder">
                                        <i class="fas {{ $item->type_icon ?? 'fa-film' }}"></i>
                                    </div>
                                @endif

                                <div class="fc-thumb-overlay"></div>

                                <div class="fc-thumb-badges">
                                    <span class="fc-badge-type badge badge-{{ $typeColors[$item->type] ?? 'secondary' }}">
                                        {{ $item->type_label }}
                                    </span>
                                    <span
                                        class="fc-badge-status badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                        {{ $item->is_active ? __tr('Live') : __tr('Hidden') }}
                                    </span>
                                </div>

                                <span class="fc-thumb-order">#{{ $item->sort_order }}</span>
                            </div>

                            {{-- Body --}}
                            <div class="fc-card-body">
                                <p class="fc-title" title="{{ $item->title }}">{{ $item->title }}</p>
                                @if ($item->subtitle)
                                    <p class="fc-subtitle" title="{{ $item->subtitle }}">{{ $item->subtitle }}</p>
                                @endif
                                <div class="fc-meta">
                                    @if ($item->badge_text)
                                        <span class="fc-badge-label">{{ $item->badge_text }}</span>
                                    @endif
                                    @if ($item->event_date)
                                        <span class="fc-meta-chip">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $item->event_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                    @if ($item->genre)
                                        <span class="fc-meta-chip">
                                            <i class="fas fa-tag"></i>
                                            {{ $item->genre }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="fc-card-footer">
                                <a href="{{ route('admin.featured-content.edit', $item) }}" class="fc-btn-edit">
                                    <i class="fas fa-pen"></i> {{ __tr('Edit') }}
                                </a>
                                <form action="{{ route('admin.featured-content.destroy', $item) }}" method="POST"
                                    style="flex:1;display:flex;"
                                    onsubmit="return confirm('{{ __tr('Delete this item?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="fc-btn-delete">
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
