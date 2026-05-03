@php
    $links = [
        [
            'title' => 'Blogs',
            'route' => 'admin.blogs.list',
            'active' => false,
        ],
        [
            'title' => 'Comments',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Blog Comments') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Comments" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Comments') }}</h3>

                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('#') }}</th>
                                        <th>{{ __tr('Author') }}</th>
                                        <th>{{ __tr('Comment') }}</th>
                                        <th>{{ __tr('Blog') }}</th>
                                        <th>{{ __tr('Date') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($comments->count() > 0)
                                        @foreach ($comments as $key => $comment)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    @if ($comment->commentAuthor != null)
                                                        {{ $comment->commentAuthor->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $comment->comment }}</td>
                                                <td>
                                                    @if ($comment->blog != null)
                                                        <a href="{{ route('frontend.new.details', ['permalink' => $comment->blog->permalink]) }}"
                                                            target="_blank">
                                                            {{ $comment->blog->title }}</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $comment->created_at->format('m D Y h:i:sA') }}
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
                                                            <a class="dropdown-item delete-comment" href="#"
                                                                data-id="{{ $comment->id }}">
                                                                {{ __tr('Delete') }}
                                                            </a>
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
                                {{ $comments->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Modal-->
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
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete ?') }}</h4>
                        <form method="POST" action="{{ route('admin.blogs.comment.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-comment-id" name="id">
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
            //Visible  delete modal
            $('.delete-comment').on('click', function(e) {
                e.preventDefault();
                let category_id = $(this).data('id');
                $('#delete-comment-id').val(category_id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
