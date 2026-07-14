@php
    $links = [['title' => 'Resellers', 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Resellers
@endsection
@section('page-content')
    <x-admin-page-header title="Reseller Management" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    {{-- Status Tabs --}}
                    <ul class="nav nav-pills mr-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('status') ? 'active' : '' }}"
                                href="{{ route('admin.resellers.index', array_merge(request()->except('status', 'page'), [])) }}">
                                All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === '0' ? 'active bg-warning text-dark' : '' }}"
                                href="{{ route('admin.resellers.index', array_merge(request()->except('status', 'page'), ['status' => '0'])) }}">
                                Pending
                                @if ($pendingCount > 0)
                                    <span class="badge badge-danger">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === '1' ? 'active' : '' }}"
                                href="{{ route('admin.resellers.index', array_merge(request()->except('status', 'page'), ['status' => '1'])) }}">
                                Active
                            </a>
                        </li>
                    </ul>

                    {{-- Search --}}
                    <form class="form-inline" method="GET">
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <input type="text" name="q" class="form-control form-control-sm mr-2"
                            placeholder="Search name/email..." value="{{ request('q') }}">
                        <button class="btn btn-sm btn-default" type="submit">Search</button>
                    </form>

                    <button class="btn btn-success btn-sm text-white ml-2" data-toggle="modal"
                        data-target="#add-reseller-modal">
                        <i class="fas fa-plus mr-1"></i>{{ __tr('Add Reseller') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Company</th>
                                <th>Clients</th>
                                <th>Credits</th>
                                <th>Markup %</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resellers as $reseller)
                                <tr>
                                    <td>{{ $reseller->id }}</td>
                                    <td>{{ $reseller->name }}</td>
                                    <td>{{ $reseller->email }}</td>
                                    <td>{{ $reseller->company_name ?: '—' }}</td>
                                    <td>{{ $reseller->reseller_clients_count }}</td>
                                    <td><strong>${{ number_format($reseller->credits, 2) }}</strong></td>
                                    <td>{{ $reseller->markup_percentage }}%</td>
                                    <td>
                                        @if ($reseller->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Approve / Reject --}}
                                        @if ($reseller->status == 0)
                                            <form action="{{ route('admin.resellers.approve', $reseller->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Approve {{ addslashes($reseller->name) }}?')">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.resellers.reject', $reseller->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Reject {{ addslashes($reseller->name) }}?')">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.resellers.reject', $reseller->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning btn-sm"
                                                    onclick="return confirm('Deactivate {{ addslashes($reseller->name) }}?')">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Top Up --}}
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#top-up-modal"
                                            data-reseller-id="{{ $reseller->id }}"
                                            data-reseller-name="{{ $reseller->name }}">
                                            Top Up
                                        </button>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.resellers.edit', $reseller->id) }}"
                                            class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        {{-- Logs --}}
                                        <a href="{{ route('admin.resellers.credit.logs', $reseller->id) }}"
                                            class="btn btn-sm btn-default">
                                            Logs
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No resellers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($resellers->hasPages())
                    <div class="card-footer">{{ $resellers->withQueryString()->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>

        {{-- Add reseller modal --}}
        <div class="modal fade" id="add-reseller-modal">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('admin.resellers.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __tr('Add New Reseller') }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        placeholder="{{ __tr('Enter name') }}" required>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Email') }} *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        placeholder="{{ __tr('Enter email') }}" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Password') }} *</label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="{{ __tr('Enter password') }}" required>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Confirm Password') }} *</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="{{ __tr('Confirm password') }}" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Company Name') }}</label>
                                    <input type="text" name="company_name" class="form-control"
                                        value="{{ old('company_name') }}" placeholder="{{ __tr('Optional') }}">
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Markup %') }}</label>
                                    <input type="number" name="markup_percentage" class="form-control" min="0"
                                        max="100" step="0.01" value="{{ old('markup_percentage', 0) }}">
                                </div>
                                <div class="form-group col-lg-3">
                                    <label class="black font-14">{{ __tr('Initial Credits ($)') }}</label>
                                    <input type="number" name="credits" class="form-control" min="0" step="0.01"
                                        value="{{ old('credits', 0) }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label class="black font-14">{{ __tr('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{ __tr('Active') }}</option>
                                        <option value="0">{{ __tr('Pending') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __tr('Create Reseller') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Top-up modal --}}
        <div class="modal fade" id="top-up-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form action="{{ route('admin.resellers.top.up') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add Credits</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="reseller_id" id="top-up-reseller-id">
                            <p class="mb-2">Adding credits to: <strong id="top-up-reseller-name"></strong></p>
                            <div class="form-group">
                                <label>Amount ($)</label>
                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01"
                                    placeholder="50.00">
                            </div>
                            <div class="form-group mb-0">
                                <label>Note</label>
                                <input type="text" name="description" class="form-control" placeholder="Admin top-up">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Add Credits</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        @if ($errors->any())
            // Validation failed on reseller creation — reopen the modal so the errors are visible
            $('#add-reseller-modal').modal('show');
        @endif

        $('#top-up-modal').on('show.bs.modal', function(event) {
            var btn = $(event.relatedTarget);
            $(this).find('#top-up-reseller-id').val(btn.data('reseller-id'));
            $(this).find('#top-up-reseller-name').text(btn.data('reseller-name'));
        });
    </script>
@endsection
