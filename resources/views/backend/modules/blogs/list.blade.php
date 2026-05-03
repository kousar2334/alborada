@php
    $links = [
        [
            'title' => 'Blogs',
            'route' => 'admin.blogs.list',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Blogs') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="Blogs" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Blogs') }}</h3>
                            @can('Create New Blog')
                                <a class="btn btn-success btn-sm float-right text-white" href="{{ route('admin.blogs.create') }}">
                                    {{ __tr('Create New Blog') }}
                                </a>
                            @endcan
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Title') }}</th>
                                        <th>{{ __tr('Author') }}</th>
                                        <th>{{ __tr('Published At') }}</th>
                                        <th>{{ __tr('Featured') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($blogs->count() > 0)
                                        @foreach ($blogs as $key => $blog)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="text-capitalize">
                                                    <a href="{{ route('frontend.new.details', ['permalink' => $blog->permalink]) }}"
                                                        target="_blank">
                                                        {{ $blog->translation('title', getLocale()) }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($blog->authorInfo != null)
                                                        {{ $blog->authorInfo->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $blog->created_at->format('d M Y') }}
                                                </td>
                                                <td>
                                                    @if ($blog->is_featured == config('settings.general_status.active'))
                                                        <p class="badge badge-success">{{ __tr('Active') }}</p>
                                                    @else
                                                        <p class="badge badge-danger">{{ __tr('Inactive') }}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($blog->status == config('settings.blog_status.publish'))
                                                        <p class="badge badge-success">{{ __tr('Published') }}</p>
                                                    @endif
                                                    @if ($blog->status == config('settings.blog_status.unpublish'))
                                                        <p class="badge badge-danger">{{ __tr('Unpublish') }}</p>
                                                    @endif
                                                    @if ($blog->status == config('settings.blog_status.draft'))
                                                        <p class="badge badge-secondary">{{ __tr('Draft') }}</p>
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
                                                                href="{{ route('admin.blogs.edit', ['blog' => $blog->id, 'lang' => defaultLangCode()]) }}">
                                                                {{ __tr('Edit') }}
                                                            </a>
                                                            @can('Delete Blog')
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item delete-blog" href="#"
                                                                    data-id="{{ $blog->id }}">
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
                                            <td colspan="7">
                                                <p class="alert alert-default-danger text-center">
                                                    {{ __tr('No Item Found') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="py-3">
                                {{ $blogs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Delete Modal-->
        <div class="modal fade" id="blog-delete-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete blog ?') }}</h4>
                        <form method="POST" action="{{ route('admin.blogs.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-blog-id" name="id">
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
            //Visible blog delete modal
            $('.delete-blog').on('click', function(e) {
                e.preventDefault();
                let blog_id = $(this).data('id');
                $('#delete-blog-id').val(blog_id);
                $('#blog-delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
