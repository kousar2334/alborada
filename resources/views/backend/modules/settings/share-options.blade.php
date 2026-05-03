@extends('backend.layouts.dashboard_layout')
@section('title')
    {{ __tr('Ad Share Options') }}
@endsection
@section('page-content')
    <div class="theme-option-container">
        @include('backend.modules.settings.includes.head')
        <div class="theme-option-tab-wrap">
            @include('backend.modules.settings.includes.tabs')
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-header align-items-center bg-white d-flex justify-content-between">
                            <h4>{{ __tr('Ad Share Options') }}</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="hoverable text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __tr('Title') }}</th>
                                            <th>{{ __tr('Status') }}</th>
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
                                                        {{ $option->network_name }}
                                                    </td>
                                                    <td>
                                                        <label class="switch glow primary medium">
                                                            <input type="checkbox" class="change-status"
                                                                data-option="{{ $option->id }}"
                                                                @checked($option->status == config('settings.general_status.active'))>
                                                            <span class="control"></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";
            /**
             * 
             * Change status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('option');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: id
                    },
                    url: '{{ route('classified.settings.share.options.status.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ __tr('Status updated successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ __tr('Status update failed') }}');
                        }
                    },
                    error: function(response) {
                        toastr.error('{{ __tr('Status update failed') }}');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
