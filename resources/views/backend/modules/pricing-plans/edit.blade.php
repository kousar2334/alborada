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
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Regular Price') }} *</label>
                <input type="number" name="price" class="form-control" min="0" step="0.01"
                    value="{{ $plan->price }}" placeholder="{{ __tr('Enter price') }}">
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Offer Price') }}</label>
                <input type="number" name="offer_price" class="form-control" min="0" step="0.01"
                    value="{{ $plan->offer_price }}" placeholder="{{ __tr('Optional promo price') }}">
                <small class="text-muted">{{ __tr('Leave empty for no offer') }}</small>
            </div>
        </div>

        <hr>
        <h6 class="text-muted mb-3">{{ __tr('IPTV Settings') }}</h6>

        <div class="form-row">
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Max Connections') }} *</label>
                <input type="number" name="max_connections" class="form-control" min="1" max="99"
                    value="{{ $plan->max_connections }}" placeholder="1">
            </div>
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Streaming Quality') }} *</label>
                <select name="streaming_quality" class="form-control">
                    @foreach (['SD', 'HD', 'FHD', '4K'] as $q)
                        <option value="{{ $q }}" {{ $plan->streaming_quality == $q ? 'selected' : '' }}>
                            {{ $q }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4">
                <label class="black font-14">{{ __tr('Catch-up Days') }} *</label>
                <input type="number" name="catchup_days" class="form-control" min="0"
                    value="{{ $plan->catchup_days }}" placeholder="0">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Provider Package') }}</label>
                <select name="iptv_package_id" class="form-control">
                    <option value="">{{ __tr('— None —') }}</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->package_id }}"
                            {{ (string) $plan->iptv_package_id === (string) $package->package_id ? 'selected' : '' }}>
                            {{ $package->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">{{ __tr('Used by 8K CMS') }}</small>
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('IPTV Duration (Months)') }}</label>
                <select name="iptv_sub_months" class="form-control">
                    @foreach ([1, 3, 6, 12] as $m)
                        <option value="{{ $m }}" {{ (int) $plan->iptv_sub_months === $m ? 'selected' : '' }}>
                            {{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Device Type') }}</label>
                <select name="iptv_device_type" class="form-control">
                    <option value="m3u" {{ $plan->iptv_device_type === 'm3u' ? 'selected' : '' }}>M3U</option>
                    <option value="mag" {{ $plan->iptv_device_type === 'mag' ? 'selected' : '' }}>MAG</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Country') }}</label>
                <input type="text" name="iptv_country" class="form-control"
                    value="{{ $plan->iptv_country ?? 'ALL' }}" placeholder="ALL">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('DVR Enabled') }}</label>
                <select name="dvr_enabled" class="form-control">
                    <option value="0" {{ !$plan->dvr_enabled ? 'selected' : '' }}>{{ __tr('No') }}</option>
                    <option value="1" {{ $plan->dvr_enabled ? 'selected' : '' }}>{{ __tr('Yes') }}</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Trial Plan') }}</label>
                <select name="is_trial" class="form-control">
                    <option value="0" {{ !$plan->is_trial ? 'selected' : '' }}>{{ __tr('No') }}</option>
                    <option value="1" {{ $plan->is_trial ? 'selected' : '' }}>{{ __tr('Yes') }}</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Trial Days') }}</label>
                <input type="number" name="trial_days" class="form-control" min="1"
                    value="{{ $plan->trial_days }}" placeholder="{{ __tr('Leave empty if not trial') }}">
            </div>
            <div class="form-group col-lg-3">
                <label class="black font-14">{{ __tr('Sort Order') }}</label>
                <input type="number" name="sort_order" class="form-control" min="0"
                    value="{{ $plan->sort_order }}" placeholder="0">
            </div>
        </div>

        <hr>
        <div class="form-row">
            <div class="form-group col-lg-6">
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
