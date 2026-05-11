@extends('backend.layouts.dashboard_layout')
@section('page-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">API Logs</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">API Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">

        {{-- Filters --}}
        <div class="card card-outline card-info mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.api.logs') }}" class="form-inline flex-wrap">
                    <input type="text" name="user" class="form-control mr-2 mb-2" placeholder="User name/email" value="{{ request('user') }}">
                    <input type="text" name="endpoint" class="form-control mr-2 mb-2" placeholder="Endpoint" value="{{ request('endpoint') }}">
                    <input type="number" name="status" class="form-control mr-2 mb-2" style="width:100px" placeholder="Status" value="{{ request('status') }}">
                    <input type="date" name="from" class="form-control mr-2 mb-2" value="{{ request('from') }}">
                    <input type="date" name="to" class="form-control mr-2 mb-2" value="{{ request('to') }}">
                    <button class="btn btn-info mb-2 mr-2">Filter</button>
                    <a href="{{ route('admin.api.logs') }}" class="btn btn-secondary mb-2">Reset</a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ $logs->total() }} log entries</h3></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Method</th>
                            <th>Endpoint</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>IP</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('M d H:i:s') }}</small></td>
                            <td>
                                @if($log->user)
                                    <small>{{ $log->user->name }}</small>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $log->method === 'GET' ? 'info' : 'warning' }}">{{ $log->method }}</span></td>
                            <td><code style="font-size:11px;">{{ Str::limit($log->endpoint, 60) }}</code></td>
                            <td>
                                <span class="badge badge-{{ $log->status_code >= 200 && $log->status_code < 300 ? 'success' : ($log->status_code >= 400 ? 'danger' : 'secondary') }}">
                                    {{ $log->status_code }}
                                </span>
                            </td>
                            <td><small>{{ $log->duration_ms }}ms</small></td>
                            <td><small>{{ $log->ip_address }}</small></td>
                            <td>
                                <button class="btn btn-xs btn-outline-secondary" data-toggle="modal" data-target="#log-{{ $log->id }}">Details</button>
                                {{-- Detail Modal --}}
                                <div class="modal fade" id="log-{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $log->method }} {{ $log->endpoint }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>Request</h6>
                                                <pre class="bg-light p-2" style="max-height:200px;overflow:auto;font-size:11px;">{{ json_encode($log->request_payload, JSON_PRETTY_PRINT) }}</pre>
                                                <h6>Response</h6>
                                                <pre class="bg-light p-2" style="max-height:200px;overflow:auto;font-size:11px;">{{ json_encode($log->response_payload, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection
