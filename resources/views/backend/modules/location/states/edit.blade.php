<form id="editForm">
    @csrf
    <input type="hidden" name="id" value="{{ $state->id }}">
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
                    data-lang="{{ $language->code }}" data-id="{{ $state->id }}"
                    data-endpoint="{{ route('classified.locations.state.edit') }}">
                    <span class="lang-dot"></span>
                    {{ $language->title }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-lg-12">
            <label class="black font-14">{{ __tr('Name') }}</label>
            <input type="text" name="name" class="form-control" placeholder="{{ __tr('Enter name') }}"
                value="{{ $state->translation('name', $lang) }}">
        </div>
    </div>

    @if ($lang == defaultLangCode())
        <div class="form-row">
            <div class="form-group col-lg-12">
                <label class="black font-14">{{ __tr('Country') }}</label>
                <select name="country" class="form-control country-select">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected($state->country_id == $country->id)>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-lg-12">
                <label class="black font-14">{{ __tr('Status') }}</label>
                <select name="status" class="form-control" required>
                    <option value="{{ config('settings.general_status.active') }}" @selected($state->status == config('settings.general_status.active'))>
                        {{ __tr('Active') }}
                    </option>
                    <option value="{{ config('settings.general_status.in_active') }}" @selected($state->status == config('settings.general_status.in_active'))>
                        {{ __tr('Inactive') }}
                    </option>
                </select>
            </div>
        </div>
    @endif

    <div class="btn-area d-flex justify-content-between">
        <button class="btn btn-primary mt-2 store-category">{{ __tr('Save Changes') }}</button>
    </div>

</form>
