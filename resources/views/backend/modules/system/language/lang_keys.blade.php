@php
    $links = [
        [
            'title' => 'System',
            'route' => 'admin.system.settings.environment',
            'active' => false,
        ],
        [
            'title' => 'Languages',
            'route' => 'admin.system.settings.language.list',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Languages') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="Languages" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title mt-1">{{ $language->title }}</h3>
                                    <a class="btn btn-success btn-sm float-right text-white"
                                        href="{{ route('admin.system.settings.language.list') }}">
                                        {{ __tr('Language List') }}
                                    </a>

                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap justify-content-end mb-2">
                                        <form
                                            action="{{ route('admin.system.settings.language.translation', $language->id) }}"
                                            method="GET">
                                            <input name="search_key"
                                                value="{{ request()->has('search_key') ? request()->get('search_key') : null }}"
                                                class="form-control" placeholder="Type Search Key & Enter ">
                                        </form>
                                        @if (request()->has('search_key'))
                                            <a class="btn btn-danger mb-auto ml-2"
                                                href="{{ route('admin.system.settings.language.translation', $language->id) }}">{{ __tr('Clear Filter') }}</a>
                                        @endif
                                    </div>
                                    <form class="form-horizontal"
                                        action="{{ route('admin.system.settings.language.translation.update') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $language->id }}">
                                        <div class="table table-bordered table-responsive">
                                            <table class="w-100">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __tr('Key') }}</th>
                                                        <th>{{ __tr('Value') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($lang_keys->count() > 0)
                                                        @foreach ($lang_keys as $key => $translation)
                                                            <tr>
                                                                <td>{{ $key + 1 + ($lang_keys->currentPage() - 1) * $lang_keys->perPage() }}
                                                                </td>
                                                                <td class="key" style="width: 40%">
                                                                    {{ $translation->lang_value }}</td>
                                                                <td>
                                                                    @php
                                                                        $traslate_lang = \App\Models\Translation::where(
                                                                            'lang',
                                                                            $language->code,
                                                                        )
                                                                            ->where('lang_key', $translation->lang_key)
                                                                            ->latest()
                                                                            ->first();
                                                                    @endphp
                                                                    <textarea name="values[{{ $translation->lang_key }}]" class="form-control">{{ $traslate_lang->lang_value ?? '' }}</textarea>

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="3">
                                                                <p class="alert alert-danger text-center">
                                                                    {{ __tr('Nothing found') }}</p>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                            @if ($lang_keys->count() > 0)
                                                <div class="col-12 my-3">
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __tr('Save Changes') }}</button>
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                {{ $lang_keys->onEachSide(1)->appends(request()->input())->links('pagination::bootstrap-5') }}
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
