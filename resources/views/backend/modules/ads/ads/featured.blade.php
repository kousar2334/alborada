@php
    $links = [
        [
            'title' => 'Ad Listings',
            'route' => 'classified.ads.list',
            'active' => false,
        ],
        [
            'title' => 'Featured Ad Listings',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Featured Ad Listings
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('/public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Featured Ad Listings" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr(' Featured Ad Listings') }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Image') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Category') }}</th>
                                        <th>{{ __tr('Member') }}</th>
                                        <th>{{ __tr('Location') }}</th>
                                        <th>{{ __tr('Price') }}</th>
                                        <th>{{ __tr('Post Date') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ads as $key=> $ad)
                                        <tr>
                                            <td>
                                                {{ $key + 1 }}
                                            </td>
                                            <td>
                                                <img src="{{ asset(getFilePath($ad->thumbnail_image, true, '240x160')) }}"
                                                    class="img-md" alt="{{ $ad->title }}">
                                            </td>
                                            <td>
                                                <span class="ad-title">{{ $ad->title }}</span>
                                            </td>
                                            <td>{{ $ad->categoryInfo?->title }}</td>
                                            <td>
                                                {{ $ad->userInfo?->name }}
                                            </td>
                                            <td>
                                                {{ $ad->location() }}
                                            </td>
                                            <td>{{ $ad->price }}</td>
                                            <td>
                                                {{ $ad->created_at->format('d M Y') }}
                                            </td>
                                            <td>
                                                @if ($ad->status == config('settings.general_status.active'))
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
                                                        <button class="dropdown-item edit-item">
                                                            {{ __tr('Edit') }}
                                                        </button>
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item delete-item"
                                                            data-id="{{ $ad->id }}">
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
                            @if ($ads->hasPages())
                                <div class="p-3">
                                    {{ $ads->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
