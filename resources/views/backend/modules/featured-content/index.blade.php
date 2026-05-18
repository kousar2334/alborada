@php
    $links = [['title' => __tr('Featured Content'), 'route' => '', 'active' => true]];
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
    {{ __tr('Featured Content') }}
@endsection
@section('page-content')
    <x-admin-page-header title="{{ __tr('Featured Content') }}" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('All Featured Content') }}</h3>
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
                                                        {{ $item->is_active ? 1 : 0 }}
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="fcModalTitle">{{ __tr('Add Content') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="fcForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="fcFormMethod" value="POST">
                        <div class="modal-body">
                            <div class="row">

                                {{-- Left: main fields --}}
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>{{ __tr('Title') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="title" id="fc_title" class="form-control"
                                                    placeholder="{{ __tr('e.g. Avengers: Endgame') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __tr('Type') }} <span class="text-danger">*</span></label>
                                                <select name="type" id="fc_type" class="form-control">
                                                    <option value="movie">Movie</option>
                                                    <option value="series">Series</option>
                                                    <option value="sports_event">Sports Event</option>
                                                    <option value="new_release">New Release</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>{{ __tr('Subtitle / Tagline') }}</label>
                                                <input type="text" name="subtitle" id="fc_subtitle" class="form-control"
                                                    placeholder="{{ __tr('Short description or tagline') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __tr('Genre') }}</label>
                                                <input type="text" name="genre" id="fc_genre" class="form-control"
                                                    placeholder="{{ __tr('e.g. Action, Drama') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __tr('Trailer / Preview URL') }}</label>
                                        <input type="text" name="trailer_url" id="fc_trailer_url"
                                            class="form-control"
                                            placeholder="{{ __tr('YouTube URL or 11-char video ID') }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __tr('Event Date') }}</label>
                                                <input type="date" name="event_date" id="fc_event_date"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __tr('Badge Text') }}</label>
                                                <input type="text" name="badge_text" id="fc_badge_text"
                                                    class="form-control" placeholder="{{ __tr('e.g. NEW, LIVE, 4K') }}"
                                                    maxlength="20">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __tr('Sort Order') }}</label>
                                                <input type="number" name="sort_order" id="fc_sort_order"
                                                    class="form-control" value="0" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="is_active" value="1"
                                                class="custom-control-input" id="fc_is_active" checked>
                                            <label class="custom-control-label" for="fc_is_active">
                                                {{ __tr('Active (visible on site)') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: thumbnail --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __tr('Thumbnail Image') }}</label>
                                        <x-media name="thumbnail" :value="null" width="100"></x-media>
                                        <small class="form-text text-muted mt-1">
                                            {{ __tr('Recommended: 16:9 ratio. JPG or PNG.') }}
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary"
                                id="fcSubmitBtn">{{ __tr('Add Content') }}</button>
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
                sortOrder, isActive) {
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
                        '{{ asset('') }}' + thumb;
                } else {
                    document.getElementById('single-img-wrap-thumbnail').classList.add('media-hidden');
                    document.getElementById('single-placeholder-thumbnail').classList.remove('media-hidden');
                }

                $('#fcModal').modal('show');
            };

            // Reset on close
            $('#fcModal').on('hidden.bs.modal', function() {
                document.getElementById('fcForm').reset();
                document.getElementById('input-thumbnail').value = '';
                document.getElementById('single-img-wrap-thumbnail').classList.add('media-hidden');
                document.getElementById('single-placeholder-thumbnail').classList.remove('media-hidden');
            });
        })();
    </script>
@endsection
