@php
    $links = [
        ['title' => 'Resellers', 'route' => 'admin.resellers.index', 'active' => false],
        ['title' => 'Edit: ' . $reseller->name, 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Edit Reseller
@endsection
@section('page-content')
    <x-admin-page-header title="Edit Reseller" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $reseller->name }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.resellers.update', $reseller->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $reseller->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $reseller->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Company / Business Name</label>
                                    <input type="text" name="company_name"
                                        class="form-control @error('company_name') is-invalid @enderror"
                                        value="{{ old('company_name', $reseller->company_name) }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Markup Percentage (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="markup_percentage"
                                        class="form-control @error('markup_percentage') is-invalid @enderror"
                                        value="{{ old('markup_percentage', $reseller->markup_percentage) }}" min="0"
                                        max="100" step="0.01" required>
                                    @error('markup_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="1"
                                            {{ old('status', $reseller->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0"
                                            {{ old('status', $reseller->status) == 0 ? 'selected' : '' }}>Inactive /
                                            Pending</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route('admin.resellers.index') }}" class="btn btn-default">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Info sidebar --}}
                <div class="col-md-4 col-lg-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current Credits</span>
                            <span class="info-box-number">${{ number_format($reseller->credits, 2) }}</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Clients</span>
                            <span class="info-box-number">{{ $reseller->resellerClients()->count() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.resellers.credit.logs', $reseller->id) }}"
                        class="btn btn-block btn-outline-secondary">
                        View Credit Logs
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
