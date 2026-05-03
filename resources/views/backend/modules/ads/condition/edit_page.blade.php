@php
    $lang = request()->get('lang', defaultLangCode());
    $links = [
        [
            'title' => 'Listing Conditions',
            'route' => 'classified.ads.condition.list',
            'active' => false,
        ],
        [
            'title' => 'Edit Condition',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Edit Condition') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Edit Condition" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Condition Information') }}</h3>
                        </div>
                        <div class="card-body">
                            {{-- Language tabs --}}
                            <div class="lang-switcher-wrap mb-3">
                                <div class="lang-switcher-label">
                                    <i class="fas fa-globe-americas"></i>
                                    <span>{{ __tr('Language') }}</span>
                                </div>
                                <div class="lang-switcher-tabs">
                                    @foreach (activeLanguages() as $language)
                                        <a href="{{ route('classified.ads.condition.edit.page', ['id' => $condition->id, 'lang' => $language->code]) }}"
                                            class="lang-switcher-btn @if ($language->code == $lang) active @endif">
                                            <span class="lang-dot"></span>
                                            {{ $language->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <form id="editForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $condition->id }}">
                                <input type="hidden" name="lang" value="{{ $lang }}">

                                <div class="form-group">
                                    <label class="black font-14">{{ __tr('Title') }}</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ $condition->translation('title', $lang) }}"
                                        placeholder="{{ __tr('Enter title') }}">
                                </div>

                                @if ($lang == defaultLangCode())
                                    <div class="form-group">
                                        <label class="black font-14">{{ __tr('Status') }}</label>
                                        <select name="status" class="form-control">
                                            <option value="{{ config('settings.general_status.active') }}"
                                                @selected($condition->status == config('settings.general_status.active'))>
                                                {{ __tr('Active') }}
                                            </option>
                                            <option value="{{ config('settings.general_status.in_active') }}"
                                                @selected($condition->status == config('settings.general_status.in_active'))>
                                                {{ __tr('Inactive') }}
                                            </option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="status" value="{{ $condition->status }}">
                                @endif

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __tr('Save Changes') }}
                                    </button>
                                    <a href="{{ route('classified.ads.condition.list') }}" class="btn btn-secondary">
                                        {{ __tr('Back') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        (function($) {
            "use strict";

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').remove();
                $(document).find('.form-control').removeClass('is-invalid');
                var formData = new FormData(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: formData,
                    url: '{{ route('classified.ads.condition.update') }}',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Success');
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').addClass(
                                    'is-invalid');
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>'
                                );
                            });
                        } else {
                            toastr.error('{{ __tr('Condition update failed') }}', 'Error');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
