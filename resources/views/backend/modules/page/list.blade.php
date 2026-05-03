@php
    $links = [
        [
            'title' => 'Blogs',
            'route' => 'admin.page.list',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Pages') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="Pages" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Pages') }}</h3>
                            <a class="btn btn-success btn-sm float-right text-white"
                                href="{{ route('admin.page.create') }}">{{ __tr('Create New Page') }}</a>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Author') }}</th>
                                        <th>{{ __tr('Created') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($pages->count() > 0)
                                        @foreach ($pages as $key => $page)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="text-capitalize page-title">
                                                    <a href="{{ route('frontend.page.single.preview', ['permalink' => $page->permalink]) }}"
                                                        target="_blank">
                                                        {{ $page->title }}
                                                    </a>
                                                    @if (get_setting('home_page_id') == $page->id)
                                                        <span class="text-black">- {{ __tr('Home Page') }}</span>
                                                    @endif
                                                    @if (get_setting('contact_page_id') == $page->id)
                                                        <span class="text-black">- {{ __tr('Contact Page') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($page->authorInfo != null)
                                                        {{ $page->authorInfo->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $page->created_at->format('M d Y h:i:sA') }}
                                                </td>
                                                <td>
                                                    @if ($page->status == config('settings.page_status.active'))
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
                                                                href="{{ route('admin.page.edit', ['page' => $page->id, 'lang' => defaultLangCode()]) }}">
                                                                {{ __tr('Edit Page') }}
                                                            </a>
                                                            @can('Delete Page')
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item delete-item" href="#"
                                                                    data-id="{{ $page->id }}">
                                                                    {{ __tr('Delete Page') }}
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
                                {{ $pages->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete?') }}</h4>
                        <form method="POST" action="{{ route('admin.page.delete') }}">
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
            //Visible delete modal
            $('.delete-item').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $('#delete-item-id').val(id);
                $('#delete-modal').modal('show');
            });

        })(jQuery);
    </script>
@endsection
