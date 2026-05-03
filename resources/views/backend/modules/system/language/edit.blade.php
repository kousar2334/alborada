<form id="language-update-form">
    @csrf
    <div class="form-group">
        <label>{{ __tr('Name') }}</label>
        <input type="hidden" name="id" value="{{ $lang->id }}">
        <input type="text" class="form-control" value="{{ $lang->title }}" name="name"
            placeholder="{{ __tr('Enter Name') }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Native Name') }}</label>
        <input type="text" class="form-control" value="{{ $lang->native_title }}" name="native_name"
            placeholder="{{ __tr('Enter Native Name') }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Code') }}</label>
        <input type="code" class="form-control" name="code" value="{{ $lang->code }}"
            placeholder="{{ __tr('Enter code') }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Status') }}</label>
        <select name="status" class="form-control">
            <option value="{{ config('settings.general_status.active') }}" @selected($lang->status == config('settings.general_status.active'))>
                {{ __tr('Active') }}</option>
            <option value="{{ config('settings.general_status.in_active') }}" @selected($lang->status == config('settings.general_status.in_active'))>
                {{ __tr('Inactive') }}
            </option>
        </select>
    </div>
    <div class="form-group">
        <label>{{ __tr('Icon') }}</label>
        <x-media name="icon" value=""></x-media>
    </div>
</form>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-primary language-update-btn">{{ __tr('Save Changes') }}</button>
</div>
