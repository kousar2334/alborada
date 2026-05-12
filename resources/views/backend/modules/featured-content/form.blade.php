@php
    $editing = !is_null($item);
    $links = [
        ['title' => __tr('Featured Content'), 'route' => 'admin.featured-content.index', 'active' => false],
        ['title' => $editing ? __tr('Edit') : __tr('Add'), 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ $editing ? __tr('Edit Featured Content') : __tr('Add Featured Content') }}
@endsection
@section('page-content')
    <x-admin-page-header title="{{ $editing ? __tr('Edit Featured Content') : __tr('Add Featured Content') }}"
        :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <form
                action="{{ $editing ? route('admin.featured-content.update', $item) : route('admin.featured-content.store') }}"
                method="POST">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="row">

                    {{-- Left: main fields --}}
                    <div class="col-lg-8 mb-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-film mr-2"></i>
                                    {{ __tr('Content Details') }}
                                </h3>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>{{ __tr('Title') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                value="{{ old('title', $item?->title) }}"
                                                placeholder="{{ __tr('e.g. Avengers: Endgame') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Type') }} <span class="text-danger">*</span></label>
                                            <select name="type" class="form-control">
                                                @foreach (['movie' => 'Movie', 'series' => 'Series', 'sports_event' => 'Sports Event', 'new_release' => 'New Release'] as $val => $label)
                                                    <option value="{{ $val }}"
                                                        {{ old('type', $item?->type) === $val ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
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
                                                placeholder="{{ __tr('e.g. Action, Drama') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Trailer / Preview URL') }}</label>
                                    <input type="text" name="trailer_url" class="form-control"
                                        value="{{ old('trailer_url', $item?->trailer_url) }}"
                                        placeholder="{{ __tr('YouTube URL or 11-char video ID') }}">
                                    <small class="form-text text-muted">
                                        {{ __tr('e.g. https://youtube.com/watch?v=dQw4w9WgXcQ or just: dQw4w9WgXcQ') }}
                                    </small>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Event Date') }}</label>
                                            <input type="date" name="event_date" class="form-control"
                                                value="{{ old('event_date', $item?->event_date?->format('Y-m-d')) }}">
                                            <small
                                                class="form-text text-muted">{{ __tr('For sports events / premieres') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Badge Text') }}</label>
                                            <input type="text" name="badge_text" class="form-control"
                                                value="{{ old('badge_text', $item?->badge_text) }}"
                                                placeholder="{{ __tr('e.g. NEW, LIVE, 4K') }}" maxlength="20">
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

                                <div class="form-group mb-0">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" value="1" class="custom-control-input"
                                            id="is_active_fc"
                                            {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active_fc">
                                            {{ __tr('Active (visible on site)') }}
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    {{ $editing ? __tr('Update Content') : __tr('Add Content') }}
                                </button>
                                <a href="{{ route('admin.featured-content.index') }}" class="btn btn-secondary ml-2">
                                    {{ __tr('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Right: thumbnail --}}
                    <div class="col-lg-4 mb-4">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-image mr-2"></i>
                                    {{ __tr('Thumbnail Image') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <x-media name="thumbnail" :value="old('thumbnail', $item?->thumbnail)" width="100">
                                </x-media>
                                @error('thumbnail')
                                    <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    {{ __tr('Recommended: 16:9 ratio. JPG or PNG.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>
@endsection
