@extends('backend.layouts.dashboard_layout')
@section('title')
    {{ __tr('Map Settings') }}
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
                            <h4>{{ __tr('Map Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('classified.settings.update') }}" method="POST">
                                @csrf
                                <div class="form-row mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Google Map Api Key') }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="google_map_api_key" class="form-control"
                                            placeholder="{{ __tr('Enter Google Map Api Key') }}"
                                            value="{{ getGeneralSetting('google_map_api_key') }}">
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
