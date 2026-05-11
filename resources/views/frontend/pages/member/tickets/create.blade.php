@extends('frontend.layouts.dashboard')
@section('dashboard-content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('member.tickets.index') }}" class="btn btn-sm btn-default mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h4 class="mb-0">{{ __tr('Open New Ticket') }}</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('member.tickets.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label>{{ __tr('Subject') }} *</label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                            value="{{ old('subject') }}" placeholder="{{ __tr('Briefly describe your issue') }}">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label>{{ __tr('Priority') }} *</label>
                        <select name="priority" class="form-control">
                            @foreach(['low','normal','high','urgent'] as $p)
                                <option value="{{ $p }}" {{ old('priority','normal') === $p ? 'selected' : '' }}>
                                    {{ ucfirst($p) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>{{ __tr('Department') }}</label>
                        <select name="department" class="form-control">
                            <option value="">{{ __tr('General') }}</option>
                            <option value="billing">{{ __tr('Billing') }}</option>
                            <option value="technical">{{ __tr('Technical') }}</option>
                            <option value="sales">{{ __tr('Sales') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __tr('Message') }} *</label>
                    <textarea name="message" rows="8" class="form-control @error('message') is-invalid @enderror"
                        placeholder="{{ __tr('Describe your issue in detail...') }}">{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-1"></i> {{ __tr('Submit Ticket') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
