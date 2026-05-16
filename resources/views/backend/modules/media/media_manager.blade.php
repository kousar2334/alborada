@php
    $links = [
        [
            'title' => 'Media Manager',
            'route' => 'admin.media.list',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Media Manager') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Media Manager" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="media-manager-wrap">
                <div class="card">
                    <div class="card-body">
                        @include('backend.media.media_list')
                    </div>
                </div>
            </div>

            {{-- Delete Modal --}}
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
                            <form method="POST" action="{{ route('admin.media.delete') }}">
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
            {{-- End Delete Modal --}}
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        (function() {
            initMediaManager();
            getMediaItemsList();

            $(document).on('click', '.delete-file', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#delete-id').val(id);
                $('#delete-modal').modal('show');
            });
        })();
    </script>
@endsection
