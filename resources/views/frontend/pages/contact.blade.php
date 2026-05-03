@extends('frontend.layouts.master')
@section('meta')
    <title>{{ __tr('Contact Us') }} - {{ get_setting('site_name') }}</title>
    <meta name="description" content="{{ __tr('Get in touch with us. Send us a message and we will get back to you.') }}">
@endsection

@section('content')

    {{-- ══════════════════ HERO BANNER ══════════════════ --}}
    <section class="contact-breadcrumb-area">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <span class="page-tag">{{ __tr('Get In Touch') }}</span>
                <h1>{{ $content->get('contact_title') ?: __tr('Contact Us') }}</h1>
                <p class="sub-text">
                    {{ $content->get('contact_sub_title') ?: __tr('We would love to hear from you. Send us a message and we will get back to you shortly.') }}
                </p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">{{ __tr('Home') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __tr('Contact Us') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    {{-- ══════════════════ END HERO ══════════════════════ --}}


    {{-- ══════════════════ CONTACT BODY ═════════════════ --}}
    <section class="contact-page-section">
        <div class="container">

            {{-- Section heading --}}
            <div class="contact-section-head text-center">
                <span class="section-tag">{{ __tr('Contact') }}</span>
                <h2>{{ __tr('How Can We Help You?') }}</h2>
                <p>{{ __tr('Choose the best way to reach us or just drop a message below.') }}</p>
            </div>

            <div class="row g-4 align-items-start">

                {{-- ── LEFT: Info Cards ──────────────────────── --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3">

                        @if ($content->get('contact_address'))
                            <div class="contact-info-card">
                                <div class="fe-icon-box fe-icon-box-primary">
                                    <i class="las la-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="card-label">{{ __tr('Address') }}</p>
                                    <p class="card-value">{{ $content->get('contact_address') }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($content->get('contact_email'))
                            <div class="contact-info-card">
                                <div class="fe-icon-box fe-icon-box-secondary">
                                    <i class="las la-envelope"></i>
                                </div>
                                <div>
                                    <p class="card-label">{{ __tr('Email') }}</p>
                                    <a href="mailto:{{ $content->get('contact_email') }}"
                                        class="card-value">{{ $content->get('contact_email') }}</a>
                                </div>
                            </div>
                        @endif

                        @if ($content->get('contact_phone_1'))
                            <div class="contact-info-card">
                                <div class="fe-icon-box fe-icon-box-warning">
                                    <i class="las la-phone-alt"></i>
                                </div>
                                <div>
                                    <p class="card-label">{{ __tr('Phone') }}</p>
                                    <a href="tel:{{ $content->get('contact_phone_1') }}"
                                        class="card-value d-block">{{ $content->get('contact_phone_1') }}</a>
                                    @if ($content->get('contact_phone_2'))
                                        <a href="tel:{{ $content->get('contact_phone_2') }}"
                                            class="card-value d-block">{{ $content->get('contact_phone_2') }}</a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($content->get('contact_opening_hours'))
                            <div class="contact-info-card">
                                <div class="fe-icon-box fe-icon-box-info">
                                    <i class="las la-clock"></i>
                                </div>
                                <div>
                                    <p class="card-label">{{ __tr('Working Hours') }}</p>
                                    <p class="card-value">{{ $content->get('contact_opening_hours') }}</p>
                                    @if ($content->get('contact_closed_hours'))
                                        <p class="card-value">{{ $content->get('contact_closed_hours') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Fallback when no info configured yet --}}
                        @if (!$content->get('contact_address') && !$content->get('contact_email') && !$content->get('contact_phone_1'))
                            <div class="contact-info-card">
                                <div class="fe-icon-box fe-icon-box-success">
                                    <i class="las la-headset"></i>
                                </div>
                                <div>
                                    <p class="card-label">{{ __tr('Support') }}</p>
                                    <p class="card-value">{{ get_setting('site_name') }}</p>
                                    <p class="card-value" style="font-size:13px; color:#94a3b8; margin-top:4px;">
                                        {{ __tr('Fill the form and we will reach out to you.') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
                {{-- ── END Left ──────────────────────────────── --}}


                {{-- ── RIGHT: Contact Form ──────────────────── --}}
                <div class="col-lg-8">
                    <div class="contact-form-card">

                        <div class="form-head">
                            <h4>{{ __tr('Send Us a Message') }}</h4>
                            <p>{{ __tr('Fill in the details below and our team will respond as soon as possible.') }}
                            </p>
                        </div>

                        {{-- Success alert --}}
                        @if (session('success'))
                            <div class="contact-alert contact-alert-success" role="alert">
                                <i class="las la-check-circle"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        {{-- Validation errors --}}
                        @if ($errors->any())
                            <div class="contact-alert contact-alert-danger" role="alert">
                                <i class="las la-exclamation-circle"></i>
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST">
                            @csrf

                            <div class="row g-3">

                                {{-- Name --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="fe-label">
                                            {{ __tr('Full Name') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="input-style {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            placeholder="{{ __tr('Enter your full name') }}" required>
                                        @error('name')
                                            <span class="fe-invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="fe-label">
                                            {{ __tr('Email Address') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="input-style {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                            placeholder="{{ __tr('Enter your email') }}" required>
                                        @error('email')
                                            <span class="fe-invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Subject --}}
                                <div class="col-12">
                                    <div class="form-group mb-20">
                                        <label class="fe-label">
                                            {{ __tr('Subject') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="subject" value="{{ old('subject') }}"
                                            class="input-style {{ $errors->has('subject') ? 'is-invalid' : '' }}"
                                            placeholder="{{ __tr('What is this about?') }}" required>
                                        @error('subject')
                                            <span class="fe-invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Message --}}
                                <div class="col-12">
                                    <div class="form-group mb-20">
                                        <label class="fe-label">
                                            {{ __tr('Message') }}
                                            <span class="required">*</span>
                                        </label>
                                        <textarea name="message" rows="6" class="input-style {{ $errors->has('message') ? 'is-invalid' : '' }}"
                                            placeholder="{{ __tr('Write your message here...') }}" required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <span class="fe-invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="col-12">
                                    <button type="submit" class="cmn-btn">
                                        <i class="las la-paper-plane"></i>
                                        {{ __tr('Send Message') }}
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
                {{-- ── END Right ─────────────────────────────── --}}

            </div>
        </div>
    </section>
    {{-- ══════════════════ END CONTACT BODY ═════════════ --}}

@endsection
