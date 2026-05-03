@php
    $links = [
        [
            'title' => 'Listing Categories',
            'route' => 'classified.ads.categories.list',
            'active' => false,
        ],
        [
            'title' => 'Edit Category',
            'route' => '',
            'active' => true,
        ],
    ];
    $lang = request()->get('lang', defaultLangCode());
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Edit Category') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('/public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Edit Category" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Category Information') }}</h3>
                        </div>
                        <div class="card-body">
                            {{-- Language tabs --}}
                            <div class="lang-switcher-wrap mb-3">
                                <div class="lang-switcher-label">
                                    <i class="fas fa-globe-americas"></i>
                                    <span>{{ __tr('Language') }}</span>
                                </div>
                                <div class="lang-switcher-tabs">
                                    @foreach (activeLanguages() as $language)
                                        <a href="{{ route('classified.ads.categories.edit.page', ['id' => $category->id, 'lang' => $language->code]) }}"
                                            class="lang-switcher-btn @if ($language->code == $lang) active @endif">
                                            <span class="lang-dot"></span>
                                            {{ $language->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <form action="{{ route('classified.ads.categories.update') }}" method="POST"
                                enctype="multipart/form-data" id="editForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $category->id }}">
                                <input type="hidden" name="lang" value="{{ $lang }}">

                                {{-- Title (translatable) --}}
                                <div class="form-group">
                                    <label class="black font-14">{{ __tr('Title') }}</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ $category->translation('title', $lang) }}"
                                        placeholder="{{ __tr('Enter title') }}">
                                    @error('title')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Fields below are only shown for default language --}}
                                @if ($lang == defaultLangCode())
                                    <div class="form-row mb-3">
                                        <div class="form-group col-lg-6">
                                            <label class="black font-14">{{ __tr('Icon') }}</label>
                                            <x-media name="icon_edit" :value="$category->icon"></x-media>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label class="black font-14">{{ __tr('Featured Image') }}</label>
                                            <x-media name="image_edit" :value="$category->image"></x-media>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-14 bold black w-100">{{ __tr('Parent') }}</label>
                                        <select class="parent-options form-control w-100" name="parent">
                                            @if ($category->parentCategory != null)
                                                <option value="{{ $category->parentCategory->id }}" selected>
                                                    {{ $category->parentCategory->title }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="black font-14">{{ __tr('Status') }}</label>
                                        <select name="status" class="form-control">
                                            <option value="{{ config('settings.general_status.active') }}"
                                                @selected($category->status == config('settings.general_status.active'))>
                                                {{ __tr('Active') }}
                                            </option>
                                            <option value="{{ config('settings.general_status.in_active') }}"
                                                @selected($category->status == config('settings.general_status.in_active'))>
                                                {{ __tr('Inactive') }}
                                            </option>
                                        </select>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __tr('Save Changes') }}
                                    </button>
                                    <a href="{{ route('classified.ads.categories.list') }}" class="btn btn-secondary">
                                        {{ __tr('Back') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('/public/web-assets/backend/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();

            @if ($lang == defaultLangCode())
                $('.parent-options').select2({
                    theme: "bootstrap4",
                    placeholder: '{{ __tr('Select parent category') }}',
                    closeOnSelect: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('classified.ads.categories.options') }}',
                        dataType: 'json',
                        method: "GET",
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }
                });
            @endif

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').remove();
                $(document).find('.form-control').removeClass('is-invalid');
                var formData = new FormData(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: formData,
                    url: '{{ route('classified.ads.categories.update') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Success');
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').addClass(
                                    'is-invalid');
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>'
                                );
                            });
                        } else {
                            toastr.error('{{ __tr('Category update failed') }}', 'Error');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
