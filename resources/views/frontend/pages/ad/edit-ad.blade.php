@extends('frontend.layouts.master')
@section('meta')
    <title>{{ __tr('Edit Ad') }} - {{ get_setting('site_name') }}</title>
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.css') }}">
@endsection
@section('content')
    <div class="pf-wrapper">

        {{-- ── Step Progress Header ── --}}
        <div class="pf-stepper-wrap">
            <div class="container">
                <div class="pf-stepper-inner">
                    <div class="stepIndicator active">
                        <div class="si-circle">1</div>
                        <div class="si-label">
                            <span class="si-title">{{ __tr('Item Details') }}</span>
                            <span class="si-sub">{{ __tr('Info & Media') }}</span>
                        </div>
                    </div>
                    <div class="pf-step-divider"></div>
                    <div class="stepIndicator">
                        <div class="si-circle">2</div>
                        <div class="si-label">
                            <span class="si-title">{{ __tr('Location & More') }}</span>
                            <span class="si-sub">{{ __tr('Where & Tags') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Session Alerts ── --}}
        @if (session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <form action="{{ route('member.ad.update', $ad->uid) }}" method="POST" enctype="multipart/form-data"
            id="ad-edit-form">
            @csrf

            {{-- ══════════════════════════════════════════
             STEP 1 — Item Details & Media
             ══════════════════════════════════════════ --}}
            <div class="tab-pane step active show" id="listing-info" role="tabpanel">
                <div class="container">

                    <div class="pf-page-title mb-4">
                        <a href="{{ route('member.my.listings') }}" class="pf-back-link">
                            <i class="fas fa-arrow-left"></i>
                            {{ __tr('Back to My Ads') }}
                        </a>
                        <h2>{{ __tr('Edit Your Ad') }}</h2>
                        <p>{{ __tr('Update the details below to modify your listing') }}</p>
                    </div>

                    <div class="row g-4">

                        {{-- ── Left Column ── --}}
                        <div class="col-lg-8">

                            {{-- Listing Info --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-blue">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Listing Information') }}</h5>
                                        <p>{{ __tr('Title, category and condition') }}</p>
                                    </div>
                                </div>
                                <div class="pf-card-bd">

                                    <div class="form-group mb-3">
                                        <label for="title">{{ __tr('Item Name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="title" id="title"
                                            value="{{ old('title', $ad->title) }}"
                                            class="input-style w-100 @error('title') is-invalid @enderror"
                                            placeholder="{{ __tr('Item Name') }}">
                                        <div class="invalid-feedback @error('title') d-block @enderror">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    @php
                                        $pickerInitialText = __tr('Click to select a category');
                                        $pickerHasSelection = false;
                                        if ($categoryHierarchy['category']) {
                                            $selectedCat = $categories->firstWhere(
                                                'id',
                                                $categoryHierarchy['category'],
                                            );
                                            if ($selectedCat) {
                                                $pickerInitialText = $selectedCat->title;
                                                $pickerHasSelection = true;
                                            }
                                        }
                                    @endphp
                                    <div class="form-group mb-3">
                                        <label>{{ __tr('Category') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="cat-picker-trigger {{ $pickerHasSelection ? 'has-selection' : '' }}"
                                            id="cat-picker-trigger" role="button">
                                            <div class="cat-picker-inner">
                                                <i class="fas fa-th-large cat-picker-icon"></i>
                                                <span id="cat-picker-text"
                                                    data-placeholder="{{ __tr('Click to select a category') }}"
                                                    @if ($pickerHasSelection) style="color:#1a1a2e;font-weight:500;" @endif>{{ $pickerInitialText }}</span>
                                            </div>
                                            <i class="fas fa-chevron-right cat-picker-arrow"></i>
                                        </div>
                                        <input type="hidden" name="category" id="final-category"
                                            value="{{ old('category', $ad->category_id) }}">
                                        <div class="invalid-feedback @error('category') d-block @enderror">
                                            @error('category')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div id="custom-fields-container" class="mt-1"></div>

                                    <div class="form-group mb-0">
                                        <label for="condition">{{ __tr('Item Condition') }}</label>
                                        <select name="condition" id="condition" class="input-style w-100">
                                            <option value="">{{ __tr('Select Condition') }}</option>
                                            @foreach ($conditions as $condition)
                                                <option value="{{ $condition->id }}"
                                                    {{ old('condition', $ad->condition_id) == $condition->id ? 'selected' : '' }}>
                                                    {{ $condition->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-green">
                                        <i class="fas fa-align-left"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Description') }}</h5>
                                        <p>{{ __tr('Minimum 150 characters') }}</p>
                                    </div>
                                </div>
                                <div class="pf-card-bd">
                                    <div class="form-group mb-0">
                                        <label for="description">{{ __tr('Item Description') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="description" id="description" rows="6"
                                            class="input-style w-100 textarea--form summernote @error('description') is-invalid @enderror"
                                            placeholder="{{ __tr('Enter a detailed description...') }}">{{ old('description', $ad->description) }}</textarea>
                                        <div class="invalid-feedback @error('description') d-block @enderror">
                                            @error('description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- /Left Column --}}

                        {{-- ── Right Column ── --}}
                        <div class="col-lg-4">

                            {{-- Pricing --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-orange">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Pricing') }}</h5>
                                    </div>
                                </div>
                                <div class="pf-card-bd">
                                    <div class="form-group mb-3">
                                        <label for="price">{{ __tr('Price') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="price" id="price"
                                            value="{{ old('price', $ad->price) }}"
                                            class="input-style w-100 @error('price') is-invalid @enderror"
                                            placeholder="0.00" step="0.01">
                                        <div class="invalid-feedback @error('price') d-block @enderror">
                                            @error('price')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <label class="pf-chk">
                                        <input type="checkbox" class="custom-check-box" name="negotiable"
                                            id="negotiable"
                                            {{ old('negotiable', $ad->is_negotiable == config('settings.general_status.active')) ? 'checked' : '' }}>
                                        <span>{{ __tr('Price is Negotiable') }}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Media Uploads --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-purple">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Media Uploads') }}</h5>
                                        <p>{{ __tr('Photos of your item') }}</p>
                                    </div>
                                </div>
                                <div class="pf-card-bd">

                                    {{-- Thumbnail --}}
                                    <div class="form-group mb-3">
                                        <label>{{ __tr('Featured Image') }}</label>
                                        <div class="thumbnail-slot {{ $ad->thumbnail_image ? 'has-image' : '' }}"
                                            id="thumbnail-slot">
                                            <div class="slot-placeholder"
                                                {{ $ad->thumbnail_image ? 'style=display:none;' : '' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>{{ __tr('Choose Featured Image') }}</span>
                                            </div>
                                            <div class="slot-image" id="thumbnail-slot-image"
                                                {{ $ad->thumbnail_image ? '' : 'style=display:none;' }}>
                                                @if ($ad->thumbnail_image)
                                                    <img src="{{ asset(getFilePath($ad->thumbnail_image)) }}"
                                                        alt="">
                                                @else
                                                    <img src="" alt="">
                                                @endif
                                                <button type="button" class="slot-remove" id="thumbnail-remove"
                                                    title="Remove">&times;</button>
                                            </div>
                                            <input type="file" name="thumbnail_image" id="thumbnail_image"
                                                class="slot-file-input @error('thumbnail_image') is-invalid @enderror"
                                                accept="image/jpg,image/jpeg,image/png,image/gif,image/webp">
                                        </div>
                                        <small
                                            class="text-muted d-block mt-2">{{ __tr('Leave empty to keep current image — max 5MB') }}</small>
                                        <div class="invalid-feedback @error('thumbnail_image') d-block @enderror">
                                            @error('thumbnail_image')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Gallery --}}
                                    <div class="form-group mb-0">
                                        @php $existingImages = $ad->galleryImages ?? collect(); @endphp
                                        <div class="gallery-slots-label">
                                            <span>{{ __tr('Gallery Images') }}</span>
                                            @if ($galleryImageLimit > 0)
                                                <span class="slot-count-badge">{{ $galleryImageLimit }}
                                                    {{ __tr('photos') }}</span>
                                            @endif
                                        </div>

                                        @if ($galleryImageLimit > 0)
                                            <div class="gallery-slots-grid" id="gallery-slots-grid">
                                                @for ($i = 0; $i < $galleryImageLimit; $i++)
                                                    @php $existingImage = $existingImages->get($i); @endphp
                                                    <div class="gallery-slot {{ $existingImage ? 'has-image' : '' }}"
                                                        data-slot="{{ $i }}"
                                                        @if ($existingImage) data-existing-id="{{ $existingImage->id }}" @endif>
                                                        <div class="slot-placeholder"
                                                            {{ $existingImage ? 'style=display:none;' : '' }}>
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1.5"
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            <span>{{ __tr('Add Photo') }}</span>
                                                        </div>
                                                        <div class="slot-image"
                                                            {{ $existingImage ? '' : 'style=display:none;' }}>
                                                            @if ($existingImage)
                                                                <img src="{{ asset(getFilePath($existingImage->image_path)) }}"
                                                                    alt="">
                                                            @else
                                                                <img src="" alt="">
                                                            @endif
                                                            <button type="button" class="slot-remove"
                                                                title="Remove">&times;</button>
                                                        </div>
                                                        <span class="slot-number">{{ $i + 1 }}</span>
                                                        <input type="file" class="slot-file-input"
                                                            accept="image/jpg,image/jpeg,image/png,image/gif,image/webp">
                                                    </div>
                                                @endfor
                                            </div>
                                            <small
                                                class="text-muted d-block mt-2">{{ __tr('Click a slot to add or replace a photo') }}
                                                &bull; {{ __tr('max 5MB each') }}</small>
                                        @else
                                            <div class="gallery-no-plan-notice">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ __tr('Your current plan does not include gallery images.') }}
                                                    <a
                                                        href="{{ route('pricing.plans') }}">{{ __tr('Upgrade your plan') }}</a>
                                                    {{ __tr('to upload gallery photos.') }}
                                                </span>
                                            </div>
                                        @endif

                                        <input type="hidden" name="deleted_gallery_images" id="deleted_gallery_images"
                                            value="[]">
                                    </div>

                                </div>
                            </div>

                            {{-- Continue Button --}}
                            <div class="pf-nav-btns">
                                <button class="pf-btn-next" id="nextBtn" type="button">
                                    {{ __tr('Continue') }}
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                        </div>
                        {{-- /Right Column --}}

                    </div>
                </div>
            </div>
            {{-- /Step 1 --}}

            {{-- ══════════════════════════════════════════
             STEP 2 — Location & Additional Info
             ══════════════════════════════════════════ --}}
            <div class="tab-pane step" id="media-uploads" role="tabpanel">
                <div class="container">

                    <div class="pf-page-title mb-4">
                        <h2>{{ __tr('Location & Details') }}</h2>
                        <p>{{ __tr('Where is the item and any extra details') }}</p>
                    </div>

                    <div class="row g-4">

                        {{-- ── Left Column ── --}}
                        <div class="col-lg-8">

                            {{-- Location --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-indigo">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Location Information') }}</h5>
                                        <p>{{ __tr('Where is the item located?') }}</p>
                                    </div>
                                </div>
                                <div class="pf-card-bd">

                                    @if ($countriesCount > 1)
                                        <div class="form-group mb-3">
                                            <label for="country">{{ __tr('Country') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="country" id="country" class="select2-ajax w-100" required>
                                                <option value="">{{ __tr('Select Country') }}</option>
                                                @if ($ad->countryInfo)
                                                    <option value="{{ $ad->country_id }}" selected>
                                                        {{ $ad->countryInfo->name }}</option>
                                                @endif
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    @else
                                        <input type="hidden" name="country" id="country"
                                            value="{{ $singleCountry->id }}">
                                    @endif

                                    <div class="form-group mb-3">
                                        <label for="state">{{ __tr('State / Province') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="state" id="state" class="select2-ajax w-100" required>
                                            <option value="">{{ __tr('Select State') }}</option>
                                            @if ($ad->stateInfo)
                                                <option value="{{ $ad->state_id }}" selected>{{ $ad->stateInfo->name }}
                                                </option>
                                            @endif
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="city">{{ __tr('City') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="city" id="city" class="select2-ajax w-100" required>
                                            <option value="">{{ __tr('Select City') }}</option>
                                            @if ($ad->cityInfo)
                                                <option value="{{ $ad->city_id }}" selected>{{ $ad->cityInfo->name }}
                                                </option>
                                            @endif
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="address">{{ __tr('Address') }}</label>
                                        <textarea class="w-100 input-style" name="address" id="address" rows="3"
                                            placeholder="{{ __tr('Street, area or landmark...') }}">{{ old('address', $ad->address) }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                </div>
                            </div>

                            {{-- Video --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-rose">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Video') }}</h5>
                                        <p>{{ __tr('Optional YouTube or Vimeo link') }}</p>
                                    </div>
                                </div>
                                <div class="pf-card-bd">
                                    <div class="form-group mb-0">
                                        <label for="video_url">{{ __tr('Video URL') }}</label>
                                        <input type="text" class="input-style w-100" name="video_url" id="video_url"
                                            value="{{ old('video_url', $ad->video_url) }}"
                                            placeholder="{{ __tr('https://youtube.com/watch?v=...') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- /Left Column --}}

                        {{-- ── Right Column ── --}}
                        <div class="col-lg-4">

                            {{-- Contact Info --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-teal">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Contact Info') }}</h5>
                                    </div>
                                </div>
                                <div class="pf-card-bd">
                                    <div class="form-group mb-3">
                                        <label for="contact_email">{{ __tr('Contact Email') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="contact_email" id="contact_email"
                                            value="{{ old('contact_email', $ad->contact_email) }}"
                                            class="input-style w-100 @error('contact_email') is-invalid @enderror"
                                            placeholder="{{ __tr('Email Address') }}">
                                        <div class="invalid-feedback @error('contact_email') d-block @enderror">
                                            @error('contact_email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <label class="pf-chk mb-3 d-flex">
                                        <input type="checkbox" class="custom-check-box" name="hide_phone_number"
                                            {{ old('hide_phone_number', $ad->contact_is_hide == config('settings.general_status.active')) ? 'checked' : '' }}>
                                        <span>{{ __tr('Hide My Phone Number') }}</span>
                                    </label>
                                    <div class="form-group mb-0">
                                        <label for="phone">{{ __tr('Phone Number') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input class="input-style w-100 @error('phone') is-invalid @enderror"
                                            type="tel" name="phone" id="phone"
                                            value="{{ old('phone', $ad->contact_phone) }}"
                                            placeholder="{{ __tr('Type Phone') }}">
                                        <div class="invalid-feedback @error('phone') d-block @enderror">
                                            @error('phone')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Feature Ad --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-amber">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Feature This Ad') }}</h5>
                                    </div>
                                </div>
                                <div class="pf-card-bd">
                                    <label class="pf-chk mb-2">
                                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                            class="custom-check-box feature_disable_color" @checked(old('is_featured', $ad->is_featured == config('settings.general_status.active')))>
                                        <span>{{ __tr('Feature This Ad') }}</span>
                                    </label>
                                    <p class="text-muted mb-0" style="font-size:12px;">
                                        {{ __tr('Requires a') }}
                                        <a href="{{ url('/membership') }}"
                                            class="text-primary">{{ __tr('paid membership') }}</a>
                                    </p>
                                </div>
                            </div>

                            {{-- Tags --}}
                            <div class="pf-card">
                                <div class="pf-card-hd">
                                    <div class="pf-icon pf-icon-teal">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div>
                                        <h5>{{ __tr('Tags') }}</h5>
                                    </div>
                                </div>
                                <div class="pf-card-bd">
                                    <div class="form-group mb-0">
                                        <div class="select-items">
                                            <select name="tags[]" id="tags" class="select2_activation" multiple
                                                data-placeholder="{{ __tr('Select or type tags...') }}">
                                                @foreach ($tags as $tag)
                                                    <option value="{{ $tag->id }}"
                                                        {{ in_array($tag->id, $selectedTagIds) ? 'selected' : '' }}>
                                                        {{ $tag->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{ __tr('Select or type a new tag') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Messages --}}
                            <div id="form-messages" class="mb-3"></div>

                            {{-- Prev / Submit --}}
                            <div class="pf-nav-btns">
                                <button class="pf-btn-prev" id="prevBtn" type="button">
                                    <i class="fas fa-arrow-left"></i>
                                    {{ __tr('Previous') }}
                                </button>
                                <button class="pf-btn-next" id="submitBtn" type="submit">
                                    <span class="btn-text">{{ __tr('Update Listing') }}</span>
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>

                        </div>
                        {{-- /Right Column --}}

                    </div>
                </div>
            </div>
            {{-- /Step 2 --}}

        </form>

    </div>

    {{-- ── Category Selection Modal ── --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
            <div class="modal-content" style="border-radius:16px;border:none;">
                <div class="modal-header" style="border-bottom:1px solid #f1f5f9;padding:18px 22px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="pf-icon pf-icon-blue" style="width:32px;height:32px;border-radius:8px;">
                            <i class="fas fa-th-large" style="font-size:13px;"></i>
                        </div>
                        <h5 class="modal-title mb-0" id="categoryModalLabel" style="font-size:15px;font-weight:700;">
                            {{ __tr('Select Category') }}</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:22px;">

                    <div class="form-group mb-3">
                        <label for="select-category"
                            style="font-size:13px;font-weight:600;margin-bottom:6px;">{{ __tr('Category') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select id="select-category" class="input-style w-100">
                            <option value="">{{ __tr('Select Category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $categoryHierarchy['category'] == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3" id="subcategory-wrapper"
                        style="{{ $categoryHierarchy['subcategory'] ? '' : 'display:none;' }}">
                        <label for="select-subcategory"
                            style="font-size:13px;font-weight:600;margin-bottom:6px;">{{ __tr('Subcategory') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select id="select-subcategory" class="input-style w-100">
                            <option value="">{{ __tr('Select Subcategory') }}</option>
                        </select>
                    </div>

                    <div class="form-group mb-0" id="sub-subcategory-wrapper"
                        style="{{ $categoryHierarchy['subSubcategory'] ? '' : 'display:none;' }}">
                        <label for="select-sub-subcategory"
                            style="font-size:13px;font-weight:600;margin-bottom:6px;">{{ __tr('Sub Subcategory') }}</label>
                        <select id="select-sub-subcategory" class="input-style w-100">
                            <option value="">{{ __tr('Select Sub Subcategory') }}</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:14px 22px;">
                    <button type="button" class="pf-btn-next" id="categoryModalDone"
                        data-bs-dismiss="modal">{{ __tr('Done') }} <i class="fas fa-check ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Loader --}}
    <div id="form-loader" style="display:none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ __tr('Loading...') }}</span>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('public/web-assets/backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Category hierarchy from server
            const categoryHierarchy = {
                category: {{ $categoryHierarchy['category'] ?? 'null' }},
                subcategory: {{ $categoryHierarchy['subcategory'] ?? 'null' }},
                subSubcategory: {{ $categoryHierarchy['subSubcategory'] ?? 'null' }}
            };

            // Existing custom field values from server
            const customFieldValues = @json($customFieldValues ?? []);

            // Initialize Select2 for tags
            if ($.fn.select2) {
                $('.select2_activation').select2({
                    tags: true,
                    tokenSeparators: [','],
                    placeholder: $('.select2_activation').data('placeholder') ||
                        '{{ __tr('Select or type tags...') }}'
                });
            }

            // Initialize Select2 for Country with ajax
            @if ($countriesCount > 1)
                $('#country').select2({
                    ajax: {
                        url: "{{ route('ad.countries') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Select Country',
                    minimumInputLength: 0,
                    allowClear: true
                });
            @endif

            // Initialize Select2 for State with ajax
            $('#state').select2({
                ajax: {
                    url: "{{ route('ad.states') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            country_id: $('#country').val()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                placeholder: 'Select State',
                minimumInputLength: 0,
                allowClear: true
            });

            // Initialize Select2 for City with ajax
            $('#city').select2({
                ajax: {
                    url: "{{ route('ad.cities') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            state_id: $('#state').val()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                placeholder: 'Select City',
                minimumInputLength: 0,
                allowClear: true,
            });

            // Handle Country change - enable state and reset
            $('#country').on('change', function() {
                $('#state').prop('disabled', false).val(null).trigger('change');
                $('#city').prop('disabled', true).val(null).trigger('change');
            });

            // Handle State change - enable city and reset
            $('#state').on('change', function() {
                $('#city').prop('disabled', false).val(null).trigger('change');
            });

            // Initialize Summernote
            $('.summernote').summernote({
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
            });

            // ============================================
            // Multi-level Category Selection
            // ============================================
            const categorySelect = $('#select-category');
            const subcategorySelect = $('#select-subcategory');
            const subSubcategorySelect = $('#select-sub-subcategory');
            const subcategoryWrapper = $('#subcategory-wrapper');
            const subSubcategoryWrapper = $('#sub-subcategory-wrapper');
            const finalCategoryInput = $('#final-category');
            const breadcrumb = $('#category-breadcrumb');

            function updateFinalCategory() {
                let finalVal = '';
                let path = [];

                if (subSubcategorySelect.val()) {
                    finalVal = subSubcategorySelect.val();
                    path = [
                        categorySelect.find('option:selected').text().trim(),
                        subcategorySelect.find('option:selected').text().trim(),
                        subSubcategorySelect.find('option:selected').text().trim()
                    ];
                } else if (subcategorySelect.val()) {
                    finalVal = subcategorySelect.val();
                    path = [
                        categorySelect.find('option:selected').text().trim(),
                        subcategorySelect.find('option:selected').text().trim()
                    ];
                } else if (categorySelect.val()) {
                    finalVal = categorySelect.val();
                    path = [categorySelect.find('option:selected').text().trim()];
                }

                finalCategoryInput.val(finalVal);
                breadcrumb.text(path.length ? path.join(' > ') : '');

                // Update category picker trigger display
                const $pickerText = $('#cat-picker-text');
                const $pickerTrigger = $('#cat-picker-trigger');
                if (path.length) {
                    $pickerText.text(path.join(' > ')).css({
                        'color': '#1a1a2e',
                        'font-weight': '500'
                    });
                    $pickerTrigger.addClass('has-selection');
                } else {
                    $pickerText.text($pickerText.data('placeholder')).css({
                        'color': '',
                        'font-weight': ''
                    });
                    $pickerTrigger.removeClass('has-selection');
                }

                // Load custom fields for the selected category
                if (finalVal) {
                    loadCustomFields(finalVal);
                } else {
                    $('#custom-fields-container').html('');
                }
            }

            // Category change -> load subcategories
            categorySelect.on('change', function() {
                const parentId = $(this).val();
                subcategoryWrapper.hide();
                subSubcategoryWrapper.hide();
                subcategorySelect.html('<option value="">Select Subcategory</option>');
                subSubcategorySelect.html('<option value="">Select Sub Subcategory</option>');

                if (parentId) {
                    $.get("{{ route('ad.subcategories') }}", {
                        parent_id: parentId
                    }, function(data) {
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                const selected = categoryHierarchy.subcategory == item
                                    .id ? 'selected' : '';
                                subcategorySelect.append(
                                    `<option value="${item.id}" ${selected}>${item.title}</option>`
                                );
                            });
                            subcategoryWrapper.show();

                            // Trigger change if we have a pre-selected subcategory
                            if (categoryHierarchy.subcategory) {
                                subcategorySelect.trigger('change');
                            }
                        } else {
                            // No subcategories — leaf level, close modal
                            closeCategoryModal();
                        }
                    });
                }
                updateFinalCategory();
            });

            // Subcategory change -> load sub-subcategories
            subcategorySelect.on('change', function() {
                const parentId = $(this).val();
                subSubcategoryWrapper.hide();
                subSubcategorySelect.html('<option value="">Select Sub Subcategory</option>');

                if (parentId) {
                    $.get("{{ route('ad.subcategories') }}", {
                        parent_id: parentId
                    }, function(data) {
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                const selected = categoryHierarchy.subSubcategory ==
                                    item.id ? 'selected' : '';
                                subSubcategorySelect.append(
                                    `<option value="${item.id}" ${selected}>${item.title}</option>`
                                );
                            });
                            subSubcategoryWrapper.show();
                        } else {
                            // No sub-subcategories — leaf level, close modal
                            closeCategoryModal();
                        }
                    });
                }
                updateFinalCategory();
            });

            subSubcategorySelect.on('change', function() {
                updateFinalCategory();
                // Sub-subcategory selected — close modal
                if ($(this).val()) {
                    closeCategoryModal();
                }
            });

            // Trigger initial category load if we have a pre-selected category
            if (categoryHierarchy.category) {
                categorySelect.trigger('change');
            }

            // ============================================
            // Load Custom Fields by Category
            // ============================================
            function loadCustomFields(categoryId) {
                $.get("{{ route('ad.custom.fields') }}", {
                    category_id: categoryId
                }, function(fields) {
                    let html = '';
                    if (fields.length > 0) {
                        html += '<h6 class="mt-3 mb-2">Additional Information</h6>';
                        html += '<div class="row g-3">';
                        fields.forEach(function(field) {
                            const required = field.is_required == 1 ? 'required' : '';
                            const requiredStar = field.is_required == 1 ?
                                '<span class="text-danger">*</span>' : '';

                            // Get existing value for this field
                            const existingValue = customFieldValues[field.id] || field
                                .default_value || '';

                            html += '<div class="col-sm-6 custom-field-group">';
                            html += `<label>${field.title} ${requiredStar}</label>`;

                            switch (parseInt(field.type)) {
                                case {{ config('settings.input_types.text') }}:
                                    html +=
                                        `<input type="text" name="custom_field[${field.id}]" class="input-style w-100" value="${existingValue}" ${required}>`;
                                    break;
                                case {{ config('settings.input_types.number') }}:
                                    html +=
                                        `<input type="number" name="custom_field[${field.id}]" class="input-style w-100" value="${existingValue}" ${required}>`;
                                    break;
                                case {{ config('settings.input_types.select') }}:
                                    html +=
                                        `<select name="custom_field[${field.id}]" class="input-style w-100" ${required}>`;
                                    html += '<option value="">Select</option>';
                                    if (field.options) {
                                        field.options.forEach(function(opt) {
                                            const selected = existingValue == opt.id ?
                                                'selected' : '';
                                            html +=
                                                `<option value="${opt.id}" ${selected}>${opt.value}</option>`;
                                        });
                                    }
                                    html += '</select>';
                                    break;
                                case {{ config('settings.input_types.text_area') }}:
                                    html +=
                                        `<textarea name="custom_field[${field.id}]" class="input-style w-100" rows="3" ${required}>${existingValue}</textarea>`;
                                    break;
                                case {{ config('settings.input_types.checkbox') }}:
                                    if (field.options) {
                                        // Handle checkbox arrays
                                        const checkedValues = Array.isArray(existingValue) ?
                                            existingValue : (existingValue ? [existingValue] : []);
                                        field.options.forEach(function(opt) {
                                            const checked = checkedValues.includes(opt.id
                                                .toString()) || checkedValues.includes(
                                                opt.id) ? 'checked' : '';
                                            html += `<div class="form-check">
                                                <input type="checkbox" name="custom_field[${field.id}][]" value="${opt.id}" class="form-check-input" id="cf_${field.id}_${opt.id}" ${checked}>
                                                <label class="form-check-label" for="cf_${field.id}_${opt.id}">${opt.value}</label>
                                            </div>`;
                                        });
                                    }
                                    break;
                                case {{ config('settings.input_types.radio') }}:
                                    if (field.options) {
                                        field.options.forEach(function(opt) {
                                            const checked = existingValue == opt.id ?
                                                'checked' : '';
                                            html += `<div class="form-check">
                                                <input type="radio" name="custom_field[${field.id}]" value="${opt.id}" class="form-check-input" id="cf_${field.id}_${opt.id}" ${required} ${checked}>
                                                <label class="form-check-label" for="cf_${field.id}_${opt.id}">${opt.value}</label>
                                            </div>`;
                                        });
                                    }
                                    break;
                                case {{ config('settings.input_types.file') }}:
                                    html +=
                                        `<input type="file" name="customfile_${field.id}" class="input-style w-100">`;
                                    if (existingValue) {
                                        html +=
                                            `<small class="text-muted d-block mt-1">Current file: ${existingValue.split('/').pop()}</small>`;
                                    }
                                    break;
                                case {{ config('settings.input_types.date') }}:
                                    html +=
                                        `<input type="date" name="custom_field[${field.id}]" class="input-style w-100" value="${existingValue}" ${required}>`;
                                    break;
                            }

                            html += '</div>';
                        });
                        html += '</div>';
                    }
                    $('#custom-fields-container').html(html);
                });
            }

            // ============================================
            // Form Wizard (Step Navigation)
            // ============================================
            let currentTab = 0;
            const tabs = document.querySelectorAll('.step');
            const indicators = document.querySelectorAll('.stepIndicator');

            function showTab(n) {
                tabs.forEach((tab, i) => {
                    if (i === n) {
                        tab.classList.add('active', 'show');
                        tab.style.display = 'block';
                    } else {
                        tab.classList.remove('active', 'show');
                        tab.style.display = 'none';
                    }
                });
                indicators.forEach((ind, i) => {
                    ind.classList.toggle('active', i === n);
                });
            }

            showTab(currentTab);

            // ── Category Modal (pure jQuery, no Bootstrap JS API) ──
            const $catModalEl = $('#categoryModal');

            function openCategoryModal() {
                $catModalEl.css('display', 'block').addClass('show');
                $('body').addClass('modal-open').css('overflow', 'hidden');
                if (!$('.modal-backdrop').length) {
                    $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                }
            }

            function closeCategoryModal() {
                $catModalEl.css('display', 'none').removeClass('show');
                $('body').removeClass('modal-open').css('overflow', '');
                $('.modal-backdrop').remove();
            }

            $('#cat-picker-trigger').on('click', openCategoryModal);

            $(document).on('click', '#categoryModalDone, #categoryModal [data-bs-dismiss="modal"]',
                closeCategoryModal);

            $(document).on('click', '.modal-backdrop', closeCategoryModal);

            $('#nextBtn').on('click', function() {
                // Validate step 1 fields before proceeding
                let isValid = true;

                // Clear previous errors
                $('.invalid-feedback').removeClass('d-block').text('');
                $('.is-invalid').removeClass('is-invalid');

                // Validate title
                const title = $('#title').val().trim();
                if (!title) {
                    isValid = false;
                    $('#title').addClass('is-invalid');
                    $('#title').siblings('.invalid-feedback').text('Item name is required').addClass(
                        'd-block');
                }

                // Validate category
                const category = $('#final-category').val();
                if (!category) {
                    isValid = false;
                    $('#final-category').addClass('is-invalid');
                    $('#final-category').siblings('.invalid-feedback').text('Please select a category')
                        .addClass('d-block');
                }

                // Validate description
                const description = $('.summernote').summernote('code').replace(/<[^>]*>/g, '').trim();
                if (!description || description.length < 150) {
                    isValid = false;
                    $('#description').addClass('is-invalid');
                    $('#description').siblings('.invalid-feedback').text(
                        !description ? 'Description is required' :
                        'Description must be at least 150 characters'
                    ).addClass('d-block');
                }

                // Validate price
                const price = $('#price').val();
                if (!price || parseFloat(price) < 0) {
                    isValid = false;
                    $('#price').addClass('is-invalid');
                    $('#price').siblings('.invalid-feedback').text(
                        !price ? 'Price is required' : 'Price cannot be negative'
                    ).addClass('d-block');
                }

                // Validate contact email
                const email = $('#contact_email').val().trim();
                if (!email) {
                    isValid = false;
                    $('#contact_email').addClass('is-invalid');
                    $('#contact_email').siblings('.invalid-feedback').text('Contact email is required')
                        .addClass('d-block');
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    isValid = false;
                    $('#contact_email').addClass('is-invalid');
                    $('#contact_email').siblings('.invalid-feedback').text(
                        'Please provide a valid email address').addClass('d-block');
                }

                // Validate phone
                const phone = $('#phone').val().trim();
                if (!phone) {
                    isValid = false;
                    $('#phone').addClass('is-invalid');
                    $('#phone').siblings('.invalid-feedback').text('Phone number is required').addClass(
                        'd-block');
                }

                if (isValid) {
                    currentTab = 1;
                    showTab(currentTab);
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    // Scroll to first error
                    const firstError = $('.is-invalid:first');
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
            });

            $('#prevBtn').on('click', function() {
                currentTab = 0;
                showTab(currentTab);
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // ============================================
            // Custom File Input - Display File Names
            // ============================================
            const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes

            // Thumbnail slot
            $('#thumbnail_image').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > maxFileSize) {
                        alert(
                            `File size (${(file.size / 1024 / 1024).toFixed(2)}MB) exceeds the maximum allowed size of 5MB. Please choose a smaller file.`
                        );
                        this.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        $('#thumbnail-slot .slot-placeholder').hide();
                        $('#thumbnail-slot .slot-image img').attr('src', ev.target.result);
                        $('#thumbnail-slot .slot-image').show();
                        $('#thumbnail-slot').addClass('has-image');
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#thumbnail-remove').on('click', function(e) {
                e.stopPropagation();
                $('#thumbnail_image').val('');
                $('#thumbnail-slot .slot-image img').attr('src', '');
                $('#thumbnail-slot .slot-image').hide();
                $('#thumbnail-slot .slot-placeholder').show();
                $('#thumbnail-slot').removeClass('has-image');
            });

            // ============================================
            // Gallery Image Slots (Edit Page)
            // ============================================
            const slotFiles = {}; // slot index => new File object
            const deletedGalleryIds = []; // IDs of existing images marked for deletion

            // File selected for a slot
            $(document).on('change', '.slot-file-input', function() {
                if ($(this).is('#thumbnail_image')) return;
                const file = this.files[0];
                if (!file) return;

                const $slot = $(this).closest('.gallery-slot');
                const slotIndex = parseInt($slot.data('slot'));

                if (file.size > maxFileSize) {
                    alert(
                        `File "${file.name}" is too large (${(file.size/1024/1024).toFixed(2)}MB). Max allowed is 5MB.`
                    );
                    this.value = '';
                    return;
                }

                // If slot had an existing image, mark it for deletion
                const existingId = $slot.data('existing-id');
                if (existingId && !deletedGalleryIds.includes(existingId)) {
                    deletedGalleryIds.push(existingId);
                    $('#deleted_gallery_images').val(JSON.stringify(deletedGalleryIds));
                    $slot.removeAttr('data-existing-id');
                }

                slotFiles[slotIndex] = file;

                const reader = new FileReader();
                reader.onload = function(ev) {
                    $slot.find('.slot-placeholder').hide();
                    $slot.find('.slot-image img').attr('src', ev.target.result);
                    $slot.find('.slot-image').show();
                    $slot.addClass('has-image');
                };
                reader.readAsDataURL(file);
                this.value = '';
            });

            // Remove image from slot
            $(document).on('click', '.slot-remove', function(e) {
                e.stopPropagation();
                const $slot = $(this).closest('.gallery-slot');
                const slotIndex = parseInt($slot.data('slot'));

                // If it was an existing image, mark for deletion
                const existingId = $slot.data('existing-id');
                if (existingId && !deletedGalleryIds.includes(existingId)) {
                    deletedGalleryIds.push(existingId);
                    $('#deleted_gallery_images').val(JSON.stringify(deletedGalleryIds));
                    $slot.removeAttr('data-existing-id');
                }

                // Remove new file if any
                delete slotFiles[slotIndex];

                $slot.find('.slot-image img').attr('src', '');
                $slot.find('.slot-image').hide();
                $slot.find('.slot-placeholder').show();
                $slot.removeClass('has-image');
            });

            // ============================================
            // Ajax Form Submission
            // ============================================
            $('#ad-edit-form').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors and messages
                $('.invalid-feedback').removeClass('d-block').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#form-messages').html('');

                // Add loading state to submit button
                const $submitBtn = $('#submitBtn');
                $submitBtn.addClass('btn-loading').prop('disabled', true);

                // Prepare form data
                const formData = new FormData(this);

                // Add gallery images from slot files
                formData.delete('gallery_images[]');
                Object.values(slotFiles).forEach(function(file) {
                    formData.append('gallery_images[]', file);
                });

                $.ajax({
                    url: "{{ route('member.ad.update', $ad->uid) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Remove loading state
                        $submitBtn.removeClass('btn-loading').prop('disabled', false);

                        if (response.success) {
                            // Show success message
                            $('#form-messages').html(
                                `<div class="success-message">${response.message}</div>`
                            );

                            // Scroll to top to show message
                            $('html, body').animate({
                                scrollTop: 0
                            }, 300);

                            // Redirect to ad details page after a short delay
                            if (response.redirect_url) {
                                setTimeout(function() {
                                    window.location.href = response.redirect_url;
                                }, 1500);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Remove loading state
                        $submitBtn.removeClass('btn-loading').prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorCount = 0;

                            // Display errors below each field
                            $.each(errors, function(field, messages) {
                                errorCount++;
                                const input = $(`[name="${field}"]`);
                                const errorContainer = input.siblings(
                                    '.invalid-feedback');

                                if (errorContainer.length === 0) {
                                    input.after(
                                        `<div class="invalid-feedback">${messages[0]}</div>`
                                    );
                                    input.next('.invalid-feedback').addClass('d-block');
                                } else {
                                    errorContainer.text(messages[0]).addClass(
                                        'd-block');
                                }

                                input.addClass('is-invalid');
                            });

                            // Show error message at top
                            $('#form-messages').html(
                                `<div class="error-message">Please fix ${errorCount} validation error${errorCount > 1 ? 's' : ''} below and try again.</div>`
                            );

                            // Scroll to first error
                            const firstError = $('.is-invalid:first');
                            if (firstError.length) {
                                $('html, body').animate({
                                    scrollTop: firstError.offset().top - 100
                                }, 500);
                            } else {
                                $('html, body').animate({
                                    scrollTop: 0
                                }, 300);
                            }
                        } else {
                            const message = xhr.responseJSON?.message ||
                                'An error occurred while updating your ad. Please try again.';

                            $('#form-messages').html(
                                `<div class="error-message">${message}</div>`
                            );

                            $('html, body').animate({
                                scrollTop: 0
                            }, 300);
                        }
                    }
                });
            });

            // Remove error styling on input change
            $('input, select, textarea').on('change input', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').removeClass('d-block');
            });
        });
    </script>
@endsection
