@php
    $links = [
        [
            'title' => 'Blogs',
            'route' => 'admin.blogs.list',
            'active' => false,
        ],
        [
            'title' => 'New Blog',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Create New Blog') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="New Blog" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('admin.blogs.new.store') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Blog Title') }}</label>
                                    <input type="text" class="form-control blog-title" name="title"
                                        placeholder="Enter Blog Title" value="{{ old('title') }}">
                                    @if ($errors->has('title'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('title') }}</div>
                                    @endif
                                </div>
                                <!--Permalink-->
                                <div
                                    class="form-row align-items-center mb-20 permalink-input-group d-none @if ($errors->has('permalink')) d-flex @endif">
                                    <div class="label">
                                        <label class="font-14 bold black">{{ __tr('Permalink') }} </label>
                                        <input type="hidden" id="permalink_input_field" value="{{ old('permalink') }}"
                                            name="permalink">
                                    </div>
                                    <div class="input ml-1">
                                        <a href="#">{{ url('') }}/blogs/<span
                                                id="permalink">{{ old('permalink') }}</span><span
                                                class="btn btn-light ml-1 permalink-edit-btn">{{ __tr('Edit') }}</span></a>

                                        <div class="permalink-editor d-none">
                                            <input type="text" class="form-control" id="permalink-updated-input"
                                                placeholder="{{ __tr('Type here') }}">
                                            <button type="button" class="btn mt-2 btn-danger permalink-cancel-btn"
                                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                                            <button type="button"
                                                class="btn btn-success mt-2 permalink-save-btn">{{ __tr('Save') }}</button>
                                        </div>
                                        @if ($errors->has('permalink'))
                                            <div class="error text-danger mb-0 invalid-input">
                                                {{ $errors->first('permalink') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <!--End Permalink-->
                                <div class="form-group">
                                    <label>{{ __tr('Content') }}</label>
                                    <textarea id="contentSummernote" name="content">{{ old('content') }}</textarea>
                                    @if ($errors->has('content'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('content') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Short Description') }}</label>
                                    <textarea class="form-control" name="short_description">{{ old('short_description') }}</textarea>
                                    @if ($errors->has('short_description'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('short_description') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Video Link') }}</label>
                                    <input type="text" class="form-control" name="video_link"
                                        value="{{ old('video_link') }}" placeholder="{{ __tr('Enter Youtube link') }}">
                                    @if ($errors->has('video_link'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('video_link') }}</div>
                                    @endif
                                </div>

                                <!--Seo-->
                                <div class="form-group">
                                    <label>{{ __tr('Meta Title') }}</label>
                                    <input type="text" class="form-control" name="meta_title"
                                        placeholder="{{ __tr('Enter Meta Title') }}" value="{{ old('meta_title') }}">
                                    @if ($errors->has('meta_title'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('meta_title') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Meta Description') }}</label>
                                    <textarea class="form-control" name="meta_description">{{ old('meta_description') }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('meta_description') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Meta Image') }}</label>
                                    <x-media name="meta_image" :value="old('meta_image')"></x-media>
                                    @if ($errors->has('meta_image'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('meta_image') }}</div>
                                    @endif
                                </div>
                                <!--End Seo-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Featured Blog') }}</label>
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox" name="is_featured" class="custom-control-input"
                                            id="blogFeatureSwitcher">
                                        <label class="custom-control-label" for="blogFeatureSwitcher"></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Thumbnail Image') }}</label>
                                    <x-media name="thumbnail" :value="old('thumbnail')"></x-media>
                                    @if ($errors->has('thumbnail'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('thumbnail') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Featured Image') }}</label>
                                    <x-media name="featured_image" :value="old('featured_image')"></x-media>
                                    @if ($errors->has('featured_image'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('featured_image') }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Categories') }}</label>
                                    <select class="categories-select form-control" name="categories[]" multiple>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Tags') }}</label>
                                    <select class="tag-select form-control" name="tags[]" multiple>

                                    </select>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" name="status"
                                        value="{{ config('settings.blog_status.draft') }}"
                                        class="btn btn-secondary">{{ __tr('Save & Draft') }}</button>
                                    <button type="submit" name="status"
                                        value="{{ config('settings.blog_status.publish') }}"
                                        class="btn btn-primary">{{ __tr('Save & Publish') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
            $('#contentSummernote').summernote({
                tabsize: 2,
                height: 250,
                toolbar: [
                    ["style", ["style"]],
                    ["font", ["bold", "underline", "clear"]],
                    ["color", ["color"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["table", ["table"]],
                    ["insert", ["link", "picture"]],
                    ["view", ["fullscreen", "help"]],
                ],
                callbacks: {
                    onImageUpload: function(images, editor, welEditable, ) {
                        sendFile(images[0], editor, 'contentSummernote');
                    }
                }
            });

            // Image insert function in summernote
            function sendFile(image, editor, section_id) {
                "use strict";
                let data = new FormData();
                data.append("image", image);
                data.append("_token", '{{ csrf_token() }}');
                $.ajax({
                    data: data,
                    type: "POST",
                    url: '{{ route('utility.store.editor.image') }}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if (data.url) {
                            var image = $('<img>').attr('src', data.url);
                            $('#' + section_id).summernote("insertNode", image[0]);
                        }
                    },
                    error: function(data) {
                        toastr.error('Image Insert Failed', "Error!");
                    }
                });
            }
            //Blog category options
            $('.categories-select').select2({
                theme: "bootstrap4",
                placeholder: 'Select Categories',
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
            //Blog tags options
            $('.tag-select').select2({
                theme: "bootstrap4",
                tags: true,
                placeholder: 'Select or create tags',
                ajax: {
                    url: '{{ route('admin.blogs.tags.dropdown.options') }}',
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

            /*Generate permalink*/
            $(".blog-title").change(function(e) {
                e.preventDefault();
                let name = $(".blog-title").val();
                let permalink = generateSlug(name);
                $("#permalink").html(permalink);
                $("#permalink_input_field").val(permalink);
                $(".permalink-input-group").removeClass("d-none");
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /*edit permalink*/
            $(".permalink-edit-btn").on("click", function(e) {
                e.preventDefault();
                let permalink = $("#permalink").html();
                $("#permalink-updated-input").val(permalink);
                $(".permalink-edit-btn").addClass("d-none");
                $(".permalink-editor").removeClass("d-none");
            });
            /*Cancel permalink edit*/
            $(".permalink-cancel-btn").on("click", function(e) {
                e.preventDefault();
                $("#permalink-updated-input").val();
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /*Update permalink*/
            $(".permalink-save-btn").on("click", function(e) {
                e.preventDefault();
                let input = $("#permalink-updated-input").val();
                let updated_permalink = generateSlug(input);
                $("#permalink_input_field").val(updated_permalink);
                $("#permalink").html(updated_permalink);
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });

        })(jQuery);
        /**
         * Generate slug
         * 
         */
        function generateSlug(str) {
            "use strict";
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to = "aaaaeeeeiiiioooouuuunc------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        }
    </script>
@endsection
