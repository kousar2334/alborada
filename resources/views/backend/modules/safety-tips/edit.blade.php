@php
    $lang = request()->get('lang');
    $links = [
        [
            'title' => 'Safety Tips',
            'route' => 'classified.settings.safety.tips.list',
            'active' => false,
        ],
        [
            'title' => 'Edit Safety Tips',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Edit Safety Tips') }}
@endsection
@section('page-content')
    <x-admin-page-header title="Edit Safety Tips" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('classified.settings.safety.tips.update') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="card">
                            <div class="lang-switcher-wrap mb-0">
                                <div class="lang-switcher-label">
                                    <i class="fas fa-globe-americas"></i>
                                    <span>{{ __tr('Language') }}</span>
                                </div>
                                <div class="lang-switcher-tabs">
                                    @foreach (getAllLanguages() as $key => $language)
                                        <a href="{{ route('classified.settings.safety.tips.edit', ['id' => $tips->id, 'lang' => $language->code]) }}"
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
                                    <input type="hidden" name="id" value="{{ $tips->id }}">
                                    <input type="hidden" name="lang" value="{{ $lang }}">
                                    <input type="text" name="title" class="form-control"
                                        value="{{ $tips->translation('title', $lang) }}"
                                        placeholder="{{ __tr('Enter title') }}">
                                    @if ($errors->has('title'))
                                        <div class="error text-danger mb-0 invalid-input">
                                            {{ $errors->first('title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="form-group {{ !empty($lang) && $lang != defaultLangCode() ? 'area-disabled' : '' }}">
                                    <label>{{ __tr('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="{{ config('settings.general_status.active') }}"
                                            @selected($tips->status == config('settings.general_status.active'))>
                                            {{ __tr('Active') }}
                                        </option>
                                        <option value="{{ config('settings.general_status.in_active') }}"
                                            @selected($tips->status == config('settings.general_status.in_active'))>
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
