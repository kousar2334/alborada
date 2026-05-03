@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Newsletter Subscribers
@endsection
@section('page-content')
    <x-admin-page-header title="Newsletter Subscribers" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Stats --}}
            <div class="row mb-3">
                <div class="col-sm-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Subscribers</span>
                            <span class="info-box-number">{{ number_format($stats['total']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active</span>
                            <span class="info-box-number">{{ number_format($stats['active']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Unsubscribed</span>
                            <span class="info-box-number">{{ number_format($stats['unsubscribed']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __tr('All Subscribers') }}</h3>
                    <div class="card-tools">
                        <form class="form-inline" method="GET">
                            <input type="text" name="search" class="form-control form-control-sm mr-2"
                                placeholder="{{ __tr('Search email / name...') }}" value="{{ request('search') }}">
                            <select name="status" class="form-control form-control-sm mr-2">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ request('status') == 2 ? 'selected' : '' }}>Unsubscribed</option>
                            </select>
                            <button class="btn btn-sm btn-primary">{{ __tr('Filter') }}</button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __tr('Email') }}</th>
                                <th>{{ __tr('Name') }}</th>
                                <th>{{ __tr('Status') }}</th>
                                <th>{{ __tr('Subscribed At') }}</th>
                                <th class="text-right">{{ __tr('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subscribers as $key => $sub)
                                <tr>
                                    <td>{{ $subscribers->firstItem() + $key }}</td>
                                    <td>{{ $sub->email }}</td>
                                    <td>{{ $sub->name ?? '—' }}</td>
                                    <td>
                                        @if ($sub->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Unsubscribed</span>
                                        @endif
                                    </td>
                                    <td>{{ $sub->created_at->format('d M Y') }}</td>
                                    <td class="text-right">
                                        <button class="btn btn-sm btn-danger delete-sub-btn" data-id="{{ $sub->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __tr('No subscribers found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($subscribers->hasPages())
                        <div class="p-3">
                            {{ $subscribers->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div class="modal fade" id="delete-sub-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete?') }}</h4>
                        <form method="POST" action="{{ route('admin.newsletter.subscribers.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-sub-id" name="id">
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
        $(document).on('click', '.delete-sub-btn', function() {
            $('#delete-sub-id').val($(this).data('id'));
            $('#delete-sub-modal').modal('show');
        });
    </script>
@endsection
