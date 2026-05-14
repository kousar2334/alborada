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
        /* ── Right-side panel modal ── */
        .modal-panel .modal-dialog {
            position: fixed;
            right: 0;
            top: 0;
            margin: 0;
            height: 100vh;
            width: 500px;
            max-width: 100%;
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }

        .modal-panel.show .modal-dialog {
            transform: translateX(0);
        }

        .modal-panel .modal-content {
            height: 100%;
            border-radius: 0;
            border: none;
            border-left: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
        }

        .modal-panel .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            flex-shrink: 0;
        }

        .modal-panel .modal-body {
            overflow-y: auto;
            flex: 1;
            padding: 1.25rem;
        }

        .modal-panel .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 0.75rem 1.25rem;
            flex-shrink: 0;
        }

        /* ── Form fields ── */
        .role-form .field-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.375rem;
            display: block;
        }

        .role-form .form-control {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .role-form .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .char-count {
            font-size: 0.75rem;
            color: #9ca3af;
            text-align: right;
            margin-top: 2px;
        }

        /* ── Permissions section ── */
        .permissions-section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .permissions-section-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }

        .perm-search-wrap {
            position: relative;
            margin-bottom: 0.75rem;
        }

        .perm-search-wrap input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.45rem 0.75rem 0.45rem 2rem;
            font-size: 0.8rem;
            outline: none;
            background: #fff;
        }

        .perm-search-wrap input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
        }

        .perm-search-wrap i {
            position: absolute;
            left: 0.6rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.8rem;
            pointer-events: none;
        }

        .perm-select-all-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 0.5rem;
        }

        .perm-select-all-row .perm-select-all-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #374151;
            cursor: pointer;
            margin: 0;
        }

        .expand-all-btn {
            font-size: 0.78rem;
            color: #3b82f6;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .expand-all-btn:hover {
            text-decoration: underline;
        }

        /* ── Module group rows ── */
        .perm-module {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 5px;
            overflow: hidden;
        }

        .perm-module.d-none {
            display: none !important;
        }

        .perm-module-header {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: #f9fafb;
            cursor: pointer;
            user-select: none;
            gap: 0.45rem;
        }

        .perm-module-header:hover {
            background: #f3f4f6;
        }

        .perm-module-chevron {
            font-size: 0.68rem;
            color: #6b7280;
            transition: transform 0.2s;
            width: 12px;
            flex-shrink: 0;
        }

        .perm-module-chevron.collapsed {
            transform: rotate(-90deg);
        }

        .perm-module-check {
            cursor: pointer;
            flex-shrink: 0;
        }

        .perm-module-name {
            flex: 1;
            font-size: 0.83rem;
            font-weight: 600;
            color: #374151;
        }

        .perm-module-count {
            font-size: 0.75rem;
            color: #6b7280;
            flex-shrink: 0;
        }

        .perm-module-body {
            padding: 0.4rem 0.75rem 0.4rem 2.5rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }

        .perm-item {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.18rem 0;
            font-size: 0.8rem;
            color: #374151;
        }

        .perm-item.d-none {
            display: none !important;
        }

        .perm-item input[type="checkbox"] {
            cursor: pointer;
            flex-shrink: 0;
        }

        .perm-item label {
            margin: 0;
            cursor: pointer;
        }

        /* ── Modal title ── */
        .modal-panel .modal-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .modal-panel .close {
            font-size: 1.4rem;
            color: #6b7280;
        }

        /* ── Footer buttons ── */
        .btn-role-cancel {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            font-size: 0.875rem;
            border-radius: 6px;
            padding: 0.45rem 1rem;
        }

        .btn-role-cancel:hover {
            background: #f9fafb;
        }

        .btn-role-save {
            background: #111827;
            color: #fff;
            border: 1px solid #111827;
            font-size: 0.875rem;
            border-radius: 6px;
            padding: 0.45rem 1.1rem;
        }

        .btn-role-save:hover {
            background: #1f2937;
            color: #fff;
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
                                <i class="fas fa-plus mr-1"></i>{{ __tr('New Role') }}
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="rolesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('ID') }}</th>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('Description') }}</th>
                                        <th>{{ __tr('Permissions') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td class="text-capitalize font-weight-bold">{{ $role->name }}</td>
                                            <td class="text-muted small">{{ $role->description ?? '-' }}</td>
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

        {{-- ── CREATE ROLE PANEL ── --}}
        <div class="modal fade modal-panel" id="role-create-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('Create New Role') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body role-form">
                        <form id="new-role-form">
                            @csrf

                            <div class="form-group mb-3">
                                <span class="field-label">{{ __tr('Name') }}</span>
                                <input type="text" class="form-control" name="name" maxlength="255"
                                    id="create-name-input" placeholder="{{ __tr('e.g. Store Manager, Content Editor') }}">
                                <div class="char-count"><span id="create-name-count">0</span>/255</div>
                            </div>

                            <div class="form-group mb-3">
                                <span class="field-label">{{ __tr('Description') }}</span>
                                <input type="text" class="form-control" name="description" maxlength="255"
                                    id="create-desc-input"
                                    placeholder="{{ __tr('Brief description of this role\'s responsibilities') }}">
                                <div class="char-count"><span id="create-desc-count">0</span>/255</div>
                            </div>

                            <div class="mb-0">
                                <div class="permissions-section-title">{{ __tr('Permissions') }}</div>
                                <div class="permissions-section-subtitle">
                                    {{ __tr('Select permissions for this role, grouped by module.') }}
                                </div>

                                <div class="perm-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="perm-search-input"
                                        placeholder="{{ __tr('Search permissions or modules') }}" data-context="create">
                                </div>

                                <div class="perm-select-all-row">
                                    <label class="perm-select-all-label">
                                        <input type="checkbox" class="create-select-all-global">
                                        {{ __tr('Select all permissions') }}
                                    </label>
                                    <button type="button" class="expand-all-btn" data-context="create"
                                        data-expanded="1">{{ __tr('Collapse all') }}</button>
                                </div>

                                <div id="create-perm-modules">
                                    @foreach ($permissions as $module => $permission_list)
                                        <div class="perm-module" data-module-name="{{ strtolower($module) }}">
                                            <div class="perm-module-header" data-toggle="collapse"
                                                data-target="#create-mod-{{ Str::slug($module) }}">
                                                <i class="fas fa-chevron-down perm-module-chevron"></i>
                                                <input type="checkbox" class="perm-module-check create-module-select-all"
                                                    data-module="create-{{ Str::slug($module) }}"
                                                    id="c-all-{{ Str::slug($module) }}"
                                                    onclick="event.stopPropagation();">
                                                <span class="perm-module-name">{{ $module }}</span>
                                                <span class="perm-module-count"
                                                    data-slug="create-{{ Str::slug($module) }}"
                                                    data-total="{{ $permission_list->count() }}">
                                                    0/{{ $permission_list->count() }}
                                                </span>
                                            </div>
                                            <div class="collapse show" id="create-mod-{{ Str::slug($module) }}">
                                                <div class="perm-module-body">
                                                    @foreach ($permission_list as $permission)
                                                        <div class="perm-item"
                                                            data-perm-name="{{ strtolower($permission->name) }}">
                                                            <input type="checkbox"
                                                                class="create-module-permission-{{ Str::slug($module) }}"
                                                                id="create-perm-{{ $permission->id }}"
                                                                name="permission[]" value="{{ $permission->name }}">
                                                            <label
                                                                for="create-perm-{{ $permission->id }}">{{ $permission->name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-role-cancel" data-dismiss="modal">
                            {{ __tr('cancel') }}
                        </button>
                        <button type="button" class="btn btn-role-save create-new-role-btn">
                            {{ __tr('Save and assign') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── END CREATE ROLE PANEL ── --}}

        {{-- ── EDIT ROLE PANEL ── --}}
        <div class="modal fade modal-panel" id="role-edit-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('Edit Role') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body role-form role-edit-content">
                        {{-- AJAX content --}}
                    </div>
                    <div class="modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-role-cancel" data-dismiss="modal">
                            {{ __tr('cancel') }}
                        </button>
                        <button type="button" class="btn btn-role-save update-role-btn">
                            {{ __tr('Save and assign') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── END EDIT ROLE PANEL ── --}}

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

            // ── DataTable ────────────────────────────────────────────────────────
            $('#rolesTable').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
            });

            // ── Char counters ─────────────────────────────────────────────────────
            $('#create-name-input').on('input', function() {
                $('#create-name-count').text($(this).val().length);
            });
            $('#create-desc-input').on('input', function() {
                $('#create-desc-count').text($(this).val().length);
            });
            $(document).on('input', '#edit-name-input', function() {
                $('#edit-name-count').text($(this).val().length);
                $('#edit-name-count-display').text($(this).val().length);
            });
            $(document).on('input', '#edit-desc-input', function() {
                $('#edit-desc-count').text($(this).val().length);
                $('#edit-desc-count-display').text($(this).val().length);
            });

            // ── Permission count helper ───────────────────────────────────────────
            function updateModuleCount(slug) {
                var $checkboxes = $('.' + slug.replace('create-', 'create-module-permission-').replace('edit-',
                    'module-permission-'));
                if (!$checkboxes.length) {
                    // try both prefixes
                    $checkboxes = $('[class*="' + slug + '"]').filter('input[type=checkbox]');
                }
                var total = $('[data-slug="' + slug + '"]').data('total');
                var checked = 0;
                // find all permission checkboxes for this slug
                var moduleSlug = slug.replace(/^(create|edit)-/, '');
                if (slug.startsWith('create-')) {
                    checked = $('.create-module-permission-' + moduleSlug + ':checked').length;
                } else {
                    checked = $('.module-permission-' + moduleSlug + ':checked').length;
                }
                $('[data-slug="' + slug + '"]').text(checked + '/' + total);
            }

            function updateAllCounts(context) {
                $('[data-slug]').each(function() {
                    var slug = $(this).data('slug');
                    if (context === 'create' && slug.startsWith('create-')) updateModuleCount(slug);
                    if (context === 'edit' && !slug.startsWith('create-')) updateModuleCount(slug);
                });
            }

            // ── Module chevron toggle (create) ─────────────────────────────────
            $(document).on('click', '#role-create-modal .perm-module-header', function() {
                $(this).find('.perm-module-chevron').toggleClass('collapsed');
            });
            $(document).on('click', '#role-edit-modal .perm-module-header', function() {
                $(this).find('.perm-module-chevron').toggleClass('collapsed');
            });

            // ── Create: module select-all ──────────────────────────────────────
            $(document).on('change', '.create-module-select-all', function() {
                var slug = $(this).data('module');
                var moduleSlug = slug.replace('create-', '');
                var checked = $(this).is(':checked');
                $('.create-module-permission-' + moduleSlug).prop('checked', checked);
                updateModuleCount(slug);
                syncGlobalSelectAll('create');
            });

            $(document).on('change', '[class*="create-module-permission-"]', function() {
                var cls = $.grep($(this).attr('class').split(' '), function(c) {
                    return c.startsWith('create-module-permission-');
                })[0];
                if (!cls) return;
                var moduleSlug = cls.replace('create-module-permission-', '');
                var slug = 'create-' + moduleSlug;
                var total = $('.create-module-permission-' + moduleSlug).length;
                var checked = $('.create-module-permission-' + moduleSlug + ':checked').length;
                $('[data-module="' + slug + '"]').prop('checked', total === checked).prop('indeterminate',
                    checked > 0 && checked < total);
                updateModuleCount(slug);
                syncGlobalSelectAll('create');
            });

            // ── Edit: module select-all ────────────────────────────────────────
            $(document).on('change', '.module-select-all', function() {
                var slug = $(this).data('module');
                var checked = $(this).is(':checked');
                $('.module-permission-' + slug).prop('checked', checked);
                updateModuleCount('edit-' + slug);
                syncGlobalSelectAll('edit');
            });

            $(document).on('change', '[class*="module-permission-"]', function() {
                var cls = $.grep($(this).attr('class').split(' '), function(c) {
                    return c.startsWith('module-permission-') && !c.includes('select-all');
                })[0];
                if (!cls) return;
                var slug = cls.replace('module-permission-', '');
                var total = $('.module-permission-' + slug).length;
                var checked = $('.module-permission-' + slug + ':checked').length;
                $('[data-module="' + slug + '"]').prop('checked', total === checked).prop('indeterminate',
                    checked > 0 && checked < total);
                updateModuleCount('edit-' + slug);
                syncGlobalSelectAll('edit');
            });

            // ── Global select-all sync ─────────────────────────────────────────
            function syncGlobalSelectAll(context) {
                if (context === 'create') {
                    var total = $('#role-create-modal [name="permission[]"]').length;
                    var checked = $('#role-create-modal [name="permission[]"]:checked').length;
                    $('.create-select-all-global').prop('checked', total > 0 && total === checked)
                        .prop('indeterminate', checked > 0 && checked < total);
                } else {
                    var total = $('#role-edit-modal [name="permission[]"]').length;
                    var checked = $('#role-edit-modal [name="permission[]"]:checked').length;
                    $('.edit-select-all-global').prop('checked', total > 0 && total === checked)
                        .prop('indeterminate', checked > 0 && checked < total);
                }
            }

            $(document).on('change', '.create-select-all-global', function() {
                var checked = $(this).is(':checked');
                $('#role-create-modal [name="permission[]"]').prop('checked', checked);
                $('#role-create-modal .create-module-select-all').prop('checked', checked).prop('indeterminate',
                    false);
                updateAllCounts('create');
            });

            $(document).on('change', '.edit-select-all-global', function() {
                var checked = $(this).is(':checked');
                $('#role-edit-modal [name="permission[]"]').prop('checked', checked);
                $('#role-edit-modal .module-select-all').prop('checked', checked).prop('indeterminate', false);
                updateAllCounts('edit');
            });

            // ── Expand / Collapse all ──────────────────────────────────────────
            $(document).on('click', '.expand-all-btn', function() {
                var context = $(this).data('context');
                var expanded = $(this).data('expanded');
                var $modal = context === 'create' ? $('#role-create-modal') : $('#role-edit-modal');
                if (expanded) {
                    $modal.find('.collapse').removeClass('show');
                    $modal.find('.perm-module-chevron').addClass('collapsed');
                    $(this).text('{{ __tr('Expand all') }}').data('expanded', 0);
                } else {
                    $modal.find('.collapse').addClass('show');
                    $modal.find('.perm-module-chevron').removeClass('collapsed');
                    $(this).text('{{ __tr('Collapse all') }}').data('expanded', 1);
                }
            });

            // ── Permission search ─────────────────────────────────────────────
            $(document).on('input', '.perm-search-input', function() {
                var q = $(this).val().toLowerCase().trim();
                var context = $(this).data('context');
                var $modules = context === 'create' ?
                    $('#create-perm-modules .perm-module') :
                    $('#edit-perm-modules .perm-module');

                $modules.each(function() {
                    var moduleName = $(this).find('.perm-module-name').text().toLowerCase();
                    var $items = $(this).find('.perm-item');
                    var anyVisible = false;

                    if (!q) {
                        $(this).removeClass('d-none');
                        $items.removeClass('d-none');
                        return;
                    }

                    $items.each(function() {
                        var permName = $(this).data('perm-name') || $(this).find('label').text()
                            .toLowerCase();
                        if (permName.includes(q) || moduleName.includes(q)) {
                            $(this).removeClass('d-none');
                            anyVisible = true;
                        } else {
                            $(this).addClass('d-none');
                        }
                    });

                    if (moduleName.includes(q)) {
                        $(this).removeClass('d-none');
                        $items.removeClass('d-none');
                    } else if (anyVisible) {
                        $(this).removeClass('d-none');
                        $(this).find('.collapse').addClass('show');
                        $(this).find('.perm-module-chevron').removeClass('collapsed');
                    } else {
                        $(this).addClass('d-none');
                    }
                });
            });

            // ── Reset create form on close ────────────────────────────────────
            $('#role-create-modal').on('hidden.bs.modal', function() {
                $('#new-role-form')[0].reset();
                $('#create-name-count').text('0');
                $('#create-desc-count').text('0');
                $('.create-module-select-all').prop('checked', false).prop('indeterminate', false);
                $('.create-select-all-global').prop('checked', false).prop('indeterminate', false);
                $('.perm-search-input[data-context="create"]').val('');
                $('#create-perm-modules .perm-module, #create-perm-modules .perm-item').removeClass('d-none');
                updateAllCounts('create');
            });

            // ── Create role AJAX ──────────────────────────────────────────────
            $('.create-new-role-btn').on('click', function(e) {
                e.preventDefault();
                $('.invalid-input').remove();
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
                                $('[name=' + field_name + ']').first().after(
                                    '<div class="error text-danger mb-0 invalid-input small">' +
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
            $(document).on('click', '.delete-role', function(e) {
                e.preventDefault();
                $('#delete-role-id').val($(this).data('id'));
                $('#role-delete-modal').modal('show');
            });

            // ── Edit role (AJAX load) ─────────────────────────────────────────
            $(document).on('click', '.edit-role', function(e) {
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
                            updateAllCounts('edit');
                            syncGlobalSelectAll('edit');
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

            // ── Update role AJAX ──────────────────────────────────────────────
            $(document).on('click', '.update-role-btn', function(e) {
                e.preventDefault();
                $('.invalid-input').remove();
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
                                $('#update-role-form [name=' + field_name + ']').first()
                                    .after(
                                        '<div class="error text-danger mb-0 invalid-input small">' +
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
@endsection
