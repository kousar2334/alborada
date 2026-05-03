@extends('backend.layouts.dashboard_layout')
@section('title')
    {{ __tr('General Settings') }}
@endsection
@section('page-content')
    <div class="theme-option-container">
        @include('backend.modules.settings.includes.head')
        <div class="theme-option-tab-wrap">
            @include('backend.modules.settings.includes.tabs')
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-header bg-white border-bottom2 py-3">
                            <h4>{{ __tr('General Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('classified.settings.update') }}" method="POST">
                                @csrf
                                @php
                                    $all_pages = \Core\Models\TlPage::where(
                                        'publish_status',
                                        config('settings.general_status.active'),
                                    )
                                        ->select('id', 'title')
                                        ->get();
                                @endphp
                                <div class="align-items-center form-row mb-20">
                                    <div class="col-sm-4">
                                        <label
                                            class="font-14 bold black">{{ __tr('Member Registration Term & Condition Page') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="member_term_condition_page">
                                            <option value="">{{ __tr('Select a page') }}</option>
                                            @foreach ($all_pages as $page)
                                                <option value="{{ $page->id }}" @selected(getGeneralSetting('member_term_condition_page') == $page->id)>
                                                    {{ $page->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mt-0 font-13">
                                            {{ __tr('To create new page or manage existing pages from') }}
                                            <a href="{{ route('core.page') }}" class="btn-link">{{ __tr('Pages Module') }}
                                            </a>
                                        </p>
                                    </div>
                                </div>

                                <div class="align-items-center form-row mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Ad Posting Term & Condition Page') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="ad_posting_term_condition_page">
                                            <option value="">{{ __tr('Select a page') }}</option>
                                            @foreach ($all_pages as $page)
                                                <option value="{{ $page->id }}" @selected(getGeneralSetting('ad_posting_term_condition_page') == $page->id)>
                                                    {{ $page->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mt-0 font-13">
                                            {{ __tr('To create new page or manage existing pages from') }}
                                            <a href="{{ route('core.page') }}" class="btn-link">{{ __tr('Pages Module') }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn long">{{ __tr('Save Changes') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
