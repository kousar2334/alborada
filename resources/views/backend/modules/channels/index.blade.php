@php
    $links = [['title' => __tr('Channels'), 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Channels') }}
@endsection
@section('page-style')
    <style>
        .ch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .ch-card {
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            transition: box-shadow .25s, transform .25s;
            display: flex;
            flex-direction: column;
        }

        .ch-card:hover {
            box-shadow: 0 10px 28px rgba(0,0,0,.12);
            transform: translateY(-3px);
        }

        .ch-logo-wrap {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .ch-logo-wrap img {
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
        }

        .ch-logo-placeholder {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: .04em;
            color: rgba(255,255,255,.7);
        }

        .ch-card-body {
            padding: 12px 14px 8px;
            flex: 1;
        }

        .ch-name {
            font-weight: 700;
            font-size: .9rem;
            color: #111827;
            margin: 0 0 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ch-card-footer {
            padding: 8px 10px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 6px;
        }

        .ch-btn-edit {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: .74rem;
            font-weight: 600;
            padding: 6px 0;
            border-radius: 8px;
            border: none;
            background: #fef9ec;
            color: #b45309;
            text-decoration: none;
            transition: background .2s, color .2s;
        }

        .ch-btn-edit:hover {
            background: #fde68a;
            color: #92400e;
            text-decoration: none;
        }

        .ch-btn-delete {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: .74rem;
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

        .ch-btn-delete:hover {
            background: #fecaca;
            color: #991b1b;
        }

        .ch-empty {
            text-align: center;
            padding: 70px 20px;
            color: #9ca3af;
        }

        .ch-empty i {
            font-size: 3rem;
            margin-bottom: 14px;
            display: block;
            opacity: .4;
        }

        .ch-empty h5 {
            color: #6b7280;
            margin-bottom: 6px;
        }

        .ch-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .ch-stat-pill {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: .8rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }

        .ch-stat-pill .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="{{ __tr('Channels') }}" :links="$links" />

    <section class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
                <div class="ch-stats">
                    <span class="ch-stat-pill">
                        <span class="dot" style="background:#635bff;"></span>
                        {{ $channels->count() }} {{ __tr('total') }}
                    </span>
                    <span class="ch-stat-pill">
                        <span class="dot" style="background:#16a34a;"></span>
                        {{ $channels->where('status', true)->count() }} {{ __tr('active') }}
                    </span>
                    <span class="ch-stat-pill">
                        <span class="dot" style="background:#9ca3af;"></span>
                        {{ $channels->where('status', false)->count() }} {{ __tr('inactive') }}
                    </span>
                </div>
                <a href="{{ route('admin.channels.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __tr('Add Channel') }}
                </a>
            </div>

            @if ($channels->isEmpty())
                <div class="card">
                    <div class="card-body ch-empty">
                        <i class="fas fa-tv"></i>
                        <h5>{{ __tr('No channels yet') }}</h5>
                        <p>{{ __tr('Click "Add Channel" to get started.') }}</p>
                        <a href="{{ route('admin.channels.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> {{ __tr('Add Channel') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="ch-grid">
                    @foreach ($channels as $channel)
                        <div class="ch-card">
                            <div class="ch-logo-wrap" style="background:{{ $channel->bg_color }};">
                                @if ($channel->logo)
                                    <img src="{{ asset(getFilePath($channel->logo, true)) }}" alt="{{ $channel->name }}">
                                @else
                                    <div class="ch-logo-placeholder">
                                        {{ strtoupper(substr($channel->name, 0, 3)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="ch-card-body">
                                <p class="ch-name" title="{{ $channel->name }}">{{ $channel->name }}</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge badge-{{ $channel->status ? 'success' : 'secondary' }}">
                                        {{ $channel->status ? __tr('Active') : __tr('Inactive') }}
                                    </span>
                                    <small class="text-muted">#{{ $channel->sort_order }}</small>
                                </div>
                            </div>

                            <div class="ch-card-footer">
                                <a href="{{ route('admin.channels.edit', $channel) }}" class="ch-btn-edit">
                                    <i class="fas fa-pen"></i> {{ __tr('Edit') }}
                                </a>
                                <form action="{{ route('admin.channels.destroy', $channel) }}" method="POST"
                                    style="flex:1;display:flex;"
                                    onsubmit="return confirm('{{ __tr('Delete this channel?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ch-btn-delete">
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
