@php
    $editing = !is_null($channel);
    $links = [
        ['title' => __tr('Channels'), 'route' => 'admin.channels.index', 'active' => false],
        ['title' => $editing ? __tr('Edit') : __tr('Add'), 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ $editing ? __tr('Edit Channel') : __tr('Add Channel') }}
@endsection
@section('page-content')
    <x-admin-page-header
        title="{{ $editing ? __tr('Edit Channel') : __tr('Add Channel') }}"
        :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <form
                action="{{ $editing ? route('admin.channels.update', $channel) : route('admin.channels.store') }}"
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
                                    <i class="fas fa-tv mr-2"></i>
                                    {{ __tr('Channel Details') }}
                                </h3>
                            </div>
                            <div class="card-body">

                                <div class="form-group">
                                    <label>{{ __tr('Channel Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $channel?->name) }}"
                                        placeholder="{{ __tr('e.g. ESPN, BBC One') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Background Color') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text p-1">
                                                        <input type="color"
                                                            id="bgColorPicker"
                                                            value="{{ old('bg_color', $channel?->bg_color ?? '#1e293b') }}"
                                                            class="border-0"
                                                            style="width:32px;height:32px;cursor:pointer;">
                                                    </span>
                                                </div>
                                                <input type="text" name="bg_color" id="bgColorText"
                                                    class="form-control @error('bg_color') is-invalid @enderror"
                                                    value="{{ old('bg_color', $channel?->bg_color ?? '#1e293b') }}"
                                                    placeholder="#1e293b" maxlength="20">
                                            </div>
                                            @error('bg_color')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">{{ __tr('Background color for the channel tile') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Sort Order') }}</label>
                                            <input type="number" name="sort_order"
                                                class="form-control"
                                                value="{{ old('sort_order', $channel?->sort_order ?? 0) }}" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="status" value="1"
                                            class="custom-control-input" id="ch_status"
                                            {{ old('status', $channel?->status ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ch_status">
                                            {{ __tr('Active (visible on site)') }}
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    {{ $editing ? __tr('Update Channel') : __tr('Add Channel') }}
                                </button>
                                <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary ml-2">
                                    {{ __tr('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Right: logo --}}
                    <div class="col-lg-4 mb-4">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-image mr-2"></i>
                                    {{ __tr('Channel Logo') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <x-media name="logo" :value="old('logo', $channel?->logo)" width="100">
                                </x-media>
                                @error('logo')
                                    <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    {{ __tr('PNG with transparent background recommended.') }}
                                </small>
                            </div>
                        </div>

                        {{-- Live preview --}}
                        <div class="card card-outline card-secondary mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-eye mr-2"></i>
                                    {{ __tr('Preview') }}
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                <div id="chPreviewTile"
                                    style="width:120px;height:80px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:{{ old('bg_color', $channel?->bg_color ?? '#1e293b') }};">
                                    <span id="chPreviewLabel"
                                        style="font-size:1rem;font-weight:800;color:rgba(255,255,255,.75);letter-spacing:.05em;">
                                        {{ strtoupper(substr(old('name', $channel?->name ?? 'CH'), 0, 3)) }}
                                    </span>
                                </div>
                                <p class="mt-2 mb-0 text-muted" style="font-size:.76rem;">{{ __tr('Tile preview') }}</p>
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
        (function () {
            const picker   = document.getElementById('bgColorPicker');
            const textInput = document.getElementById('bgColorText');
            const tile     = document.getElementById('chPreviewTile');
            const nameInput = document.querySelector('input[name="name"]');
            const label    = document.getElementById('chPreviewLabel');

            function syncColor(val) {
                tile.style.background = val;
            }

            picker.addEventListener('input', function () {
                textInput.value = this.value;
                syncColor(this.value);
            });

            textInput.addEventListener('input', function () {
                const val = this.value;
                if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(val)) {
                    picker.value = val;
                    syncColor(val);
                }
            });

            nameInput && nameInput.addEventListener('input', function () {
                label.textContent = this.value.substring(0, 3).toUpperCase() || 'CH';
            });
        })();
    </script>
@endsection
