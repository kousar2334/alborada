@php
    $links = [
        [
            'title' => 'Listing',
            'route' => 'classified.ads.list',
            'active' => false,
        ],
        [
            'title' => 'Listing Tags',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Listing Tags
@endsection
@section('page-content')
    <x-admin-page-header title="Listing Tags" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Listing Tags') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#create-item-modal">{{ __tr('Create New Tags') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tags as $key=> $item)
                                        <tr>
                                            <td>
                                                {{ $key + 1 }}
                                            </td>
                                            <td>
                                                {{ $item->title }}
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

                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item delete-item"
                                                            data-id="{{ $item->id }}">
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
                            @if ($tags->hasPages())
                                <div class="p-3">
                                    {{ $tags->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
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
                        <h5 class="modal-title">{{ __tr('New Tags') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="item-adding-form">
                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="black font-14">{{ __tr('Tags') }}</label>
                                    <textarea name="tags" class="form-control" placeholder="Enter tags"></textarea>
                                    <small>{{ __tr('Separated tags by comma') }}</small>
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
                        <form method="POST" action="{{ route('classified.ads.tag.delete') }}">
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
    <script>
        (function($) {
            "use strict";

            //Create new 
            $('#item-adding-form').submit(function(e) {
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
                    url: '{{ route('classified.ads.tag.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New Tags added successfully', 'Success');
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
                            toastr.error('Tags add failed', 'Error')
                        }
                    }
                });
            }); //Visible user delete modal
            $('.delete-item').on('click', function(e) {
                e.preventDefault();
                let user_id = $(this).data('id');
                $('#delete-item-id').val(user_id);
                $('#user-delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
