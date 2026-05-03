<form id="editForm">
    @csrf
    <div class="form-row">
        <div class="form-group col-lg-12">
            <label class="black font-14">{{ __tr('Value') }}</label>
            <input type="text" name="value" class="form-control" placeholder="{{ __tr('Enter value') }}"
                value="{{ $option->value }}">
            <input type="hidden" name="id" value="{{ $option->id }}">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-lg-12">
            <label class="black font-14">{{ __tr('Status') }}</label>
            <select name="status" class="form-control">
                <option value="{{ config('settings.general_status.active') }}" @selected($option->status == config('settings.general_status.active'))>
                    {{ __tr('Active') }}
                </option>
                <option value="{{ config('settings.general_status.in_active') }}" @selected($option->status == config('settings.general_status.in_active'))>
                    {{ __tr('Inactive') }}
                </option>
            </select>
        </div>
    </div>
    <div class="btn-area d-flex justify-content-between">
        <button class="btn btn-primary mt-2">{{ __tr('Save Changes') }}</button>
    </div>
</form>
