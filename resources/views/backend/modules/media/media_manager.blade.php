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
@section('page-style')
    <style>
        .media-grid {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            min-height: 400px;
        }

        .grid-view {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .list-view {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .media-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e1e5e9;
            position: relative;
        }

        .media-item:hover {
            transform: translationY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .media-preview {
            width: 100%;
            height: 150px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #ccc;
            position: relative;
            overflow: hidden;
        }

        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .media-item:hover .media-preview img {
            transform: scale(1.05);
        }

        .media-info {
            padding: 15px;
        }

        .media-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            font-size: 1rem;
            word-break: break-word;
        }

        .media-details {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .media-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            opacity: 0;
            transform: translationY(10px);
            transition: all 0.3s ease;
        }

        .media-item:hover .media-actions {
            opacity: 1;
            transform: translationY(0);
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-download {
            background: #28a745;
            color: white;
        }

        .btn-download:hover {
            background: #218838;
            transform: translationY(-1px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translationY(-1px);
        }

        .btn-view {
            background: #007bff;
            color: white;
        }

        .btn-view:hover {
            background: #0056b3;
            transform: translationY(-1px);
        }

        .list-view .media-item {
            display: flex;
            align-items: center;
            padding: 15px;
        }

        .list-view .media-preview {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            margin-right: 15px;
            border-radius: 8px;
        }

        .list-view .media-info {
            flex: 1;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .list-view .media-actions {
            opacity: 0;
            transform: translationX(10px);
            transition: all 0.3s ease;
        }

        .list-view .media-item:hover .media-actions {
            opacity: 1;
            transform: translationX(0);
        }

        .list-view .media-details-container {
            flex: 1;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-text {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #666;
        }

        .empty-subtext {
            color: #999;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="Media Manager" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Media Manager') }}</h3>

                        </div>
                        <div class="card-body">
                            @if ($files->count() > 0)
                                <div id="mediaContainer" class="grid-view">
                                    @foreach ($files as $key => $file)
                                        <div class="media-item" data-type="image">
                                            <div class="media-preview">
                                                <img src="{{ asset(getFilePath($file->path, true)) }}">
                                            </div>
                                            <div class="media-info">

                                                <div class="media-name">{{ $file->title }}</div>
                                                <div class="media-details">{{ number_format($file->size, 2) }} KB •
                                                    {{ $file->created_at->format('d M Y') }}</div>

                                                <div class="media-actions">
                                                    <button class="action-btn btn-delete delete-file text-white"
                                                        data-id="{{ $file->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <tr>
                                    <td colspan="8">
                                        <p class="alert alert-default-danger text-center">
                                            {{ __tr('No item found') }}
                                        </p>
                                    </td>
                                </tr>
                            @endif
                            <div class="p-2 row justify-between">
                                {{ $files->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
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
                            <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete ?') }}</h4>
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
            <!--End Delete Modal-->
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";
            //Visible  delete modal
            $('.delete-file').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $('#delete-id').val(id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
