@php
    $links = [
        ['title' => 'Users', 'route' => 'admin.users.list', 'active' => false],
        ['title' => 'Roles', 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Roles') }}
@endsection
@section('page-style')
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        .permission-module-card .card-header {
            background: #f4f6f9;
        }

        .permission-module-card .card-header:hover {
            background: #e9ecef;
        }

        .custom-control-label {
            font-size: 0.82rem;
        }

        .module-modal-body {
            max-height: 65vh;
            overflow-y: auto;
        }

        .collapse-icon {
            transition: transform 0.2s;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="Roles" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Roles') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#role-create-modal">
                                <i class="fas fa-plus mr-1"></i>{{ __tr('Add New Role') }}
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="rolesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('ID') }}</th>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('Guard') }}</th>
                                        <th>{{ __tr('Permissions') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td class="text-capitalize font-weight-bold">{{ $role->name }}</td>
                                            <td>{{ $role->guard_name }}</td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ $role->permissions->count() }} {{ __tr('permissions') }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-default">
                                                        {{ __tr('Action') }}
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-default dropdown-toggle dropdown-toggle-split"
                                                        data-toggle="dropdown">
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item edit-role" href="#"
                                                            data-id="{{ $role->id }}">
                                                            <i class="fas fa-edit mr-1"></i>{{ __tr('Edit') }}
                                                        </a>
                                                        <a class="dropdown-item delete-role text-danger" href="#"
                                                            data-id="{{ $role->id }}">
                                                            <i class="fas fa-trash mr-1"></i>{{ __tr('Delete') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CREATE ROLE MODAL ── --}}
        <div class="modal fade" id="role-create-modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('New Role') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-role-form">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __tr('Role Name') }}</label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="{{ __tr('Enter Role Name') }}">
                            </div>

                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="font-weight-bold mb-0">{{ __tr('Permissions') }}</label>
                                    <div>
                                        <button type="button"
                                            class="btn btn-xs btn-outline-success create-select-all-global">
                                            <i class="fas fa-check-square mr-1"></i>{{ __tr('Select All') }}
                                        </button>
                                        <button type="button"
                                            class="btn btn-xs btn-outline-secondary create-deselect-all-global ml-1">
                                            <i class="fas fa-square mr-1"></i>{{ __tr('Deselect All') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="module-modal-body">
                                    @foreach ($permissions as $module => $permission_list)
                                        <div class="card mb-3 permission-module-card">
                                            <div class="card-header p-2" style="cursor:pointer;" data-toggle="collapse"
                                                data-target="#create-module-{{ Str::slug($module) }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-chevron-down mr-2 collapse-icon"
                                                            style="font-size:11px;"></i>
                                                        <span class="font-weight-bold text-sm">{{ $module }}</span>
                                                        <span
                                                            class="badge badge-secondary ml-2">{{ $permission_list->count() }}</span>
                                                    </div>
                                                    <div onclick="event.stopPropagation();">
                                                        <div class="form-check form-check-inline mb-0">
                                                            <input class="form-check-input create-module-select-all"
                                                                type="checkbox"
                                                                data-module="create-{{ Str::slug($module) }}"
                                                                id="create-select-all-{{ Str::slug($module) }}">
                                                            <label class="form-check-label text-sm"
                                                                for="create-select-all-{{ Str::slug($module) }}">
                                                                {{ __tr('All') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse show" id="create-module-{{ Str::slug($module) }}">
                                                <div class="card-body p-2">
                                                    <div class="row">
                                                        @foreach ($permission_list as $permission)
                                                            <div class="col-sm-6 col-md-4 col-lg-3 py-1">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input
                                                                        class="custom-control-input create-module-permission-{{ Str::slug($module) }}"
                                                                        id="create-perm-{{ $permission->id }}"
                                                                        name="permission[]" type="checkbox"
                                                                        value="{{ $permission->name }}">
                                                                    <label class="custom-control-label text-sm"
                                                                        for="create-perm-{{ $permission->id }}"
                                                                        style="cursor:pointer;">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ __tr('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-success create-new-role-btn">
                            <i class="fas fa-plus mr-1"></i>{{ __tr('Create Role') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── END CREATE ROLE MODAL ── --}}

        {{-- ── EDIT ROLE MODAL ── --}}
        <div class="modal fade" id="role-edit-modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('Edit Role') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body module-modal-body role-edit-content">
                        {{-- content loaded via AJAX --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- ── END EDIT ROLE MODAL ── --}}

        {{-- ── DELETE ROLE MODAL ── --}}
        <div class="modal fade" id="role-delete-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete role ?') }}</h4>
                        <form method="POST" action="{{ route('admin.users.role.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-role-id" name="id">
                            <button type="button" class="btn mt-2 btn-secondary"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger mt-2">
                                <i class="fas fa-trash mr-1"></i>{{ __tr('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── END DELETE ROLE MODAL ── --}}
    </section>
@endsection

@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/web-assets/backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/web-assets/backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script>
        (function($) {
            "use strict";

            // DataTable
            $('#rolesTable').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
            });

            // ── Create modal: module select-all ──────────────────────────────
            $(document).on('change', '.create-module-select-all', function() {
                var module = $(this).data('module');
                var checked = $(this).is(':checked');
                $('.create-module-permission-' + module.replace('create-', '')).prop('checked', checked);
            });

            $(document).on('change', '[class*="create-module-permission-"]', function() {
                var cls = $(this).attr('class').split(' ').find(c => c.startsWith('create-module-permission-'));
                if (!cls) return;
                var module = cls.replace('create-module-permission-', '');
                var total = $('.create-module-permission-' + module).length;
                var checked = $('.create-module-permission-' + module + ':checked').length;
                $('[data-module="create-' + module + '"]').prop('checked', total === checked);
            });

            $('#role-create-modal').on('click', '.create-select-all-global', function() {
                $(this).closest('.modal').find('[name="permission[]"]').prop('checked', true);
                $(this).closest('.modal').find('.create-module-select-all').prop('checked', true);
            });
            $('#role-create-modal').on('click', '.create-deselect-all-global', function() {
                $(this).closest('.modal').find('[name="permission[]"]').prop('checked', false);
                $(this).closest('.modal').find('.create-module-select-all').prop('checked', false);
            });

            // Reset create form on close
            $('#role-create-modal').on('hidden.bs.modal', function() {
                $('#new-role-form')[0].reset();
                $('.create-module-select-all').prop('checked', false);
            });

            // ── Collapse chevron (create modal) ──────────────────────────────
            $('#role-create-modal').on('click', '[data-target^="#create-module-"]', function() {
                $(this).find('.collapse-icon').toggleClass('fa-chevron-down fa-chevron-right');
            });

            // ── Create role AJAX ──────────────────────────────────────────────
            $('.create-new-role-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    data: $('#new-role-form').serialize(),
                    url: '{{ route('admin.users.role.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Role created successfully', 'Success');
                            $('#role-create-modal').modal('hide');
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
                            toastr.error('Role create failed', 'Error');
                        }
                    }
                });
            });

            // ── Delete modal ──────────────────────────────────────────────────
            $('.delete-role').on('click', function(e) {
                e.preventDefault();
                $('#delete-role-id').val($(this).data('id'));
                $('#role-delete-modal').modal('show');
            });

            // ── Edit role (AJAX load) ─────────────────────────────────────────
            $('.edit-role').on('click', function(e) {
                e.preventDefault();
                var role_id = $(this).data('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        id: role_id
                    },
                    url: '{{ route('admin.users.role.edit') }}',
                    success: function(response) {
                        if (response.success) {
                            $('.role-edit-content').html(response.data);
                            $('#role-edit-modal').modal('show');
                        } else {
                            toastr.error('Role not found', 'Error');
                        }
                    },
                    error: function() {
                        toastr.error('Role not found', 'Error');
                    }
                });
            });

        })(jQuery);
    </script>
@endsection
