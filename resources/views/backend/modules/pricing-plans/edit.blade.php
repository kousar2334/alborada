<form id="editForm">
    @csrf
    <input type="hidden" name="id" value="{{ $plan->id }}">
    <input type="hidden" name="lang" value="{{ $lang }}">

    {{-- Language tabs --}}
    <div class="lang-switcher-wrap mb-4">
        <div class="lang-switcher-label">
            <i class="fas fa-globe-americas"></i>
            <span>{{ __tr('Language') }}</span>
        </div>
        <div class="lang-switcher-tabs">
            @foreach (activeLanguages() as $language)
                <a href="#" class="lang-tab lang-switcher-btn @if ($language->code == $lang) active @endif"
                    data-lang="{{ $language->code }}" data-id="{{ $plan->id }}">
                    <span class="lang-dot"></span>
                    {{ $language->title }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-lg-12">
            <label class="black font-14">{{ __tr('Title') }} *</label>
            <input type="text" name="title" class="form-control" value="{{ $plan->translation('title', $lang) }}"
                placeholder="{{ __tr('Enter plan title') }}">
        </div>
    </div>

    @if ($lang == defaultLangCode())
        <div class="form-row">
            <div class="form-group col-lg-6">
                <label class="black font-14">{{ __tr('Duration (Days)') }} *</label>
                <input type="number" name="duration_days" class="form-control" min="1"
                    value="{{ $plan->duration_days }}" placeholder="{{ __tr('Enter duration in days') }}">
            </div>
            <div class="form-group col-lg-6">
                <label class="black font-14">{{ __tr('Price') }} *</label>
                <input type="number" name="price" class="form-control" min="0" step="0.01"
                    value="{{ $plan->price }}" placeholder="{{ __tr('Enter price') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Ad Posting Quantity') }} *</label>
                <input type="number" name="listing_quantity" class="form-control" min="0"
                    value="{{ $plan->listing_quantity }}" placeholder="{{ __tr('Number of listings') }}">
            </div>
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Featured Listing Quantity') }} *</label>
                <input type="number" name="featured_listing_quantity" class="form-control" min="0"
                    value="{{ $plan->featured_listing_quantity }}"
                    placeholder="{{ __tr('Number of featured listings') }}">
            </div>
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Gallery Image Quantity') }} *</label>
                <input type="number" name="gallery_image_quantity" class="form-control" min="0"
                    value="{{ $plan->gallery_image_quantity }}"
                    placeholder="{{ __tr('Max gallery images per listing') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Membership Badge') }}</label>
                <select name="membership_badge" class="form-control">
                    <option value="0" {{ $plan->membership_badge == 0 ? 'selected' : '' }}>
                        {{ __tr('Disabled') }}
                    </option>
                    <option value="1" {{ $plan->membership_badge == 1 ? 'selected' : '' }}>
                        {{ __tr('Enabled') }}
                    </option>
                </select>
            </div>
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Status') }}</label>
                <select name="status" class="form-control">
                    <option value="{{ config('settings.general_status.active') }}"
                        {{ $plan->status == config('settings.general_status.active') ? 'selected' : '' }}>
                        {{ __tr('Active') }}
                    </option>
                    <option value="{{ config('settings.general_status.in_active') }}"
                        {{ $plan->status == config('settings.general_status.in_active') ? 'selected' : '' }}>
                        {{ __tr('Inactive') }}
                    </option>
                </select>
            </div>
        </div>
    @endif

    <div class="btn-area d-flex justify-content-between">
        <button type="submit" class="btn btn-primary mt-2">{{ __tr('Update') }}</button>
    </div>
</form>

<script>
    (function($) {
        $('.lang-tab').on('click', function(e) {
            e.preventDefault();
            var lang = $(this).data('lang');
            var id = $(this).data('id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('admin.pricing.plans.edit') }}',
                data: {
                    id: id,
                    lang: lang
                },
                success: function(response) {
                    if (response.success) {
                        $('.item-edit-content').html(response.html);
                    }
                }
            });
        });
    })(jQuery);
</script>
