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
                                        <th style="width:50px;">#</th>
                                        <th style="width:80px;">{{ __tr('Logo') }}</th>
                                        <th>{{ __tr('Name') }}</th>
                                        <th style="width:130px;">{{ __tr('BG Color') }}</th>
                                        <th style="width:90px;">{{ __tr('Status') }}</th>
                                        <th style="width:90px;">{{ __tr('Order') }}</th>
                                        <th class="text-right" style="width:130px;">{{ __tr('Action') }}</th>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="channelModalTitle">{{ __tr('Add Channel') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="channelForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="channelFormMethod" value="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">

                                    <div class="form-group">
                                        <label>{{ __tr('Channel Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="ch_name" class="form-control"
                                            placeholder="{{ __tr('e.g. ESPN, BBC One') }}" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __tr('Background Color') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text p-1">
                                                            <input type="color" id="ch_color_picker" value="#1e293b"
                                                                class="border-0"
                                                                style="width:32px;height:32px;cursor:pointer;">
                                                        </span>
                                                    </div>
                                                    <input type="text" name="bg_color" id="ch_bg_color"
                                                        class="form-control" value="#1e293b" placeholder="#1e293b"
                                                        maxlength="20">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __tr('Sort Order') }}</label>
                                                <input type="number" name="sort_order" id="ch_sort_order"
                                                    class="form-control" value="0" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="status" value="1"
                                                class="custom-control-input" id="ch_status" checked>
                                            <label class="custom-control-label" for="ch_status">
                                                {{ __tr('Active (visible on site)') }}
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __tr('Channel Logo') }}</label>
                                        <x-media name="logo" :value="null" width="100"></x-media>
                                        <small
                                            class="form-text text-muted">{{ __tr('PNG with transparent background recommended.') }}</small>
                                    </div>

                                    <div class="text-center mt-2">
                                        <div id="ch_preview_tile"
                                            style="width:88px;height:88px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:#1e293b;border:1px solid rgba(0,0,0,.08);">
                                            <span id="ch_preview_label"
                                                style="font-size:.78rem;font-weight:800;color:rgba(255,255,255,.75);letter-spacing:.05em;">
                                                CH
                                            </span>
                                        </div>
                                        <p class="mt-1 mb-0 text-muted" style="font-size:.73rem;">{{ __tr('Preview') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary"
                                id="channelSubmitBtn">{{ __tr('Add Channel') }}</button>
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
                        '{{ asset('') }}' + logoPath;
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
