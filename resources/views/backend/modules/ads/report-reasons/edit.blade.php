@php
    $lang = request()->get('lang', defaultLangCode());
    $links = [
        ['title' => 'Report Reasons', 'route' => 'classified.ads.report.reasons.list', 'active' => false],
        ['title' => 'Edit Reason', 'route' => '', 'active' => true],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Edit Report Reason') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Edit Report Reason" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('classified.ads.report.reasons.update') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $reason->id }}">
                <input type="hidden" name="lang" value="{{ $lang }}">

                <div class="row">
                    {{-- Main Content --}}
                    <div class="col-lg-9 col-12">
                        <div class="card">
                            {{-- Language Tabs --}}
                            <div class="lang-switcher-wrap mb-0">
                                <div class="lang-switcher-label">
                                    <i class="fas fa-globe-americas"></i>
                                    <span>{{ __tr('Language') }}</span>
                                </div>
                                <div class="lang-switcher-tabs">
                                    @foreach (activeLanguages() as $language)
                                        <a href="{{ route('classified.ads.report.reasons.edit', ['id' => $reason->id, 'lang' => $language->code]) }}"
                                            class="lang-switcher-btn @if ($language->code == $lang) active @endif">
                                            <span class="lang-dot"></span>
                                            {{ $language->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Title') }}</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ $reason->translation('title', $lang) }}"
                                        placeholder="{{ __tr('Enter reason title') }}">
                                    @error('title')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar: Status + Save --}}
                    <div class="col-lg-3 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="form-group {{ !empty($lang) && $lang != defaultLangCode() ? 'area-disabled' : '' }}">
                                    <label>{{ __tr('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="{{ config('settings.general_status.active') }}"
                                            @selected($reason->status == config('settings.general_status.active'))>
                                            {{ __tr('Active') }}
                                        </option>
                                        <option value="{{ config('settings.general_status.in_active') }}"
                                            @selected($reason->status == config('settings.general_status.in_active'))>
                                            {{ __tr('Inactive') }}
                                        </option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary btn-block">{{ __tr('Save Changes') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
