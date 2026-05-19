@php
    $links = [['title' => __tr('Channels'), 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Channels') }}
@endsection
@section('page-content')
    <x-admin-page-header title="{{ __tr('Channels') }}" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('All Channels') }}</h3>
                            <button class="btn btn-primary btn-sm float-right" onclick="openChannelModal()">
                                <i class="fas fa-plus mr-1"></i> {{ __tr('Add Channel') }}
                            </button>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-bordered text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __tr('Logo') }}</th>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('BG Color') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th>{{ __tr('Order') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($channels as $i => $channel)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center"
                                                    style="width:48px;height:34px;border-radius:6px;background:{{ $channel->bg_color }};overflow:hidden;">
                                                    @if ($channel->logo)
                                                        <img src="{{ asset(getFilePath($channel->logo, true)) }}"
                                                            alt="{{ $channel->name }}"
                                                            style="max-width:90%;max-height:90%;object-fit:contain;">
                                                    @else
                                                        <span
                                                            style="font-size:.6rem;font-weight:800;color:rgba(255,255,255,.8);letter-spacing:.05em;">
                                                            {{ strtoupper(substr($channel->name, 0, 3)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $channel->name }}</td>
                                            <td>
                                                <span class="d-inline-flex align-items-center" style="gap:7px;">
                                                    <span
                                                        style="display:inline-block;width:18px;height:18px;border-radius:4px;background:{{ $channel->bg_color }};border:1px solid #e2e8f0;"></span>
                                                    <code style="font-size:.78rem;">{{ $channel->bg_color }}</code>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $channel->status ? 'success' : 'secondary' }}">
                                                    {{ $channel->status ? __tr('Active') : __tr('Inactive') }}
                                                </span>
                                            </td>
                                            <td>{{ $channel->sort_order }}</td>
                                            <td class="text-right">
                                                <button class="btn btn-sm btn-warning mr-1"
                                                    onclick="openChannelModal({{ $channel->id }}, '{{ addslashes($channel->name) }}', '{{ $channel->logo }}', '{{ $channel->bg_color }}', {{ $channel->status ? 1 : 0 }}, {{ $channel->sort_order }})">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <form action="{{ route('admin.channels.destroy', $channel) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __tr('Delete this channel?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <p class="text-center text-muted my-3">
                                                    {{ __tr('No channels yet. Click "Add Channel" to get started.') }}
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
        <div class="modal fade" id="channelModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-tv mr-2"></i>
                            <span id="channelModalTitle">{{ __tr('Add Channel') }}</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="channelForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="channelFormMethod" value="POST">

                        <div class="modal-body p-0">
                            <div class="row no-gutters">

                                {{-- Left panel ── Channel Details --}}
                                <div class="col-md-8 border-right">
                                    <div class="px-4 py-3 border-bottom bg-light">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-info-circle mr-1 text-primary"></i>
                                            {{ __tr('Channel Details') }}
                                        </h6>
                                    </div>
                                    <div class="px-4 py-3">

                                        <div class="form-group">
                                            <label class="font-weight-600">
                                                {{ __tr('Channel Name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" id="ch_name"
                                                class="form-control form-control-lg"
                                                placeholder="{{ __tr('e.g. ESPN, BBC One') }}" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Background Color') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text p-1">
                                                                <input type="color" id="ch_color_picker" value="#1e293b"
                                                                    class="border-0"
                                                                    style="width:34px;height:34px;cursor:pointer;border-radius:4px;">
                                                            </span>
                                                        </div>
                                                        <input type="text" name="bg_color" id="ch_bg_color"
                                                            class="form-control" value="#1e293b" placeholder="#1e293b"
                                                            maxlength="20">
                                                    </div>
                                                    <small
                                                        class="form-text text-muted">{{ __tr('Tile background color') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="font-weight-600">{{ __tr('Sort Order') }}</label>
                                                    <input type="number" name="sort_order" id="ch_sort_order"
                                                        class="form-control" value="0" min="0">
                                                    <small
                                                        class="form-text text-muted">{{ __tr('Lower = first') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="px-4 py-3 border-top bg-light">
                                        <h6 class="mb-2 font-weight-bold text-dark">
                                            <i class="fas fa-toggle-on mr-1 text-success"></i>
                                            {{ __tr('Visibility') }}
                                        </h6>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="status" value="1"
                                                class="custom-control-input" id="ch_status" checked>
                                            <label class="custom-control-label" for="ch_status">
                                                {{ __tr('Active — show this channel on the site') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right panel ── Logo & Preview --}}
                                <div class="col-md-4">
                                    <div class="px-3 py-3 border-bottom bg-light">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-image mr-1 text-secondary"></i>
                                            {{ __tr('Logo & Preview') }}
                                        </h6>
                                    </div>
                                    <div class="px-3 py-3">
                                        <x-media name="logo" :value="null" width="100"></x-media>
                                        <small class="form-text text-muted mt-1">
                                            {{ __tr('PNG transparent background recommended.') }}
                                        </small>
                                    </div>
                                    <div class="px-3 pb-4 text-center">
                                        <p class="text-muted mb-2"
                                            style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">
                                            {{ __tr('Tile Preview') }}
                                        </p>
                                        <div id="ch_preview_tile"
                                            style="width:96px;height:96px;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;background:#1e293b;border:1px solid rgba(0,0,0,.1);box-shadow:0 4px 12px rgba(0,0,0,.15);">
                                            <span id="ch_preview_label"
                                                style="font-size:.82rem;font-weight:800;color:rgba(255,255,255,.8);letter-spacing:.05em;">
                                                CH
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>{{ __tr('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" id="channelSubmitBtn">
                                <i class="fas fa-save mr-1"></i>{{ __tr('Add Channel') }}
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
            var storeUrl = '{{ route('admin.channels.store') }}';
            var editBaseUrl = '{{ url('admin/channels') }}';

            var picker = document.getElementById('ch_color_picker');
            var bgText = document.getElementById('ch_bg_color');
            var tile = document.getElementById('ch_preview_tile');
            var label = document.getElementById('ch_preview_label');
            var nameIn = document.getElementById('ch_name');

            picker.addEventListener('input', function() {
                bgText.value = this.value;
                tile.style.background = this.value;
            });

            bgText.addEventListener('input', function() {
                if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(this.value)) {
                    picker.value = this.value;
                    tile.style.background = this.value;
                }
            });

            nameIn.addEventListener('input', function() {
                label.textContent = this.value.substring(0, 3).toUpperCase() || 'CH';
            });

            window.openChannelModal = function(id, name, logo, bgColor, status, sortOrder) {
                var isEdit = !!id;

                document.getElementById('channelModalTitle').textContent = isEdit ?
                    '{{ __tr('Edit Channel') }}' :
                    '{{ __tr('Add Channel') }}';
                document.getElementById('channelSubmitBtn').textContent = isEdit ?
                    '{{ __tr('Update Channel') }}' :
                    '{{ __tr('Add Channel') }}';

                if (isEdit) {
                    document.getElementById('channelForm').action = editBaseUrl + '/' + id;
                    document.getElementById('channelFormMethod').value = 'PUT';
                } else {
                    document.getElementById('channelForm').action = storeUrl;
                    document.getElementById('channelFormMethod').value = 'POST';
                }

                nameIn.value = name || '';
                label.textContent = (name || 'CH').substring(0, 3).toUpperCase();

                var color = bgColor || '#1e293b';
                bgText.value = color;
                picker.value = color;
                tile.style.background = color;

                document.getElementById('ch_sort_order').value = sortOrder || 0;
                document.getElementById('ch_status').checked = isEdit ? !!status : true;

                // Populate media picker
                var logoPath = logo || '';
                document.getElementById('input-logo').value = logoPath;
                if (logoPath) {
                    document.getElementById('single-img-wrap-logo').classList.remove('media-hidden');
                    document.getElementById('single-placeholder-logo').classList.add('media-hidden');
                    document.getElementById('media-input-preview-logo').src = logoPath.startsWith('http') ?
                        logoPath :
                        '/public/' + logoPath;
                } else {
                    document.getElementById('single-img-wrap-logo').classList.add('media-hidden');
                    document.getElementById('single-placeholder-logo').classList.remove('media-hidden');
                }

                $('#channelModal').modal('show');
            };

            // Reset form on modal hidden
            $('#channelModal').on('hidden.bs.modal', function() {
                document.getElementById('channelForm').reset();
                bgText.value = '#1e293b';
                picker.value = '#1e293b';
                tile.style.background = '#1e293b';
                label.textContent = 'CH';
                document.getElementById('input-logo').value = '';
                document.getElementById('single-img-wrap-logo').classList.add('media-hidden');
                document.getElementById('single-placeholder-logo').classList.remove('media-hidden');
            });
        })();
    </script>
@endsection
