@php
    $links = [
        [
            'title' => 'Blogs',
            'route' => 'admin.blogs.list',
            'active' => false,
        ],
        [
            'title' => 'Categories',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Blog Categories') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Categories" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Categories') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#category-create-modal">{{ __tr('Add New Category') }}</button>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Parent') }}</th>
                                        <th>{{ __tr('Featured') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($categories->count() > 0)
                                        @foreach ($categories as $key => $category)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="text-capitalize">{{ $category->title }}</td>
                                                <td>
                                                    @if ($category->parentCat != null)
                                                        {{ $category->parentCat->title }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($category->is_featured == config('settings.general_status.active'))
                                                        <p class="badge badge-success">{{ __tr('Active') }}</p>
                                                    @else
                                                        <p class="badge badge-danger">{{ __tr('Inactive') }}</p>
                                                    @endif
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
                                                        <button type="button"
                                                            class="btn btn-default">{{ __tr('Action') }}</button>
                                                        <button type="button"
                                                            class="btn btn-default dropdown-toggle dropdown-hover dropdown-icon"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                        </button>
                                                        <div class="dropdown-menu" role="menu">
                                                            <a href="{{ route('admin.blogs.categories.edit', ['id' => $category->id, 'lang' => defaultLangCode()]) }}"
                                                                class="dropdown-item">
                                                                {{ __tr('Edit') }}
                                                            </a>
                                                            @can('Delete Blog Category')
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item delete-category" href="#"
                                                                    data-id="{{ $category->id }}">
                                                                    {{ __tr('Delete') }}
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">
                                                <p class="alert alert-default-danger text-center">
                                                    {{ __tr('No item found') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="py-3">
                                {{ $categories->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--New Catgory Modal-->
        <div class="modal fade" id="category-create-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('New Category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-category-form">
                            @csrf
                            <div class="form-group">
                                <label>{{ __tr('Category Title') }}</label>
                                <input type="text" class="form-control" name="title"
                                    placeholder="{{ __tr('Enter Category Title') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __tr('Parent Category') }}</label>
                                <select class="parent-category-select form-control" name="parent">

                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __tr('Meta Title') }}</label>
                                <input type="text" class="form-control" name="meta_title"
                                    placeholder="{{ __tr('Enter Meta Title') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Meta Description') }}</label>
                                <textarea class="form-control" name="meta_description"></textarea>
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Meta Image') }}</label>
                                <x-media name="meta_image" value=""></x-media>
                            </div>

                        </form>
                        <div class="d-flex justify-content-between">
                            <button type="button"
                                class="btn btn-primary store-new-category-btn">{{ __tr('Create Category') }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--End New Catgory Modal-->
        <!--Catgory Delete Modal-->
        <div class="modal fade" id="category-delete-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete category ?') }}</h4>
                        <form method="POST" action="{{ route('admin.blogs.categories.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-category-id" name="id">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End Catgory Delete Modal-->
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
            //Parent category options
            $('.parent-category-select').select2({
                theme: "bootstrap4",
                placeholder: 'Select parent category',
                ajax: {
                    url: '{{ route('admin.blogs.categories.dropdown.options') }}',
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

            //Store new category 
            $('.store-new-category-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-category-form').serialize(),
                    url: '{{ route('admin.blogs.categories.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Category created successfully', 'Success');
                            $('#category-create-modal').modal('hide');
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
                            toastr.error('Category create failed', 'Error');
                        }
                    }
                });
            });

            //Visible category delete modal
            $('.delete-category').on('click', function(e) {
                e.preventDefault();
                let category_id = $(this).data('id');
                $('#delete-category-id').val(category_id);
                $('#category-delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
