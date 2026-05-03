@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Newsletter Campaigns
@endsection
@section('page-content')
    <x-admin-page-header title="Newsletter Campaigns" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __tr('Campaigns') }}</h3>
                    <div class="card-tools d-flex align-items-center">
                        <form class="form-inline mr-2" method="GET">
                            <input type="text" name="search" class="form-control form-control-sm mr-2"
                                placeholder="{{ __tr('Search subject...') }}" value="{{ request('search') }}">
                            <select name="status" class="form-control form-control-sm mr-2">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending
                                </option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            </select>
                            <button class="btn btn-sm btn-primary">{{ __tr('Filter') }}</button>
                        </form>
                        <a href="{{ route('admin.newsletter.campaigns.create') }}"
                            class="btn btn-success btn-sm text-white">
                            {{ __tr('New Campaign') }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __tr('Subject') }}</th>
                                <th>{{ __tr('Status') }}</th>
                                <th class="text-center">{{ __tr('Sent') }}</th>
                                <th class="text-center">{{ __tr('Opened') }}</th>
                                <th class="text-center">{{ __tr('Clicked') }}</th>
                                <th>{{ __tr('Sent At') }}</th>
                                <th class="text-right">{{ __tr('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($campaigns as $key => $campaign)
                                <tr>
                                    <td>{{ $campaigns->firstItem() + $key }}</td>
                                    <td>{{ $campaign->subject }}</td>
                                    <td>
                                        @if ($campaign->status === 'sent')
                                            <span class="badge badge-success">Sent</span>
                                        @elseif ($campaign->status === 'sending')
                                            <span class="badge badge-warning">Sending</span>
                                        @else
                                            <span class="badge badge-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ number_format($campaign->total_sent) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-primary">{{ number_format($campaign->total_opened) }}</span>
                                        @if ($campaign->total_sent > 0)
                                            <small class="text-muted">({{ $campaign->open_rate }}%)</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-success">{{ number_format($campaign->total_clicked) }}</span>
                                        @if ($campaign->total_sent > 0)
                                            <small class="text-muted">({{ $campaign->click_rate }}%)</small>
                                        @endif
                                    </td>
                                    <td>{{ $campaign->sent_at ? $campaign->sent_at->format('d M Y H:i') : '—' }}</td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-default btn-sm">{{ __tr('Action') }}</button>
                                            <button type="button"
                                                class="btn btn-default btn-sm dropdown-toggle dropdown-hover dropdown-icon"
                                                data-toggle="dropdown"></button>
                                            <div class="dropdown-menu" role="menu">
                                                @if ($campaign->status === 'sent')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.newsletter.campaigns.stats', $campaign->id) }}">
                                                        {{ __tr('View Stats') }}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                @endif
                                                @if ($campaign->status !== 'sent')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.newsletter.campaigns.edit', $campaign->id) }}">
                                                        {{ __tr('Edit') }}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <button class="dropdown-item send-campaign-btn"
                                                        data-id="{{ $campaign->id }}"
                                                        data-subject="{{ $campaign->subject }}">
                                                        {{ __tr('Send Now') }}
                                                    </button>
                                                    <div class="dropdown-divider"></div>
                                                @endif
                                                <button class="dropdown-item delete-campaign-btn"
                                                    data-id="{{ $campaign->id }}">
                                                    {{ __tr('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __tr('No campaigns found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($campaigns->hasPages())
                        <div class="p-3">
                            {{ $campaigns->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Send Confirm Modal --}}
        <div class="modal fade" id="send-campaign-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">Confirm Send</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mb-1">Send campaign to <strong>all active subscribers</strong>?</p>
                        <p class="send-campaign-subject text-muted small mb-3"></p>
                        <form id="send-campaign-form" method="POST" action="">
                            @csrf
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div class="modal fade" id="delete-campaign-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete?') }}</h4>
                        <form method="POST" action="{{ route('admin.newsletter.campaigns.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-campaign-id" name="id">
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
        $(document).on('click', '.send-campaign-btn', function() {
            var id = $(this).data('id');
            var subject = $(this).data('subject');
            $('.send-campaign-subject').text('"' + subject + '"');
            $('#send-campaign-form').attr('action', '/admin/newsletter/campaigns/' + id + '/send');
            $('#send-campaign-modal').modal('show');
        });

        $(document).on('click', '.delete-campaign-btn', function() {
            $('#delete-campaign-id').val($(this).data('id'));
            $('#delete-campaign-modal').modal('show');
        });
    </script>
@endsection
