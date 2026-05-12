@extends('backend.layouts.dashboard_layout')
@section('page-content')
    @php $editing = !is_null($item); @endphp
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $editing ? __tr('Edit Featured Content') : __tr('Add Featured Content') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __tr('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.featured-content.index') }}">{{ __tr('Featured Content') }}</a></li>
                        <li class="breadcrumb-item active">{{ $editing ? __tr('Edit') : __tr('Add') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $editing ? __tr('Edit Content') : __tr('New Content') }}</h3>
                        </div>
                        <form action="{{ $editing ? route('admin.featured-content.update', $item) : route('admin.featured-content.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($editing) @method('PUT') @endif

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>{{ __tr('Title') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                                value="{{ old('title', $item?->title) }}" required>
                                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Type') }} <span class="text-danger">*</span></label>
                                            <select name="type" class="form-control">
                                                @foreach(['movie' => 'Movie', 'series' => 'Series', 'sports_event' => 'Sports Event', 'new_release' => 'New Release'] as $val => $label)
                                                    <option value="{{ $val }}" {{ old('type', $item?->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>{{ __tr('Subtitle / Tagline') }}</label>
                                            <input type="text" name="subtitle" class="form-control"
                                                value="{{ old('subtitle', $item?->subtitle) }}"
                                                placeholder="{{ __tr('Short description or tagline') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Genre') }}</label>
                                            <input type="text" name="genre" class="form-control"
                                                value="{{ old('genre', $item?->genre) }}"
                                                placeholder="{{ __tr('e.g. Action, Drama, Boxing') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Thumbnail Image') }}</label>
                                            @if($editing && $item->thumbnail)
                                                <div class="mb-2">
                                                    <img src="{{ asset($item->thumbnail) }}" style="height:80px;object-fit:cover;border-radius:4px;" alt="">
                                                </div>
                                            @endif
                                            <input type="file" name="thumbnail" class="form-control-file @error('thumbnail') is-invalid @enderror" accept="image/*">
                                            @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="text-muted">{{ __tr('Max 2MB. JPG, PNG, WebP recommended.') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Trailer / Preview URL') }}</label>
                                            <input type="text" name="trailer_url" class="form-control"
                                                value="{{ old('trailer_url', $item?->trailer_url) }}"
                                                placeholder="{{ __tr('YouTube URL or 11-char video ID') }}">
                                            <small class="text-muted">{{ __tr('e.g. https://youtube.com/watch?v=dQw4w9WgXcQ or just: dQw4w9WgXcQ') }}</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Event Date') }}</label>
                                            <input type="date" name="event_date" class="form-control"
                                                value="{{ old('event_date', $item?->event_date?->format('Y-m-d')) }}">
                                            <small class="text-muted">{{ __tr('For sports events / premiere dates') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Badge Text') }}</label>
                                            <input type="text" name="badge_text" class="form-control"
                                                value="{{ old('badge_text', $item?->badge_text) }}"
                                                placeholder="{{ __tr('e.g. NEW, LIVE, 4K, HD') }}" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Sort Order') }}</label>
                                            <input type="number" name="sort_order" class="form-control"
                                                value="{{ old('sort_order', $item?->sort_order ?? 0) }}" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active_fc"
                                            {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active_fc">{{ __tr('Active (visible on site)') }}</label>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ $editing ? __tr('Update Content') : __tr('Add Content') }}
                                </button>
                                <a href="{{ route('admin.featured-content.index') }}" class="btn btn-secondary ml-2">
                                    {{ __tr('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
