@php
    $links = [
        [
            'title' => 'Listing',
            'route' => 'classified.ads.list',
            'active' => false,
        ],
        [
            'title' => 'Listing Custom Fields',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Listing Custom Fields
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Listing Custom Fields" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Listing Custom Fields') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#create-item-modal">{{ __tr('Create New Custom Field') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Type') }}</th>
                                        <th>{{ __tr('Category') }}</th>
                                        <th>{{ __tr('Options') }}</th>
                                        <th>{{ __tr('Is Required') }}</th>
                                        <th>{{ __tr('Is Filterable') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-center">{{ __tr('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($fields as $key=> $field)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $field->title }}</td>
                                            <td> {{ $field->get_type() }}</td>
                                            <td>
                                                @if ($field->category != null)
                                                    <a href="#" data-id="{{ $field->id }}"
                                                        class="attatch-category"><i class="icofont-wrench"></i>
                                                        {{ $field->category?->title }}
                                                    </a>
                                                @else
                                                    <a href="#" data-id="{{ $field->id }}"
                                                        class="attatch-category"><i class="icofont-plus-circle"></i>
                                                        {{ __tr('Add to a category') }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>

                                                @if ($field->has_options() == config('settings.general_status.active'))
                                                    <a
                                                        href="{{ route('classified.ads.custom.field.options', ['id' => $field->id]) }}">
                                                        <i class="icofont-ui-settings"></i>
                                                        {{ __tr('Options') }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($field->is_required == config('settings.general_status.active'))
                                                    <p class="badge badge-success">{{ __tr('Yes') }}</p>
                                                @else
                                                    <p class="badge badge-danger">{{ __tr('No') }}</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($field->is_filterable == config('settings.general_status.active'))
                                                    <p class="badge badge-success">{{ __tr('Yes') }}</p>
                                                @else
                                                    <p class="badge badge-danger">{{ __tr('No') }}</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($field->status == config('settings.general_status.active'))
                                                    <p class="badge badge-success">{{ __tr('Active') }}</p>
                                                @else
                                                    <p class="badge badge-danger">{{ __tr('Inactive') }}</p>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default">{{ __tr('Action') }}
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-default dropdown-toggle dropdown-hover dropdown-icon"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        @if ($field->has_options() == config('settings.general_status.active'))
                                                            <a class="dropdown-item edit-item"
                                                                href="{{ route('classified.ads.custom.field.options', ['id' => $field->id]) }}">
                                                                {{ __tr('Options') }}</a>
                                                            <div class="dropdown-divider"></div>
                                                        @endif
                                                        <a class="dropdown-item"
                                                            href="{{ route('classified.ads.custom.field.edit.page', ['id' => $field->id, 'lang' => defaultLangCode()]) }}">
                                                            {{ __tr('Edit') }}
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item delete-item"
                                                            data-id="{{ $field->id }}">
                                                            {{ __tr('Delete') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10">
                                                <div class="text-center">{{ __tr('No item found') }}</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($fields->hasPages())
                                <div class="p-3">
                                    {{ $fields->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--New  Modal-->
        <div class="modal fade" id="create-item-modal">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('New Custom Field') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="item-adding-form">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="black font-14">{{ __tr('Type') }}</label>
                                    <select name="type" class="form-control text-capitalize">
                                        @foreach (config('settings.input_types') as $key => $value)
                                            <option value="{{ $value }}">
                                                {{ ucwords(str_replace('_', ' ', $key)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="black font-14">{{ __tr('Title') }}</label>
                                    <input type="text" name="title" class="form-control slugable_input"
                                        placeholder="{{ __tr('Enter title') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="black font-14">{{ __tr('Default Value') }}</label>
                                    <input type="text" name="default_value" class="form-control slugable_input"
                                        placeholder="{{ __tr('Enter Default Value') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-lg-12 ">
                                    <label class="switch glow primary medium mr-2">
                                        <input type="checkbox" name="is_required">
                                        <span class="control"></span>
                                    </label>
                                    <label class="black font-14">{{ __tr('Is Required ?') }}</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="switch glow primary medium mr-2">
                                        <input type="checkbox" name="is_filterable">
                                        <span class="control"></span>
                                    </label>
                                    <label class="black font-14">{{ __tr('Is Filterable ?') }}</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="black font-14">{{ __tr('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="{{ config('settings.general_status.active') }}">
                                            {{ __tr('Active') }}
                                        </option>
                                        <option value="{{ config('settings.general_status.in_active') }}">
                                            {{ __tr('Inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="btn-area d-flex justify-content-between">
                                <button class="btn btn-primary mt-2 store-category">{{ __tr('Save') }}</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!--End New  Modal-->
        <!-- Edit Modal-->
        <div class="modal fade" id="edit-item-modal">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('Custom Field Information') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body item-edit-content">

                    </div>
                </div>
            </div>
        </div>
        <!--End  Edit Modal-->
        <!-- Delete Modal-->
        <div class="modal fade" id="user-delete-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete ?') }}</h4>
                        <form method="POST" action="{{ route('classified.ads.condition.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-item-id" name="id">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End  Delete Modal-->
        <!--Assign category-->
        <div id="category-modal" class="category-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Category') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="assign-category-form">
                            <input type="hidden" id="selected-field-id" name="id">
                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="font-14 bold black w-100">{{ __tr('Select category') }} </label>
                                    <select class="category-options form-control w-100" name="category">
                                    </select>
                                </div>
                            </div>
                            <div class="form-row d-flex justify-content-between">
                                <button class="btn btn-primary assign-category">{{ __tr('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End Assign Category-->
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            $('.category-options').select2({
                theme: "bootstrap4",
                placeholder: '{{ __tr('Select a category') }}',
                closeOnSelect: true,
                ajax: {
                    url: '{{ route('classified.ads.categories.options') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });

            //Create new 
            $('#item-adding-form').submit(function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $(document).find(".form-control").removeClass('is-invalid');
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    data: formData,
                    url: '{{ route('classified.ads.custom.field.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New custom field added successfully', 'Success');
                            location.reload();

                        } else {
                            toastr.error(response.message, 'Error')
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name,
                                error) {
                                $(document).find('[name=' + field_name + ']')
                                    .addClass('is-invalid');
                                $(document).find('[name=' + field_name + ']')
                                    .after(
                                        '<div class="error text-danger mb-0 invalid-input">' +
                                        error + '</div>');
                            })
                        } else {
                            toastr.error('custom field add failed', 'Error')
                        }
                    }
                });
            });

            //Visible user edit modal
            $('.edit-item').on('click', function(e) {
                e.preventDefault();
                let item_id = $(this).data('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    url: '{{ route('classified.ads.custom.field.edit') }}',
                    data: {
                        id: item_id
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.item-edit-content').html(response.html);
                            $('#edit-item-modal').modal('show');
                        } else {
                            toastr.error('Custom field fetch failed', 'Error')
                        }
                    },
                    error: function(response) {
                        toastr.error('Custom field fetch failed', 'Error')
                    }
                });
            });


            //Open Category Modal
            $('.attatch-category').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $("#selected-field-id").val(id);
                $("#category-modal").modal("show");
            });

            //Assign category
            $(document).on('click', '.assign-category', function(e) {
                $(document).find('.invalid-input').remove();
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $("#assign-category-form").serialize(),
                    url: '{{ route('classified.ads.custom.field.assign.category') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ __tr('Category  assigned successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ __tr('Category  assign failed') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.theme-input-style').after(
                                    '<div class="invalid-input d-flex">' + error +
                                    '</div>')
                            })
                        } else {
                            toastr.error('{{ __tr('Category  assign failed') }}');
                        }
                    }
                });
            });
            //update category
            $(document).on('submit', '#editForm', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $(document).find(".form-control").removeClass('is-invalid');
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    data: formData,
                    url: '{{ route('classified.ads.custom.field.update') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Custom field updated successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name,
                                error) {
                                $(document).find('[name=' + field_name + ']')
                                    .addClass('is-invalid');
                                $(document).find('[name=' + field_name + ']')
                                    .after(
                                        '<div class="error text-danger mb-0 invalid-input">' +
                                        error + '</div>');
                            })
                        } else {
                            toastr.error('Custom field update failed', 'Error')
                        }
                    }
                });
            });


            //Visible user delete modal
            $('.delete-item').on('click', function(e) {
                e.preventDefault();
                let user_id = $(this).data('id');
                $('#delete-item-id').val(user_id);
                $('#user-delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
