@php
    $links = [
        [
            'title' => 'Blogs',
            'route' => 'admin.blogs.list',
            'active' => false,
        ],
        [
            'title' => 'Categories',
            'route' => '',
            'active' => true,
        ],
    ];
    $lang = request()->get('lang');
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Edit Category') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Edit Category" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Category Information') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="lang-switcher-wrap mb-3">
                                <div class="lang-switcher-label">
                                    <i class="fas fa-globe-americas"></i>
                                    <span>{{ __tr('Language') }}</span>
                                </div>
                                <div class="lang-switcher-tabs">
                                    @foreach (activeLanguages() as $key => $language)
                                        <a href="{{ route('admin.blogs.categories.edit', ['id' => $category->id, 'lang' => $language->code]) }}"
                                            class="lang-switcher-btn @if ($language->code == $lang) active @endif">
                                            <span class="lang-dot"></span>
                                            {{ $language->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <form action="{{ route('admin.blogs.categories.update') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>{{ __tr('Category Title') }}</label>
                                    <input type="hidden" name="id" value="{{ $category->id }}">
                                    <input type="hidden" name="lang" value="{{ $lang }}">
                                    <input type="text" class="form-control" name="title"
                                        value="{{ $category->translation('title', $lang) }}"
                                        placeholder="{{ __tr('Enter Category Title') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Parent Category') }}</label>
                                    <select class="parent-category-select form-control" name="parent">
                                        @if ($category->parentCat != null)
                                            <option value="{{ $category->parentCat->id }}" selected>
                                                {{ $category->parentCat->title }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Meta Title') }}</label>
                                    <input type="text" class="form-control" name="meta_title"
                                        value="{{ $category->meta_title }}" placeholder="{{ __tr('Enter Meta Title') }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Meta Description') }}</label>
                                    <textarea class="form-control" name="meta_description" value="{{ $category->meta_description }}"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Meta Image') }}</label>
                                    <x-media name="edit_meta_image" :value="$category->meta_image"></x-media>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="{{ config('settings.general_status.active') }}"
                                            @selected($category->status == config('settings.general_status.active'))>
                                            {{ __tr('Active') }}</option>
                                        <option value="{{ config('settings.general_status.in_active') }}"
                                            @selected($category->status == config('settings.general_status.in_active'))>
                                            {{ __tr('Inactive') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Is Featured') }}</label>
                                    <select name="is_featured" class="form-control">
                                        <option value="{{ config('settings.general_status.active') }}"
                                            @selected($category->is_featured == config('settings.general_status.active'))>
                                            {{ __tr('Active') }}</option>
                                        <option value="{{ config('settings.general_status.in_active') }}"
                                            @selected($category->is_featured == config('settings.general_status.in_active'))>
                                            {{ __tr('Inactive') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
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
    <script src="{{ asset('public/web-assets/backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
            //Parent category options
            $('.parent-category-select').select2({
                theme: "bootstrap4",
                placeholder: 'Select parent category',
                ajax: {
                    url: '{{ route('admin.blogs.categories.dropdown.options') }}',
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
        })(jQuery);
    </script>
@endsection
