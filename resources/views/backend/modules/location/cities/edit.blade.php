<form id="editForm">
    @csrf
    <input type="hidden" name="id" value="{{ $city->id }}">
    <input type="hidden" name="lang" value="{{ $lang }}">

    {{-- Language Tabs --}}
    <div class="lang-switcher-wrap mb-3">
        <div class="lang-switcher-label">
            <i class="fas fa-globe-americas"></i>
            <span>{{ __tr('Language') }}</span>
        </div>
        <div class="lang-switcher-tabs">
            @foreach ($languages as $language)
                <button type="button"
                    class="lang-switcher-btn location-lang-tab @if ($language->code == $lang) active @endif"
                    data-lang="{{ $language->code }}" data-id="{{ $city->id }}"
                    data-endpoint="{{ route('classified.locations.city.edit') }}">
                    <span class="lang-dot"></span>
                    {{ $language->title }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-lg-12">
            <label class="black font-14">{{ __tr('Name') }}</label>
            <input type="text" name="name" class="form-control" placeholder="{{ __tr('Enter Name') }}"
                value="{{ $city->translation('name', $lang) }}">
        </div>
    </div>

    @if ($lang == defaultLangCode())
        <div class="form-row">
            <div class="form-group col-lg-12">
                <label class="black font-14">{{ __tr('Country') }}</label>
                <select name="country" class="form-control country-select">
                    <option value="">{{ __tr('Select Country') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected($city->state?->country_id == $country->id)>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-lg-12">
                <label class="black font-14">{{ __tr('State') }}</label>
                <select name="state_id" class="form-control state-select">
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" @selected($city->state_id == $state->id)>{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-lg-12">
                <label class="black font-14">{{ __tr('Status') }}</label>
                <select name="status" class="form-control" required>
                    <option value="{{ config('settings.general_status.active') }}" @selected($city->status == config('settings.general_status.active'))>
                        {{ __tr('Active') }}
                    </option>
                    <option value="{{ config('settings.general_status.in_active') }}" @selected($city->status == config('settings.general_status.in_active'))>
                        {{ __tr('Inactive') }}
                    </option>
                </select>
            </div>
        </div>
    @endif

    <div class="btn-area d-flex justify-content-between">
        <button class="btn btn-primary mt-2">{{ __tr('Save Changes') }}</button>
    </div>

</form>
