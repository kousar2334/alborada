<form id="update-user-form">
    @csrf
    <div class="form-group">
        <label>{{ __tr('Name') }}</label>
        <input type="hidden" name="id" value="{{ $user->id }}">
        <input type="text" class="form-control" name="name" placeholder="{{ __tr('Enter Name') }}"
            value="{{ $user->name }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Email') }}</label>
        <input type="email" class="form-control" name="email" placeholder="{{ __tr('Enter Email') }}"
            value="{{ $user->email }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Password') }}</label>
        <input type="password" class="form-control" name="password" placeholder="{{ __tr('Enter Password') }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Confirm Password') }}</label>
        <input type="password" class="form-control" name="password_confirmation"
            placeholder="{{ __tr('Re Enter Password') }}">
    </div>
    <div class="form-group">
        <label>{{ __tr('Image') }}</label>
        <div class="input-group">
            <x-media name="edit_image" :value="$user->image"></x-media>
        </div>
    </div>
    <div class="form-group">
        <label>{{ __tr('Role') }}</label>
        <select name="role" class="form-control">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected($user->getRoleNames()->contains($role->name))>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>{{ __tr('Status') }}</label>
        <select name="status" class="form-control">
            <option value="{{ config('settings.general_status.active') }}" @selected($user->status == config('settings.general_status.active'))>
                {{ __tr('Active') }}</option>
            <option value="{{ config('settings.general_status.in_active') }}" @selected($user->status == config('settings.general_status.in_active'))>
                {{ __tr('Inactive') }}
            </option>
        </select>
    </div>
</form>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-primary update-user-btn">{{ __tr('Save Changes') }}</button>
</div>

<script>
    (function($) {
        "use strict";
        //Update User
        $('.update-user-btn').on('click', function(e) {
            e.preventDefault();
            e.preventDefault();
            $(document).find(".invalid-input").remove();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                data: $('#update-user-form').serialize(),
                url: '{{ route('admin.users.update') }}',
                success: function(response) {
                    if (response.success) {
                        toastr.success('User updated successfully', 'Success');
                        $('#update-user-form').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(response.message, 'Error')
                    }
                },
                error: function(response) {
                    if (response.status === 422) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="error text-danger mb-0 invalid-input">' +
                                error + '</div>');
                        })
                    } else {
                        toastr.error('User update failed', 'Error');
                    }
                }
            });
        });

    })(jQuery);
</script>
