@php
    $links = [
        [
            'title' => 'Pricing Plans',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Pricing Plans
@endsection
@section('page-content')
    <x-admin-page-header title="Pricing Plans" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Pricing Plans') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#create-item-modal">{{ __tr('Create New Plan') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Duration') }}</th>
                                        <th>{{ __tr('Price') }}</th>
                                        <th>{{ __tr('Offer Price') }}</th>
                                        <th>{{ __tr('Connections') }}</th>
                                        <th>{{ __tr('Quality') }}</th>
                                        <th>{{ __tr('Catch-up') }}</th>
                                        <th>{{ __tr('DVR') }}</th>
                                        <th>{{ __tr('Trial') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($plans as $key => $plan)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $plan->title }}</td>
                                            <td>{{ $plan->duration_days }} {{ __tr('days') }}</td>
                                            <td>{{ $plan->price }}</td>
                                            <td>
                                                @if ($plan->has_offer)
                                                    <span class="badge badge-success">{{ $plan->offer_price }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $plan->max_connections }}</td>
                                            <td><span class="badge badge-info">{{ $plan->streaming_quality }}</span></td>
                                            <td>{{ $plan->catchup_days }} {{ __tr('days') }}</td>
                                            <td>
                                                @if ($plan->dvr_enabled)
                                                    <span class="badge badge-success">{{ __tr('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ __tr('No') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($plan->is_trial)
                                                    <span class="badge badge-warning">{{ __tr('Trial') }}</span>
                                                @else
                                                    <span class="badge badge-light">{{ __tr('No') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($plan->status == config('settings.general_status.active'))
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
                                                        <button class="dropdown-item edit-item"
                                                            data-id="{{ $plan->id }}">
                                                            {{ __tr('Edit') }}
                                                        </button>
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item delete-item"
                                                            data-id="{{ $plan->id }}">
                                                            {{ __tr('Delete') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12">
                                                <div class="text-center">{{ __tr('No item found') }}</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($plans->hasPages())
                                <div class="p-3">
                                    {{ $plans->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Create Modal-->
        <div class="modal fade" id="create-item-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('New Pricing Plan') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-plan-form">
                            <div class="form-row">
                                <div class="form-group col-lg-12">
                                    <label class="black font-14">{{ __tr('Title') }} *</label>
                                    <input type="text" name="title" class="form-control"
                                        placeholder="{{ __tr('Enter plan title') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Duration (Days)') }} *</label>
                                    <input type="number" name="duration_days" class="form-control" min="1"
                                        value="30" placeholder="{{ __tr('Enter duration in days') }}">
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Regular Price') }} *</label>
                                    <input type="number" name="price" class="form-control" min="0" step="0.01"
                                        value="0" placeholder="{{ __tr('Enter price') }}">
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Offer Price') }}</label>
                                    <input type="number" name="offer_price" class="form-control" min="0"
                                        step="0.01" placeholder="{{ __tr('Optional promo price') }}">
                                    <small class="text-muted">{{ __tr('Shown as a limited-time offer; leave empty for no offer') }}</small>
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-muted mb-3">{{ __tr('IPTV Settings') }}</h6>

                            <div class="form-row">
                                <div class="form-group col-lg-4">
                                    <label class="black font-14">{{ __tr('Max Connections') }} *</label>
                                    <input type="number" name="max_connections" class="form-control" min="1"
                                        max="99" value="1">
                                </div>
                                <div class="form-group col-lg-4">
                                    <label class="black font-14">{{ __tr('Streaming Quality') }} *</label>
                                    <select name="streaming_quality" class="form-control">
                                        <option value="SD">SD</option>
                                        <option value="HD" selected>HD</option>
                                        <option value="FHD">FHD</option>
                                        <option value="4K">4K</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label class="black font-14">{{ __tr('Catch-up Days') }} *</label>
                                    <input type="number" name="catchup_days" class="form-control" min="0"
                                        value="0">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('DVR Enabled') }}</label>
                                    <select name="dvr_enabled" class="form-control">
                                        <option value="0">{{ __tr('No') }}</option>
                                        <option value="1">{{ __tr('Yes') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Trial Plan') }}</label>
                                    <select name="is_trial" class="form-control">
                                        <option value="0">{{ __tr('No') }}</option>
                                        <option value="1">{{ __tr('Yes') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Trial Days') }}</label>
                                    <input type="number" name="trial_days" class="form-control" min="1"
                                        placeholder="{{ __tr('Leave empty if not trial') }}">
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Sort Order') }}</label>
                                    <input type="number" name="sort_order" class="form-control" min="0"
                                        value="0">
                                </div>
                            </div>

                            <hr>
                            <div class="form-row">
                                <div class="form-group col-lg-6">
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
                                <button class="btn btn-primary mt-2">{{ __tr('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End Create Modal-->

        <!--Edit Modal-->
        <div class="modal fade" id="edit-item-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('Edit Pricing Plan') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body item-edit-content">
                    </div>
                </div>
            </div>
        </div>
        <!--End Edit Modal-->

        <!--Delete Modal-->
        <div class="modal fade" id="delete-item-modal">
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
                        <form method="POST" action="{{ route('admin.pricing.plans.delete') }}">
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
        <!--End Delete Modal-->
    </section>
@endsection

@section('page-script')
    <script>
        (function($) {
            "use strict";

            // Create new plan
            $('#new-plan-form').submit(function(e) {
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
                    url: '{{ route('admin.pricing.plans.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New pricing plan added successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error('Pricing plan add failed', 'Error');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').addClass(
                                    'is-invalid');
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>');
                            });
                        } else {
                            toastr.error('Pricing plan add failed', 'Error');
                        }
                    }
                });
            });

            // Edit plan - load form via AJAX
            $('.edit-item').on('click', function(e) {
                e.preventDefault();
                let plan_id = $(this).data('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    url: '{{ route('admin.pricing.plans.edit') }}',
                    data: {
                        id: plan_id,
                        lang: '{{ defaultLangCode() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.item-edit-content').html(response.html);
                            $('#edit-item-modal').modal('show');
                        } else {
                            toastr.error('Plan fetch failed', 'Error');
                        }
                    },
                    error: function() {
                        toastr.error('Plan fetch failed', 'Error');
                    }
                });
            });

            // Update plan
            $(document).on('submit', '#editForm', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $(document).find(".form-control").removeClass('is-invalid');
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    data: formData,
                    url: '{{ route('admin.pricing.plans.update') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Pricing plan updated successfully', 'Success');
                            location.reload();
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name="' + field_name + '"]').addClass(
                                    'is-invalid');
                                $(document).find('[name="' + field_name + '"]').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>');
                            });
                        } else {
                            toastr.error('Pricing plan update failed', 'Error');
                        }
                    }
                });
            });

            // Delete plan
            $('.delete-item').on('click', function(e) {
                e.preventDefault();
                let plan_id = $(this).data('id');
                $('#delete-item-id').val(plan_id);
                $('#delete-item-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
