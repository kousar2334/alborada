@extends('backend.layouts.dashboard_layout')
@section('page-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __tr('Featured Content') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __tr('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __tr('Featured Content') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('admin.featured-content.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __tr('Add Content') }}
                    </a>
                </div>
            </div>

            @if($items->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-film fa-3x mb-3"></i>
                        <p>{{ __tr('No featured content yet. Click "Add Content" to get started.') }}</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($items as $item)
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                @if($item->thumbnail)
                                    <img src="{{ asset($item->thumbnail) }}" class="card-img-top" style="height:140px;object-fit:cover;"
                                        alt="{{ $item->title }}">
                                @else
                                    <div style="height:140px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas {{ $item->type_icon }} fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="badge badge-{{ $item->type === 'sports_event' ? 'warning' : ($item->type === 'movie' ? 'danger' : 'primary') }}">
                                            {{ $item->type_label }}
                                        </span>
                                        <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                            {{ $item->is_active ? __tr('Live') : __tr('Hidden') }}
                                        </span>
                                    </div>
                                    <h6 class="card-title mb-1" style="font-size:.85rem;">{{ Str::limit($item->title, 40) }}</h6>
                                    @if($item->badge_text)
                                        <span class="badge badge-dark">{{ $item->badge_text }}</span>
                                    @endif
                                    @if($item->event_date)
                                        <div style="font-size:.75rem;color:#6c757d;margin-top:4px;">
                                            <i class="fas fa-calendar"></i> {{ $item->event_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer p-1 d-flex gap-1">
                                    <a href="{{ route('admin.featured-content.edit', $item) }}" class="btn btn-xs btn-warning flex-fill">
                                        <i class="fas fa-edit"></i> {{ __tr('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.featured-content.destroy', $item) }}" method="POST" style="flex:1;"
                                        onsubmit="return confirm('{{ __tr('Delete this item?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger w-100">
                                            <i class="fas fa-trash"></i> {{ __tr('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>
@endsection
