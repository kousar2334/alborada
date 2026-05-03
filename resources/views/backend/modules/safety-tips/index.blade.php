@php
    $links = [
        [
            'title' => 'Safety Tips',
            'route' => 'classified.settings.safety.tips.list',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Safety Tips') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Safety Tips" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Safety Tips') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#new-tip-model">{{ __tr('Add New Tips') }}
                            </button>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($tips->count() > 0)
                                        @foreach ($tips as $key => $tip)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="text-capitalize">
                                                    {{ $tip->translation('title') }}
                                                </td>
                                                <td>
                                                    @if ($tip->status == config('settings.general_status.active'))
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
                                                            <a class="dropdown-item"
                                                                href="{{ route('classified.settings.safety.tips.edit', ['id' => $tip->id, 'lang' => defaultLangCode()]) }}">
                                                                {{ __tr('Edit') }}
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item delete-tip" href="#"
                                                                data-tip="{{ $tip->id }}">
                                                                {{ __tr('Delete') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">
                                                <p class="alert alert-default-danger text-center">
                                                    {{ __tr('No Item Found') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="py-3">
                                {{ $tips->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Tip adding Modal-->
        <div class="modal fade" id="new-tip-model">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Create New Safety Tips') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-tip-form">
                            <div class="form-group">
                                <label>{{ __tr('Title') }}</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="{{ __tr('Enter title') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="{{ config('settings.general_status.active') }}">
                                        {{ __tr('Active') }}
                                    </option>
                                    <option value="{{ config('settings.general_status.in_active') }}">
                                        {{ __tr('Inactive') }}
                                    </option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-success mt-2 store-new-tip">{{ __tr('Save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End tip adding modal-->
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
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete this safety tips') }}?</h4>
                        <form method="POST" action="{{ route('classified.settings.safety.tips.delete') }}">
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
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.store-new-tip', function(e) {
                $(document).find('.invalid-input').remove();
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $("#new-tip-form").serialize(),
                    url: '{{ route('classified.settings.safety.tips.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(
                                '{{ __tr('Safety tips created successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ __tr('Safety tips create failed') }}');
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
                            toastr.error('{{ __tr('Safety tips create failed') }}');
                        }
                    }
                });
            });

            $('.delete-tip').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('tip');
                $('#delete-id').val(id);
                $("#delete-modal").modal("show");
            });
        })(jQuery);
    </script>
@endsection
