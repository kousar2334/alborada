@php
    $links = [['title' => __tr('Contents'), 'route' => '', 'active' => true]];
    $typeColors = ['movie' => 'danger', 'series' => 'info', 'sports_event' => 'warning', 'new_release' => 'primary'];
    $typeLabels = [
        'movie' => 'Movie',
        'series' => 'Series',
        'sports_event' => 'Sports Event',
        'new_release' => 'New Release',
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Contents') }}
@endsection
@section('page-content')
    <x-admin-page-header title="{{ __tr('Contents') }}" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('All Contents') }}</h3>
                            <button class="btn btn-primary btn-sm float-right" onclick="openFcModal()">
                                <i class="fas fa-plus mr-1"></i> {{ __tr('Add Content') }}
                            </button>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th style="width:80px;">{{ __tr('Thumbnail') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th style="width:110px;">{{ __tr('Type') }}</th>
                                        <th style="width:100px;">{{ __tr('Genre') }}</th>
                                        <th style="width:90px;">{{ __tr('Status') }}</th>
                                        <th style="width:80px;">{{ __tr('Order') }}</th>
                                        <th class="text-right" style="width:130px;">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                @if ($item->thumbnail)
                                                    <img src="{{ asset(getFilePath($item->thumbnail, true)) }}"
                                                        alt="{{ $item->title }}"
                                                        style="width:52px;height:36px;object-fit:cover;border-radius:5px;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center"
                                                        style="width:52px;height:36px;border-radius:5px;background:#0f172a;">
                                                        <i class="fas {{ $item->type_icon }} text-white"
                                                            style="font-size:.7rem;opacity:.5;"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight:600;">{{ $item->title }}</div>
                                                @if ($item->subtitle)
                                                    <div style="font-size:.78rem;color:#6b7280;">
                                                        {{ Str::limit($item->subtitle, 50) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $typeColors[$item->type] ?? 'secondary' }}">
                                                    {{ $typeLabels[$item->type] ?? $item->type }}
                                                </span>
                                            </td>
                                            <td>{{ $item->genre ?: '—' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                                    {{ $item->is_active ? __tr('Active') : __tr('Hidden') }}
                                                </span>
                                            </td>
                                            <td>{{ $item->sort_order }}</td>
                                            <td class="text-right">
                                                <button class="btn btn-sm btn-warning mr-1"
                                                    onclick="openFcModal(
                                                        {{ $item->id }},
                                                        '{{ addslashes($item->title) }}',
                                                        '{{ addslashes($item->subtitle ?? '') }}',
                                                        '{{ $item->thumbnail }}',
                                                        '{{ $item->trailer_url }}',
                                                        '{{ $item->type }}',
                                                        '{{ addslashes($item->genre ?? '') }}',
                                                        '{{ $item->event_date?->format('Y-m-d') }}',
                                                        '{{ addslashes($item->badge_text ?? '') }}',
                                                        {{ $item->sort_order }},
                                                        {{ $item->is_active ? 1 : 0 }},
                                                        {{ $item->release_year ?? 'null' }},
                                                        {{ $item->rating ?? 'null' }}
                                                    )">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <form action="{{ route('admin.featured-content.destroy', $item) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __tr('Delete this item?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">
                                                <p class="text-center text-muted my-3">
                                                    {{ __tr('No content yet. Click "Add Content" to get started.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Add / Edit Modal ── --}}
        <div class="modal fade" id="fcModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-film mr-2"></i>
                            <span id="fcModalTitle">{{ __tr('Add Content') }}</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="fcForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="fcFormMethod" value="POST">

                        <div class="modal-body p-0">
                            <div class="row no-gutters">

                                {{-- Left panel ── Content Details --}}
                                <div class="col-md-8 border-right">

                                    {{-- Section: Basic Info --}}
                                    <div class="px-4 py-2 border-bottom bg-light">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-info-circle mr-1 text-primary"></i>
                                            {{ __tr('Basic Information') }}
                                        </h6>
                                    </div>
                                    <div class="px-4 py-3 border-bottom">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-600">
                                                        {{ __tr('Title') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="title" id="fc_title"
                                                        class="form-control form-control-lg"
                                                        placeholder="{{ __tr('e.g. Avengers: Endgame') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-600">
                                                        {{ __tr('Type') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="type" id="fc_type"
                                                        class="form-control form-control-lg">
                                                        <option value="movie">🎬 Movie</option>
                                                        <option value="series">📺 Series</option>
                                                        <option value="sports_event">🏆 Sports Event</option>
                                                        <option value="new_release">⭐ New Release</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Details --}}
                                    <div class="px-4 py-2 border-bottom bg-light">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-align-left mr-1 text-info"></i>
                                            {{ __tr('Details') }}
                                        </h6>
                                    </div>
                                    <div class="px-4 py-3 border-bottom">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Cast / Actors') }}</label>
                                                    <input type="text" name="subtitle" id="fc_subtitle"
                                                        class="form-control"
                                                        placeholder="{{ __tr('e.g. Tom Hanks, Meg Ryan, ...') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Genre') }}</label>
                                                    <input type="text" name="genre" id="fc_genre"
                                                        class="form-control"
                                                        placeholder="{{ __tr('e.g. Action, Drama') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-600">{{ __tr('Trailer / Preview URL') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fab fa-youtube text-danger"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="trailer_url" id="fc_trailer_url"
                                                    class="form-control"
                                                    placeholder="{{ __tr('YouTube URL or 11-char video ID') }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Meta --}}
                                    <div class="px-4 py-2 border-bottom bg-light">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-tag mr-1 text-warning"></i>
                                            {{ __tr('Meta & Ordering') }}
                                        </h6>
                                    </div>
                                    <div class="px-4 py-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Release Year') }}</label>
                                                    <input type="number" name="release_year" id="fc_release_year"
                                                        class="form-control" placeholder="e.g. 2024" min="1900"
                                                        max="2099">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Rating') }}</label>
                                                    <input type="number" name="rating" id="fc_rating"
                                                        class="form-control" placeholder="e.g. 8.5" min="0"
                                                        max="10" step="0.1">
                                                    <small class="form-text text-muted">{{ __tr('0.0 – 10.0') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Event Date') }}</label>
                                                    <input type="date" name="event_date" id="fc_event_date"
                                                        class="form-control">
                                                    <small
                                                        class="form-text text-muted">{{ __tr('For sports events') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Badge Text') }}</label>
                                                    <input type="text" name="badge_text" id="fc_badge_text"
                                                        class="form-control"
                                                        placeholder="{{ __tr('e.g. NEW, LIVE, 4K') }}" maxlength="20">
                                                    <small
                                                        class="form-text text-muted">{{ __tr('Max 20 characters') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-600">{{ __tr('Sort Order') }}</label>
                                                    <input type="number" name="sort_order" id="fc_sort_order"
                                                        class="form-control" value="0" min="0">
                                                    <small
                                                        class="form-text text-muted">{{ __tr('Lower = first') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Visibility --}}
                                    <div class="px-4 py-3 border-top bg-light">
                                        <h6 class="mb-2 font-weight-bold text-dark">
                                            <i class="fas fa-toggle-on mr-1 text-success"></i>
                                            {{ __tr('Visibility') }}
                                        </h6>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="is_active" value="1"
                                                class="custom-control-input" id="fc_is_active" checked>
                                            <label class="custom-control-label" for="fc_is_active">
                                                {{ __tr('Active — show this content on the site') }}
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                {{-- Right panel ── Thumbnail --}}
                                <div class="col-md-4">
                                    <div class="px-3 py-2 border-bottom bg-light">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-image mr-1 text-secondary"></i>
                                            {{ __tr('Thumbnail Image') }}
                                        </h6>
                                    </div>
                                    <div class="px-3 py-3">
                                        <x-media name="thumbnail" :value="null" width="100"></x-media>
                                        <small class="form-text text-muted mt-2">
                                            {{ __tr('Recommended: 16:9 ratio. JPG or PNG.') }}
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>{{ __tr('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" id="fcSubmitBtn">
                                <i class="fas fa-save mr-1"></i>{{ __tr('Add Content') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
@endsection
@section('page-script')
    <script>
        initMediaManager();
        (function() {
            var storeUrl = '{{ route('admin.featured-content.store') }}';
            var editBase = '{{ url('admin/featured-content') }}';

            window.openFcModal = function(id, title, subtitle, thumbnail, trailerUrl, type, genre, eventDate, badgeText,
                sortOrder, isActive, releaseYear, rating) {
                var isEdit = !!id;

                document.getElementById('fcModalTitle').textContent = isEdit ?
                    '{{ __tr('Edit Content') }}' :
                    '{{ __tr('Add Content') }}';
                document.getElementById('fcSubmitBtn').textContent = isEdit ?
                    '{{ __tr('Update Content') }}' :
                    '{{ __tr('Add Content') }}';

                if (isEdit) {
                    document.getElementById('fcForm').action = editBase + '/' + id;
                    document.getElementById('fcFormMethod').value = 'PUT';
                } else {
                    document.getElementById('fcForm').action = storeUrl;
                    document.getElementById('fcFormMethod').value = 'POST';
                }

                document.getElementById('fc_title').value = title || '';
                document.getElementById('fc_subtitle').value = subtitle || '';
                document.getElementById('fc_trailer_url').value = trailerUrl || '';
                document.getElementById('fc_genre').value = genre || '';
                document.getElementById('fc_event_date').value = eventDate || '';
                document.getElementById('fc_badge_text').value = badgeText || '';
                document.getElementById('fc_sort_order').value = sortOrder || 0;
                document.getElementById('fc_release_year').value = releaseYear || '';
                document.getElementById('fc_rating').value = rating || '';
                document.getElementById('fc_is_active').checked = isEdit ? !!isActive : true;

                var sel = document.getElementById('fc_type');
                if (type) sel.value = type;

                // Populate thumbnail media picker
                var thumb = thumbnail || '';
                document.getElementById('input-thumbnail').value = thumb;
                if (thumb) {
                    document.getElementById('single-img-wrap-thumbnail').classList.remove('media-hidden');
                    document.getElementById('single-placeholder-thumbnail').classList.add('media-hidden');
                    document.getElementById('media-input-preview-thumbnail').src = thumb.startsWith('http') ?
                        thumb :
                        '/public/' + thumb;
                } else {
                    document.getElementById('single-img-wrap-thumbnail').classList.add('media-hidden');
                    document.getElementById('single-placeholder-thumbnail').classList.remove('media-hidden');
                }

                $('#fcModal').modal('show');
            };

            // Reset on close
            $('#fcModal').on('hidden.bs.modal', function() {
                document.getElementById('fcForm').reset();
                document.getElementById('fc_release_year').value = '';
                document.getElementById('fc_rating').value = '';
                document.getElementById('input-thumbnail').value = '';
                document.getElementById('single-img-wrap-thumbnail').classList.add('media-hidden');
                document.getElementById('single-placeholder-thumbnail').classList.remove('media-hidden');
            });
        })();
    </script>
@endsection
