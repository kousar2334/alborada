@php
    $links = [['title' => 'Report Reasons', 'route' => 'classified.ads.report.reasons.list', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Report Reasons') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Report Reasons" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Report Reasons') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#new-reason-modal">
                                {{ __tr('Add New Reason') }}
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
                                    @forelse ($reasons as $key => $reason)
                                        <tr>
                                            <td>{{ $reasons->firstItem() + $key }}</td>
                                            <td class="text-capitalize">
                                                {{ $reason->translation('title') }}
                                            </td>
                                            <td>
                                                @if ($reason->status == config('settings.general_status.active'))
                                                    <span class="badge badge-success">{{ __tr('Active') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __tr('Inactive') }}</span>
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
                                                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('classified.ads.report.reasons.edit', ['id' => $reason->id, 'lang' => defaultLangCode()]) }}">
                                                            {{ __tr('Edit') }}
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item delete-reason" href="#"
                                                            data-id="{{ $reason->id }}">
                                                            {{ __tr('Delete') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <p class="alert alert-default-danger text-center">
                                                    {{ __tr('No Item Found') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($reasons->hasPages())
                                <div class="py-3">
                                    {{ $reasons->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add New Reason Modal --}}
        <div class="modal fade" id="new-reason-modal">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Add New Report Reason') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-reason-form">
                            <div class="form-group">
                                <label>{{ __tr('Title') }}</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="{{ __tr('Enter reason title') }}">
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
                            <button type="button"
                                class="btn btn-success mt-2 store-reason-btn">{{ __tr('Save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div class="modal fade" id="delete-reason-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete this reason?') }}</h4>
                        <form method="POST" action="{{ route('classified.ads.report.reasons.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-reason-id" name="id">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

            // Store new reason via AJAX
            $(document).on('click', '.store-reason-btn', function(e) {
                $(document).find('.invalid-input').remove();
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route('classified.ads.report.reasons.store') }}',
                    data: $('#new-reason-form').serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ __tr('Report reason created successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ __tr('Failed to create reason') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field, error) {
                                $('[name=' + field + ']').closest('.form-group').append(
                                    '<div class="invalid-input text-danger mt-1">' +
                                    error +
                                    '</div>');
                            });
                        } else {
                            toastr.error('{{ __tr('Failed to create reason') }}');
                        }
                    }
                });
            });

            // Show delete confirmation modal
            $('.delete-reason').on('click', function(e) {
                e.preventDefault();
                $('#delete-reason-id').val($(this).data('id'));
                $('#delete-reason-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
