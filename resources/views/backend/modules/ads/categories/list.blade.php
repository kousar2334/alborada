@php
    $links = [
        [
            'title' => 'Listing',
            'route' => 'classified.ads.list',
            'active' => false,
        ],
        [
            'title' => 'Listing Categories',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Listing Categories
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('/public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Listing Categories" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Listing Categories') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#create-item-modal">{{ __tr('Create New Category') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Parent') }}</th>
                                        <th>{{ __tr('Icon') }}</th>
                                        <th>{{ __tr('Image') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $key=> $category)
                                        <tr>
                                            <td>
                                                {{ $key + 1 }}
                                            </td>
                                            <td>
                                                {{ $category->title }}
                                            </td>
                                            <td>
                                                @if ($category->parentCategory != null)
                                                    {{ $category->parentCategory?->title }}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td>
                                                <img src="{{ asset(getFilePath($category->icon, true)) }}" class="img-md"
                                                    alt="{{ $category->title }}">
                                            </td>
                                            <td>
                                                <img src="{{ asset(getFilePath($category->image, true)) }}" class="img-md"
                                                    alt="{{ $category->title }}">
                                            </td>

                                            <td>
                                                @if ($category->status == config('settings.general_status.active'))
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
                                                            href="{{ route('classified.ads.categories.edit.page', ['id' => $category->id]) }}">
                                                            {{ __tr('Edit') }}
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item delete-item"
                                                            data-id="{{ $category->id }}">
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
                            @if ($categories->hasPages())
                                <div class="p-3">
                                    {{ $categories->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
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
                        <h5 class="modal-title">{{ __tr('New Category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-category-form">
                            <div class="form-row mb-20">
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Icon') }}</label>
                                    <x-media name="icon"></x-media>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Featured Image') }}</label>
                                    <x-media name="image"></x-media>
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
                                    <label class="font-14 bold black w-100">{{ __tr('Parent') }} </label>
                                    <select class="parent-options form-control w-100" name="parent">
                                    </select>
                                    @if ($errors->has('parent'))
                                        <div class="invalid-input">{{ $errors->first('parent') }}</div>
                                    @endif
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
                        <form method="POST" action="{{ route('classified.ads.categories.delete') }}">
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
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('/public/web-assets/backend/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
            initParentSelect();

            //Create new 
            $('#new-category-form').submit(function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $(document).find(".form-control").removeClass('is-invalid');
                var formData = new FormData(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: formData,
                    url: '{{ route('classified.ads.categories.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New category added successfully', 'Success');
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
                            toastr.error('Category add failed', 'Error')
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

            function initParentSelect() {
                $('.parent-options').select2({
                    theme: "bootstrap4",
                    placeholder: '{{ __tr('Select parent category') }}',
                    closeOnSelect: true,
                    width: '100%',
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
            }
        })(jQuery);
    </script>
@endsection
