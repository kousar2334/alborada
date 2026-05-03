@php
    $links = [
        [
            'title' => 'Listing',
            'route' => 'classified.ads.list',
            'active' => false,
        ],
        [
            'title' => 'Listing Custom Fields',
            'route' => 'classified.ads.custom.field.list',
            'active' => false,
        ],
        [
            'title' => 'Custom Field Options',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Custom Field's Options
@endsection
@section('page-content')
    <x-admin-page-header title="Custom Field's Options" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Custom Field Options') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#new-option-modal">{{ __tr('Create New Custom Field') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Value') }}</th>
                                        <th>{{ __tr('Field') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-center">{{ __tr('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($options->count() > 0)
                                        @foreach ($options as $key => $option)
                                            <tr>
                                                <td>
                                                    {{ $key + 1 }}
                                                </td>
                                                <td>
                                                    {{ $option->value }}
                                                </td>
                                                <td>{{ $option->field?->title }}</td>
                                                <td>
                                                    @if ($option->status == config('settings.general_status.active'))
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

                                                            <a class="dropdown-item"
                                                                href="{{ route('classified.ads.custom.field.options.edit.page', ['id' => $option->id, 'lang' => defaultLangCode()]) }}">
                                                                {{ __tr('Edit') }}
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item delete-item"
                                                                data-id="{{ $option->id }}">
                                                                {{ __tr('Delete') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9">
                                                <p class="alert alert-danger text-center">
                                                    {{ __tr('Nothing Found') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            @if ($options->hasPages())
                                <div class="p-3">
                                    {{ $options->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Option adding Modal-->
    <div class="modal fade new-option-modal" id="new-option-modal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __tr('New Custom Field') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-Option-form">
                        <div class="form-row">
                            <div class="form-group col-lg-12">
                                <label class="black font-14">{{ __tr('Value') }}</label>
                                <input type="text" name="value" class="form-control"
                                    placeholder="{{ __tr('Enter value') }}">
                                <input type="hidden" name="field" value="{{ $field->id }}">
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
                            <button class="btn btn-primary mt-2 store-option">{{ __tr('Save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!--End Option adding modal-->
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
    <div class="modal fade" id="item-delete-modal">
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
                    <form method="POST" action="{{ route('classified.ads.custom.field.options.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-item-id" name="id">
                        <input type="hidden" name="field_id" value="{{ $field->id }}">
                        <button type="button" class="btn mt-2 btn-danger"
                            data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End  Delete Modal-->
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

            /**
             * Store New option 
             *
             **/
            $(document).on('click', '.store-option', function(e) {
                $(document).find('.invalid-input').remove();
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $("#new-Option-form").serialize(),
                    url: '{{ route('classified.ads.custom.field.options.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ __tr('Option created successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ __tr('Option create failed') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.form-control').after(
                                    '<div class="invalid-input d-flex">' + error +
                                    '</div>')
                            })
                        } else {
                            toastr.error('{{ __tr('Option create failed') }}');
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
                    url: '{{ route('classified.ads.custom.field.options.edit') }}',
                    data: {
                        id: item_id
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.item-edit-content').html(response.html);
                            $('#edit-item-modal').modal('show');
                        } else {
                            toastr.error('Option fetch failed', 'Error')
                        }
                    },
                    error: function(response) {
                        toastr.error('Option fetch failed', 'Error')
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
                    url: '{{ route('classified.ads.custom.field.options.update') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Option updated successfully', 'Success');
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
                            toastr.error('Option update failed', 'Error')
                        }
                    }
                });
            });


            //Visible user delete modal
            $('.delete-item').on('click', function(e) {
                e.preventDefault();
                let item_id = $(this).data('id');
                $('#delete-item-id').val(item_id);
                $('#item-delete-modal').modal('show');
            });

        })(jQuery);
    </script>
@endsection
