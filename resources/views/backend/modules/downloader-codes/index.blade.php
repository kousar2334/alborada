@extends('backend.layouts.dashboard_layout')
@section('page-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __tr('App Downloader Codes') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __tr('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __tr('App Codes') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                {{-- ── Add New Code Form ── --}}
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Add New App Code') }}</h3>
                        </div>
                        <form action="{{ route('admin.downloader-codes.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Label') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                                        value="{{ old('label') }}" placeholder="{{ __tr('e.g. XCIPTV Player') }}" required>
                                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                        value="{{ old('code') }}" placeholder="{{ __tr('e.g. 140565') }}" required>
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Device Type') }} <span class="text-danger">*</span></label>
                                    <select name="device_type" class="form-control" required>
                                        <option value="firestick">Amazon Firestick / Fire TV</option>
                                        <option value="android">Android TV / Box</option>
                                        <option value="ios">iPhone / iPad</option>
                                        <option value="smart_tv">Smart TV</option>
                                        <option value="desktop">Windows / Mac</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Description') }}</label>
                                    <textarea name="description" class="form-control" rows="3"
                                        placeholder="{{ __tr('Optional install instructions or notes') }}">{{ old('description') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Sort Order') }}</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active_new" checked>
                                        <label class="custom-control-label" for="is_active_new">{{ __tr('Active') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ __tr('Add Code') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Code List ── --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Existing Codes') }} ({{ $codes->count() }})</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($codes->isEmpty())
                                <div class="text-center p-4 text-muted">
                                    <i class="fas fa-download fa-2x mb-2"></i>
                                    <p>{{ __tr('No app codes added yet. Add one using the form.') }}</p>
                                </div>
                            @else
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __tr('Label') }}</th>
                                            <th>{{ __tr('Code') }}</th>
                                            <th>{{ __tr('Device') }}</th>
                                            <th>{{ __tr('Status') }}</th>
                                            <th>{{ __tr('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($codes as $code)
                                            <tr id="row-{{ $code->id }}">
                                                <td>{{ $code->label }}</td>
                                                <td><code>{{ $code->code }}</code></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ \App\Models\AppDownloaderCode::deviceTypeLabel($code->device_type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $code->is_active ? 'success' : 'secondary' }} toggle-badge" id="badge-{{ $code->id }}">
                                                        {{ $code->is_active ? __tr('Active') : __tr('Inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-xs btn-warning" onclick="openEditModal({{ $code->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-secondary" onclick="toggleStatus({{ $code->id }})">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                    <form action="{{ route('admin.downloader-codes.destroy', $code) }}" method="POST" style="display:inline;"
                                                        onsubmit="return confirm('{{ __tr('Delete this code?') }}')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __tr('Edit App Code') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="editForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __tr('Label') }}</label>
                            <input type="text" name="label" id="edit_label" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Code') }}</label>
                            <input type="text" name="code" id="edit_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Device Type') }}</label>
                            <select name="device_type" id="edit_device_type" class="form-control">
                                <option value="firestick">Amazon Firestick / Fire TV</option>
                                <option value="android">Android TV / Box</option>
                                <option value="ios">iPhone / iPad</option>
                                <option value="smart_tv">Smart TV</option>
                                <option value="desktop">Windows / Mac</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Description') }}</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('Sort Order') }}</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="edit_is_active">
                                <label class="custom-control-label" for="edit_is_active">{{ __tr('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
const codesData = @json($codes->keyBy('id'));
const baseUpdateUrl = "{{ url('admin/downloader-codes') }}";
const toggleUrl     = "{{ url('admin/downloader-codes') }}";

function openEditModal(id) {
    const c = codesData[id];
    document.getElementById('edit_label').value       = c.label;
    document.getElementById('edit_code').value        = c.code;
    document.getElementById('edit_device_type').value = c.device_type;
    document.getElementById('edit_description').value = c.description || '';
    document.getElementById('edit_sort_order').value  = c.sort_order;
    document.getElementById('edit_is_active').checked = !!c.is_active;
    document.getElementById('editForm').action = baseUpdateUrl + '/' + id;
    $('#editModal').modal('show');
}

function toggleStatus(id) {
    $.post(toggleUrl + '/' + id + '/toggle', { _token: '{{ csrf_token() }}' }, function(res) {
        const badge = document.getElementById('badge-' + id);
        if (res.is_active) {
            badge.className = 'badge badge-success toggle-badge';
            badge.textContent = '{{ __tr('Active') }}';
        } else {
            badge.className = 'badge badge-secondary toggle-badge';
            badge.textContent = '{{ __tr('Inactive') }}';
        }
    });
}
</script>
@endsection
