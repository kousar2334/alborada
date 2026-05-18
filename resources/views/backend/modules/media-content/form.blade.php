@php
    $editing = !is_null($item);
    $links = [
        ['title' => __tr('Movies & TV Shows'), 'route' => 'admin.media-content.index', 'active' => false],
        ['title' => $editing ? __tr('Edit') : __tr('Add'), 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ $editing ? __tr('Edit Movie / TV Show') : __tr('Add Movie / TV Show') }}
@endsection
@section('page-style')
    <style>
        .trailer-preview {
            margin-top: 10px;
            border-radius: 10px;
            overflow: hidden;
            display: none;
        }

        .trailer-preview iframe {
            width: 100%;
            height: 200px;
            border: none;
        }

        #tvShowFields {
            display: none;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="{{ $editing ? __tr('Edit Movie / TV Show') : __tr('Add Movie / TV Show') }}"
        :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <form action="{{ $editing ? route('admin.media-content.update', $item) : route('admin.media-content.store') }}"
                method="POST">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="row">

                    {{-- ── LEFT: main fields ── --}}
                    <div class="col-lg-8 mb-4">

                        {{-- Basic info --}}
                        <div class="card card-outline card-primary mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-film mr-2"></i>{{ __tr('Basic Information') }}
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
                                                placeholder="{{ __tr('e.g. Breaking Bad') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Type') }} <span class="text-danger">*</span></label>
                                            <select name="type" id="typeSelect" class="form-control">
                                                <option value="movie"
                                                    {{ old('type', $item?->type) === 'movie' ? 'selected' : '' }}>🎬
                                                    {{ __tr('Movie') }}</option>
                                                <option value="tv_show"
                                                    {{ old('type', $item?->type) === 'tv_show' ? 'selected' : '' }}>📺
                                                    {{ __tr('TV Show') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Subtitle / Tagline') }}</label>
                                    <input type="text" name="subtitle" class="form-control"
                                        value="{{ old('subtitle', $item?->subtitle) }}"
                                        placeholder="{{ __tr('Short tagline or one-liner') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Description') }}</label>
                                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="{{ __tr('Full plot summary or description…') }}">{{ old('description', $item?->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Genre(s)') }}</label>
                                            <input type="text" name="genre" class="form-control"
                                                value="{{ old('genre', $item?->genre) }}"
                                                placeholder="{{ __tr('e.g. Action, Thriller, Crime') }}">
                                            <small
                                                class="form-text text-muted">{{ __tr('Separate multiple genres with commas') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __tr('Release Year') }}</label>
                                            <input type="number" name="release_year" class="form-control"
                                                value="{{ old('release_year', $item?->release_year) }}" placeholder="2024"
                                                min="1900" max="2099">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __tr('IMDb Rating') }}</label>
                                            <input type="number" name="rating" class="form-control"
                                                value="{{ old('rating', $item?->rating) }}" placeholder="8.5"
                                                min="0" max="10" step="0.1">
                                            <small class="form-text text-muted">{{ __tr('0 – 10') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- TV Show specific fields --}}
                                <div id="tvShowFields">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __tr('Number of Seasons') }}</label>
                                                <input type="number" name="seasons" class="form-control"
                                                    value="{{ old('seasons', $item?->seasons) }}" placeholder="5"
                                                    min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __tr('Total Episodes') }}</label>
                                                <input type="number" name="episodes" class="form-control"
                                                    value="{{ old('episodes', $item?->episodes) }}" placeholder="62"
                                                    min="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Cast') }}</label>
                                    <input type="text" name="cast" class="form-control"
                                        value="{{ old('cast', $item?->cast) }}"
                                        placeholder="{{ __tr('e.g. Bryan Cranston, Aaron Paul') }}">
                                    <small class="form-text text-muted">{{ __tr('Separate names with commas') }}</small>
                                </div>

                            </div>
                        </div>

                        {{-- Trailer & publishing --}}
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fab fa-youtube mr-2 text-danger"></i>{{ __tr('Trailer & Publishing') }}
                                </h3>
                            </div>
                            <div class="card-body">

                                <div class="form-group">
                                    <label>{{ __tr('YouTube Trailer URL or Video ID') }}</label>
                                    <input type="text" name="trailer_url" id="trailerUrlInput" class="form-control"
                                        value="{{ old('trailer_url', $item?->trailer_url) }}"
                                        placeholder="{{ __tr('https://youtube.com/watch?v=… or 11-char ID') }}">
                                    <small class="form-text text-muted">
                                        {{ __tr('Accepts full YouTube URL or just the 11-character video ID') }}
                                    </small>
                                    <div class="trailer-preview" id="trailerPreview">
                                        <iframe id="trailerFrame" allowfullscreen></iframe>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Badge Text') }}</label>
                                            <input type="text" name="badge_text" class="form-control"
                                                value="{{ old('badge_text', $item?->badge_text) }}"
                                                placeholder="{{ __tr('e.g. NEW, 4K, EXCLUSIVE') }}" maxlength="20">
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="is_active" value="1"
                                                    class="custom-control-input" id="isActiveMC"
                                                    {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="isActiveMC">
                                                    {{ __tr('Active (visible)') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="featured_on_home" value="1"
                                                    class="custom-control-input" id="featuredOnHome"
                                                    {{ old('featured_on_home', $item?->featured_on_home ?? false) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="featuredOnHome">
                                                    {{ __tr('Show on Home Page') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    {{ $editing ? __tr('Update') : __tr('Save') }}
                                </button>
                                <a href="{{ route('admin.media-content.index') }}" class="btn btn-secondary ml-2">
                                    {{ __tr('Cancel') }}
                                </a>
                            </div>
                        </div>

                    </div>

                    {{-- ── RIGHT: thumbnail ── --}}
                    <div class="col-lg-4 mb-4">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-image mr-2"></i>{{ __tr('Poster / Thumbnail') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <x-media name="thumbnail" :value="old('thumbnail', $item?->thumbnail)" width="100">
                                </x-media>
                                @error('thumbnail')
                                    <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2 d-block">
                                    {{ __tr('Recommended: 2:3 portrait ratio (movie poster). JPG or PNG.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>
@endsection
@section('js')
    <script>
        (function() {
            // ── Show/hide TV-show fields ──
            const typeSelect = document.getElementById('typeSelect');
            const tvFields = document.getElementById('tvShowFields');

            function toggleTvFields() {
                tvFields.style.display = typeSelect.value === 'tv_show' ? 'block' : 'none';
            }

            typeSelect.addEventListener('change', toggleTvFields);
            toggleTvFields();

            // ── Live trailer preview ──
            const trailerInput = document.getElementById('trailerUrlInput');
            const trailerPreview = document.getElementById('trailerPreview');
            const trailerFrame = document.getElementById('trailerFrame');

            function extractYoutubeId(val) {
                val = val.trim();
                const m = val.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                if (m) return m[1];
                if (/^[a-zA-Z0-9_-]{11}$/.test(val)) return val;
                return null;
            }

            function updatePreview() {
                const id = extractYoutubeId(trailerInput.value);
                if (id) {
                    trailerFrame.src = 'https://www.youtube.com/embed/' + id;
                    trailerPreview.style.display = 'block';
                } else {
                    trailerPreview.style.display = 'none';
                    trailerFrame.src = '';
                }
            }

            trailerInput.addEventListener('input', updatePreview);
            updatePreview();
        })();
    </script>
@endsection
