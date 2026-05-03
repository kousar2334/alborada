@extends('backend.layouts.dashboard_layout')
@section('title')
    {{ __tr('Ads Settings') }}
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
                            <h4>{{ __tr('Ads Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('classified.settings.update') }}" method="POST">
                                @csrf
                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Display Ads per page') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="ad_per_page"
                                            value="{{ getGeneralSetting('ad_per_page') }}" placeholder="00">
                                    </div>
                                </div>

                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Free Ad Posting Limit') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="free_ad_posting_limit"
                                            value="{{ getGeneralSetting('free_ad_posting_limit') }}" placeholder="00">
                                    </div>
                                </div>

                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Cost Per Ad') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="cost_per_ads"
                                            value="{{ getGeneralSetting('cost_per_ads') }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Cost Per Featured Ad') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="cost_per_featured_ads"
                                            value="{{ getGeneralSetting('cost_per_featured_ads') }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label
                                            class="font-14 bold black">{{ __tr('How Many Days Featured Add Visible on Top ?') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="featured_ad_visible_at_top"
                                            value="{{ getGeneralSetting('featured_ad_visible_at_top') }}" placeholder="00">
                                    </div>
                                </div>
                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label
                                            class="font-14 bold black">{{ __tr('How Many Featured Add Visible on Top ?') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="featured_ad_on_to_counter"
                                            value="{{ getGeneralSetting('featured_ad_on_to_counter') }}" placeholder="00">
                                    </div>
                                </div>

                                <div class="form-row align-items-center mb-20">
                                    <div class="col-sm-4">
                                        <label class="font-14 bold black">{{ __tr('Maximum Gallery Image') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="maximum_gallery_image"
                                            value="{{ getGeneralSetting('maximum_gallery_image') }}" placeholder="00">
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
