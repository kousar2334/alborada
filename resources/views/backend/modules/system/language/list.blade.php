@php
    $links = [
        [
            'title' => 'System',
            'route' => 'admin.system.settings.environment',
            'active' => false,
        ],
        [
            'title' => 'Languages',
            'route' => 'admin.system.settings.language.list',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Languages') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="Languages" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __tr('Languages') }}</h3>
                                    <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                        data-target="#language-create-modal">
                                        {{ __tr('Create New Language') }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        @if ($languages->count() > 0)
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __tr('#') }}</th>
                                                        <th>{{ __tr('Name') }}</th>
                                                        <th>{{ __tr('Native Name') }}</th>
                                                        <th>{{ __tr('Code') }}</th>
                                                        <th>{{ __tr('Status') }}</th>
                                                        <th class="text-right">{{ __tr('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($languages as $key => $language)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>

                                                            <td>{{ $language->title }}</td>
                                                            <td>{{ $language->native_title }}</td>
                                                            <td>{{ $language->code }}</td>
                                                            <td>
                                                                @if ($language->status == config('settings.general_status.active'))
                                                                    <p class=" badge badge-success">
                                                                        {{ __tr('Active') }}
                                                                    </p>
                                                                @else
                                                                    <p class=" badge badge-danger">
                                                                        {{ __tr('Inactive') }}</p>
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                <div class="btn-group">
                                                                    <button type="button"
                                                                        class="btn btn-default">{{ __tr('Action') }}
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-default dropdown-toggle dropdown-hover dropdown-icon"
                                                                        data-toggle="dropdown" aria-expanded="false">
                                                                    </button>
                                                                    <div class="dropdown-menu" role="menu">
                                                                        <a href="{{ route('admin.system.settings.language.translation', $language->id) }}"
                                                                            class="dropdown-item">
                                                                            {{ __tr('Translation') }}
                                                                        </a>
                                                                        <div class="dropdown-divider"></div>
                                                                        <button class="dropdown-item edit-language"
                                                                            data-id="{{ $language->id }}">
                                                                            {{ __tr('Edit') }}
                                                                        </button>
                                                                        <div class="dropdown-divider"></div>
                                                                        <button class="dropdown-item delete-language"
                                                                            data-id="{{ $language->id }}">
                                                                            {{ __tr('Delete') }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="text-center">
                                                <h2>{{ __tr('No Language Found') }}</h2>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--Create Modal-->
        <div class="modal fade" id="language-create-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('New Language') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-language-form">
                            @csrf
                            <div class="form-group">
                                <label>{{ __tr('Name') }}</label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="{{ __tr('Enter Name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Native Name') }}</label>
                                <input type="text" class="form-control" name="native_name"
                                    placeholder="{{ __tr('Enter Native Name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Code') }}</label>
                                <input type="code" class="form-control" name="code"
                                    placeholder="{{ __tr('Enter code') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Icon') }}</label>
                                <x-media name="icon" value=""></x-media>
                            </div>
                        </form>
                        <div class="d-flex justify-content-between">
                            <button type="button"
                                class="btn btn-primary language-create-btn">{{ __tr('Save Language') }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--End Create Model-->

        <!--Delete Modal-->
        <div class="modal fade" id="delete-modal">
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
                        <form method="POST" action="{{ route('admin.system.settings.language.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-id" name="id">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End Delete Modal-->

        <!--Edit Modal-->
        <div class="modal fade" id="edit-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('Language Information') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body edit-content">

                    </div>
                </div>
            </div>
        </div>
        <!--End Edit Modal-->
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";
            //Create new Language 
            $('.language-create-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-language-form').serialize(),
                    url: '{{ route('admin.system.settings.language.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New Language created successfully', 'Success');
                            $('#Language-create-modal').modal('hide');
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
                            toastr.error('Language create failed', 'Error');
                        }
                    }
                });
            });

            /**
             * Open Delete modal 
             **/
            $('.delete-language').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $("#delete-id").val(id);
                $("#delete-modal").modal('show');
            });

            //Open edit form 
            $('.edit-language').on('click', function(e) {
                e.preventDefault();
                let lang_id = $(this).data('id')
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: lang_id
                    },
                    url: '{{ route('admin.system.settings.language.edit') }}',
                    success: function(response) {
                        if (response.success) {
                            $(".edit-content").html(response.data);
                            $("#edit-modal").modal('show');
                        } else {
                            toastr.error(response.message, 'Error')
                        }
                    },
                    error: function(response) {
                        toastr.error('Language Not Found', 'Error');
                    }
                });
            });

            //Upadte Language 
            $(document).on('click', '.language-update-btn', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#language-update-form').serialize(),
                    url: '{{ route('admin.system.settings.language.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Language updated successfully', 'Success');
                            $('#edit-modal').modal('hide');
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
                            toastr.error('Language update failed', 'Error');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
