@extends('backend.layouts.dashboard_layout')
@section('title')
    {{ __tr('Member Settings') }}
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
                            <h4>{{ __tr('Member Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('classified.member.settings.update') }}" method="POST">
                                @csrf
                                <div class="align-items-center form-row mb-20">
                                    <div class="col-sm-6">
                                        <label class="font-14 bold black">{{ __tr('Member Auto Approval') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" name="member_auto_approved" @checked(getGeneralSetting('member_auto_approved') == config('settings.general_status.active'))>
                                            <span class="control"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="align-items-center form-row mb-20">
                                    <div class="col-sm-6">
                                        <label class="font-14 bold black">{{ __tr('Member Email Verification') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-1">
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" name="member_email_verification"
                                                @checked(getGeneralSetting('member_email_verification') == config('settings.general_status.active'))>
                                            <span class="control"></span>
                                        </label>
                                    </div>
                                    <div class="col-sm-5">
                                        <p class="mt-0 font-13">
                                            {{ __tr('Enable member email verification you need to complete email configuration and Cron Job setup') }}
                                            <br>
                                            <a href="{{ route('core.email.smtp.configuration') }}"
                                                class="btn-link">{{ __tr('Configure Email') }}
                                            </a>
                                            <br>
                                            <a href="https://document-tlcommerce.themelooks.us/blog/cron-job-setup"
                                                target="_blank" class="btn-link">{{ __tr('How to setup cron job ?') }}
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
