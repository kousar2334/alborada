<form id="update-role-form">
    @csrf
    <input type="hidden" name="id" value="{{ $role->id }}">

    <div class="form-group mb-3">
        <label class="font-weight-bold">{{ __tr('Role Name') }}</label>
        <input type="text" class="form-control" name="name" placeholder="{{ __tr('Enter Role Name') }}"
            value="{{ $role->name }}">
    </div>

    <div class="form-group mb-0">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="font-weight-bold mb-0">{{ __tr('Permissions') }}</label>
            <div>
                <button type="button" class="btn btn-xs btn-outline-success select-all-global">
                    <i class="fas fa-check-square mr-1"></i>{{ __tr('Select All') }}
                </button>
                <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-global ml-1">
                    <i class="fas fa-square mr-1"></i>{{ __tr('Deselect All') }}
                </button>
            </div>
        </div>

        <div class="permission-modules">
            @foreach ($permissions as $module => $permission_list)
                <div class="card mb-3 permission-module-card">
                    <div class="card-header p-2" style="cursor:pointer;" data-toggle="collapse"
                        data-target="#edit-module-{{ Str::slug($module) }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down mr-2 collapse-icon" style="font-size:11px;"></i>
                                <span class="font-weight-bold text-sm">{{ $module }}</span>
                                <span class="badge badge-secondary ml-2">{{ $permission_list->count() }}</span>
                            </div>
                            <div onclick="event.stopPropagation();">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input module-select-all" type="checkbox"
                                        data-module="{{ Str::slug($module) }}"
                                        id="edit-select-all-{{ Str::slug($module) }}"
                                        @if ($role->permissions->whereIn('name', $permission_list->pluck('name'))->count() === $permission_list->count()) checked @endif>
                                    <label class="form-check-label text-sm"
                                        for="edit-select-all-{{ Str::slug($module) }}">
                                        {{ __tr('All') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse show" id="edit-module-{{ Str::slug($module) }}">
                        <div class="card-body p-2">
                            <div class="row">
                                @foreach ($permission_list as $permission)
                                    <div class="col-sm-6 col-md-4 col-lg-3 py-1">
                                        <div class="custom-control custom-checkbox">
                                            <input
                                                class="custom-control-input module-permission-{{ Str::slug($module) }}"
                                                id="edit-perm-{{ $permission->id }}" name="permission[]"
                                                type="checkbox" value="{{ $permission->name }}"
                                                @checked($role->permissions->contains($permission))>
                                            <label class="custom-control-label text-sm"
                                                for="edit-perm-{{ $permission->id }}" style="cursor:pointer;">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</form>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
        {{ __tr('Cancel') }}
    </button>
    <button type="button" class="btn btn-primary update-role-btn">
        <i class="fas fa-save mr-1"></i>{{ __tr('Save Changes') }}
    </button>
</div>

<script>
    (function($) {
        "use strict";

        // Module select-all toggle
        $(document).on('change', '.module-select-all', function() {
            var module = $(this).data('module');
            var checked = $(this).is(':checked');
            $('.module-permission-' + module).prop('checked', checked);
        });

        // Update "All" checkbox state when individual permissions change
        $(document).on('change', '[class^="module-permission-"], [class*=" module-permission-"]', function() {
            var classList = $(this).attr('class').split(' ');
            classList.forEach(function(cls) {
                if (cls.indexOf('module-permission-') === 0) {
                    var module = cls.replace('module-permission-', '');
                    var total = $('.' + cls).length;
                    var checked = $('.' + cls + ':checked').length;
                    $('[data-module="' + module + '"]').prop('checked', total === checked);
                }
            });
        });

        // Global select / deselect all
        $(document).on('click', '.select-all-global', function() {
            $('[name="permission[]"]').prop('checked', true);
            $('.module-select-all').prop('checked', true);
        });

        $(document).on('click', '.deselect-all-global', function() {
            $('[name="permission[]"]').prop('checked', false);
            $('.module-select-all').prop('checked', false);
        });

        // Collapse chevron rotate
        $(document).on('click', '[data-target^="#edit-module-"]', function() {
            $(this).find('.collapse-icon').toggleClass('fa-chevron-down fa-chevron-right');
        });

        // Save role
        $(document).on('click', '.update-role-btn', function(e) {
            e.preventDefault();
            $(document).find('.invalid-input').remove();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                data: $('#update-role-form').serialize(),
                url: '{{ route('admin.users.role.update') }}',
                success: function(response) {
                    if (response.success) {
                        toastr.success('Role updated successfully', 'Success');
                        $('#role-edit-modal').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(response.message, 'Error');
                    }
                },
                error: function(response) {
                    if (response.status === 422) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="error text-danger mb-0 invalid-input">' +
                                error + '</div>'
                            );
                        });
                    } else {
                        toastr.error('Role update failed', 'Error');
                    }
                }
            });
        });

    })(jQuery);
</script>
