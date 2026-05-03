@php
    $links = [
        [
            'title' => 'Ads',
            'route' => 'classified.ads.list',
            'active' => false,
        ],
        [
            'title' => 'Edit Ad',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Edit Ad') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Ad Information" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('classified.ads.update') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $ad_details->id }}">
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <!-- Basic Information -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __tr('Basic Information') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Title') }}</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ $ad_details->title }}" placeholder="{{ __tr('Enter title') }}">
                                    @if ($errors->has('title'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('title') }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Category') }}</label>
                                    <select class="category-select form-control" name="category">
                                        @if ($ad_details->categoryInfo != null)
                                            <option value="{{ $ad_details->category_id }}" selected>
                                                {{ $ad_details->categoryInfo->title }}
                                            </option>
                                        @endif
                                    </select>
                                    @if ($errors->has('category'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('category') }}</div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Condition') }}</label>
                                            <select class="form-control" name="condition">
                                                <option value="">{{ __tr('Select Condition') }}</option>
                                                @foreach ($conditions as $condition)
                                                    <option value="{{ $condition->id }}" @selected($ad_details->condition_id == $condition->id)>
                                                        {{ $condition->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('condition'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('condition') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Price') }}</label>
                                            <div class="input-group">
                                                <input type="number" name="price" value="{{ $ad_details->price }}"
                                                    class="form-control" placeholder="0.00" step="0.01">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <input type="checkbox" name="is_negotiable"
                                                            @checked($ad_details->is_negotiable == config('settings.general_status.active'))>
                                                        <small class="ml-1">{{ __tr('Negotiable') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($errors->has('price'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('price') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Description') }}</label>
                                    <textarea id="contentSummernote" name="description">{{ $ad_details->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('description') }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Video URL') }}</label>
                                    <input type="text" class="form-control" name="video_url"
                                        value="{{ $ad_details->video_url }}"
                                        placeholder="{{ __tr('Enter YouTube link') }}">
                                    @if ($errors->has('video_url'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('video_url') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __tr('Location Information') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('Country') }}</label>
                                            <select class="form-control" name="country" id="country-select">
                                                <option value="">{{ __tr('Select Country') }}</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" @selected($ad_details->country_id == $country->id)>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('country'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('country') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('State') }}</label>
                                            <select class="form-control" name="state" id="state-select">
                                                <option value="">{{ __tr('Select State') }}</option>
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}" @selected($ad_details->state_id == $state->id)>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('state'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('state') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __tr('City') }}</label>
                                            <select class="form-control" name="city" id="city-select">
                                                <option value="">{{ __tr('Select City') }}</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" @selected($ad_details->city_id == $city->id)>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('city'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('city') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Address') }}</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="{{ __tr('Enter detailed address') }}">{{ $ad_details->address }}</textarea>
                                    @if ($errors->has('address'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('address') }}</div>
                                    @endif
                                </div>

                                @if ($ad_details->cityInfo != null)
                                    <div class="form-group">
                                        <iframe width="100%" height="200" frameborder="0"
                                            style="border-radius: 4px;"
                                            src="https://www.google.com/maps/embed/v1/place?q={{ $ad_details->cityInfo->name }}&key=AIzaSyBz0OQItk9_cip0UiVTR7NL9lL9oR5VR_Q"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __tr('Contact Information') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Contact Email') }}</label>
                                            <input type="email" name="contact_email" class="form-control"
                                                value="{{ $ad_details->contact_email }}"
                                                placeholder="{{ __tr('Enter email') }}">
                                            @if ($errors->has('contact_email'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('contact_email') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __tr('Contact Phone') }}</label>
                                            <input type="text" name="contact_phone" class="form-control"
                                                value="{{ $ad_details->contact_phone }}"
                                                placeholder="{{ __tr('Enter phone') }}">
                                            @if ($errors->has('contact_phone'))
                                                <div class="error text-danger mb-0 invalid-input">
                                                    {{ $errors->first('contact_phone') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Hide Phone Number') }}</label>
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox" name="contact_is_hide" class="custom-control-input"
                                            id="phoneVisibleSwitch" @checked($ad_details->contact_is_hide == config('settings.general_status.active'))>
                                        <label class="custom-control-label" for="phoneVisibleSwitch"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        @if ($ad_details->customFields() && count($ad_details->customFields()) > 0)
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __tr('Custom Fields') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($ad_details->customFields() as $cf)
                                            @php
                                                $field = \App\Models\AdsCustomField::with(['options'])->find(
                                                    $cf['flied_id'],
                                                );
                                            @endphp
                                            @if ($field != null)
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ $field->title }}</label>
                                                        @if ($cf['type'] == config('settings.input_types.text'))
                                                            <input type="text"
                                                                name="custom_field[{{ $field->id }}]"
                                                                class="form-control" value="{{ $cf['value'] }}"
                                                                placeholder="{{ __tr('Enter') }} {{ $field->title }}">
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.number'))
                                                            <input type="number"
                                                                name="custom_field[{{ $field->id }}]"
                                                                class="form-control" value="{{ $cf['value'] }}"
                                                                placeholder="{{ __tr('Enter') }} {{ $field->title }}">
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.text_area'))
                                                            <textarea name="custom_field[{{ $field->id }}]" class="form-control" rows="2">{{ $cf['value'] }}</textarea>
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.date'))
                                                            <input type="date"
                                                                name="custom_field[{{ $field->id }}]"
                                                                class="form-control" value="{{ $cf['value'] }}">
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.select'))
                                                            <select name="custom_field[{{ $field->id }}]"
                                                                class="form-control">
                                                                <option value="">{{ __tr('Select') }}
                                                                </option>
                                                                @foreach ($field->options as $option)
                                                                    <option value="{{ $option->id }}"
                                                                        @selected($option->id == $cf['value'])>
                                                                        {{ $option->value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.radio'))
                                                            <div>
                                                                @foreach ($field->options as $option)
                                                                    <div
                                                                        class="custom-control custom-radio custom-control-inline">
                                                                        <input type="radio"
                                                                            id="cf_{{ $field->id }}_{{ $option->id }}"
                                                                            name="custom_field[{{ $field->id }}]"
                                                                            class="custom-control-input"
                                                                            value="{{ $option->id }}"
                                                                            @checked($option->id == $cf['value'])>
                                                                        <label class="custom-control-label"
                                                                            for="cf_{{ $field->id }}_{{ $option->id }}">{{ $option->value }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.checkbox'))
                                                            <div>
                                                                @foreach ($field->options as $option)
                                                                    <div
                                                                        class="custom-control custom-checkbox custom-control-inline">
                                                                        <input type="checkbox"
                                                                            id="cf_{{ $field->id }}_{{ $option->id }}"
                                                                            name="custom_field[{{ $field->id }}][]"
                                                                            class="custom-control-input"
                                                                            value="{{ $option->id }}"
                                                                            @checked(is_array($cf['value']) && in_array($option->id, $cf['value']))>
                                                                        <label class="custom-control-label"
                                                                            for="cf_{{ $field->id }}_{{ $option->id }}">{{ $option->value }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if ($cf['type'] == config('settings.input_types.file'))
                                                            <x-media name="customfile_{{ $field->id }}"
                                                                :value="$cf['value']"></x-media>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-3 col-12">
                        <!-- Status & Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Featured Ad') }}</label>
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox" name="is_featured" class="custom-control-input"
                                            id="featuredSwitch" @checked($ad_details->is_featured == config('settings.general_status.active'))>
                                        <label class="custom-control-label" for="featuredSwitch"></label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Thumbnail Image') }}</label>
                                    <x-media name="thumbnail_image" :value="$ad_details->thumbnail_image"></x-media>
                                    @if ($errors->has('thumbnail_image'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('thumbnail_image') }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Gallery Images') }}</label>
                                    @php
                                        $galleryImages = $ad_details->galleryImages
                                            ? $ad_details->galleryImages->pluck('image_id')->implode(',')
                                            : '';
                                    @endphp
                                    <x-media name="gallery_images" :value="$galleryImages" :multiple="true"></x-media>
                                    @if ($errors->has('gallery_images'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('gallery_images') }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Tags') }}</label>
                                    <select class="tag-select form-control" name="tags[]" multiple>
                                        @foreach ($ad_details->tags as $tag)
                                            <option value="{{ $tag->id }}" selected>{{ $tag->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr>

                                <!-- Ad Status Info -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">{{ __tr('Status') }}:</small>
                                        <span
                                            class="badge {{ $ad_details->status == config('settings.general_status.active') ? 'badge-success' : 'badge-warning' }}">
                                            {{ $ad_details->status == config('settings.general_status.active') ? __tr('Published') : __tr('Pending') }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">{{ __tr('Payment') }}:</small>
                                        <span
                                            class="badge {{ $ad_details->payment_status == config('settings.general_status.active') ? 'badge-success' : 'badge-danger' }}">
                                            {{ $ad_details->payment_status == config('settings.general_status.active') ? __tr('Paid') : __tr('Unpaid') }}
                                        </span>
                                    </div>
                                    @if ($ad_details->userInfo)
                                        <div class="d-flex justify-content-between mb-2">
                                            <small class="text-muted">{{ __tr('Owner') }}:</small>
                                            <small>{{ $ad_details->userInfo->name }}</small>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">{{ __tr('Created') }}:</small>
                                        <small>{{ $ad_details->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" name="status"
                                        value="{{ config('settings.general_status.in_active') }}"
                                        class="btn btn-secondary">{{ __tr('Save & Pending') }}</button>
                                    <button type="submit" name="status"
                                        value="{{ config('settings.general_status.active') }}"
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

            // Summernote Init
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
                    onImageUpload: function(images) {
                        sendFile(images[0], 'contentSummernote');
                    }
                }
            });

            // Image insert function in summernote
            function sendFile(image, section_id) {
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

            // Category Select
            $('.category-select').select2({
                theme: "bootstrap4",
                placeholder: '{{ __tr('Select category') }}',
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

            // Tags Select
            $('.tag-select').select2({
                theme: "bootstrap4",
                tags: true,
                placeholder: '{{ __tr('Select or create tags') }}',
                ajax: {
                    url: '{{ route('classified.ads.tag.options') }}',
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

            // Location dropdowns - cascading selection
            $('#country-select').on('change', function() {
                var countryId = $(this).val();
                var stateSelect = $('#state-select');
                var citySelect = $('#city-select');

                stateSelect.html('<option value="">{{ __tr('Select State') }}</option>');
                citySelect.html('<option value="">{{ __tr('Select City') }}</option>');

                if (countryId) {
                    $.ajax({
                        url: '{{ route('classified.ads.get.states') }}',
                        type: 'GET',
                        data: {
                            country_id: countryId
                        },
                        success: function(data) {
                            $.each(data, function(index, state) {
                                stateSelect.append('<option value="' + state.id + '">' +
                                    state.name + '</option>');
                            });
                        }
                    });
                }
            });

            $('#state-select').on('change', function() {
                var stateId = $(this).val();
                var citySelect = $('#city-select');

                citySelect.html('<option value="">{{ __tr('Select City') }}</option>');

                if (stateId) {
                    $.ajax({
                        url: '{{ route('classified.ads.get.cities') }}',
                        type: 'GET',
                        data: {
                            state_id: stateId
                        },
                        success: function(data) {
                            $.each(data, function(index, city) {
                                citySelect.append('<option value="' + city.id + '">' + city
                                    .name + '</option>');
                            });
                        }
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
