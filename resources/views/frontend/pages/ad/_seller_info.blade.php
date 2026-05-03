@php
    $phonePrefix = $isMobile ? 'ForResponsive' : '';
    $user = $ad->userInfo;
    $whatsappPhone = $ad->contact_phone;
    $whatsappClean = $whatsappPhone ? preg_replace('/[^0-9]/', '', $whatsappPhone) : null;
@endphp

{{-- ─── Seller Card ──────────────────────────────────────────── --}}
@if ($user)
    <div class="list-info-card">
        <div class="sid-seller-top">
            <div class="seller-avatar-wrap">
                <img src="{{ asset(getFilePath($user->image)) }}" alt="{{ $user->name }}" class="seller-avatar" />
                <span class="seller-member-label">{{ __tr('Member') }}</span>
            </div>
            <div class="sid-seller-info">
                <div class="sid-seller-name">
                    {{ $user->name }}
                    @if ($user->email_verified_at)
                        <span class="sid-verified-badge">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.7775 4.37L9.0975 3.58C8.9675 3.43 8.8625 3.15 8.8625 2.95V2.1C8.8625 1.57 8.4275 1.135 7.8975 1.135H7.0475C6.8525 1.135 6.5675 1.03 6.4175 0.899999L5.6275 0.219999C5.2825 -0.0750012 4.7175 -0.0750012 4.3675 0.219999L3.5825 0.904999C3.4325 1.03 3.1475 1.135 2.9525 1.135H2.0875C1.5575 1.135 1.1225 1.57 1.1225 2.1V2.955C1.1225 3.15 1.0175 3.43 0.8925 3.58L0.2175 4.375C-0.0725 4.72 -0.0725 5.28 0.2175 5.625L0.8925 6.42C1.0175 6.57 1.1225 6.85 1.1225 7.045V7.9C1.1225 8.43 1.5575 8.865 2.0875 8.865H2.9525C3.1475 8.865 3.4325 8.97 3.5825 9.1L4.3725 9.78C4.7175 10.075 5.2825 10.075 5.6325 9.78L6.4225 9.1C6.5725 8.97 6.8525 8.865 7.0525 8.865H7.9025C8.4325 8.865 8.8675 8.43 8.8675 7.9V7.05C8.8675 6.855 8.9725 6.57 9.1025 6.42L9.7825 5.63C10.0725 5.285 10.0725 4.715 9.7775 4.37ZM7.0775 4.055L4.6625 6.47C4.5925 6.54 4.4975 6.58 4.3975 6.58C4.2975 6.58 4.2025 6.54 4.1325 6.47L2.9225 5.26C2.7775 5.115 2.7775 4.875 2.9225 4.73C3.0675 4.585 3.3075 4.585 3.4525 4.73L4.3975 5.675L6.5475 3.525C6.6925 3.38 6.9325 3.38 7.0775 3.525C7.2225 3.67 7.2225 3.91 7.0775 4.055Z"
                                    fill="white" />
                            </svg>
                            {{ __tr('VERIFIED') }}
                        </span>
                    @endif
                </div>
                <div class="sid-seller-meta">
                    <span>{{ $user->ads_count ?? 0 }} {{ __tr('ads') }}</span>
                    <span class="sid-dot"></span>
                    <span>{{ __tr('Member since') }} {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ─── Contact Card ─────────────────────────────────────────── --}}
@if ($ad->contact_phone || ($user && !($user->id === auth()->id())))
    <div class="list-info-card p-0">

        {{-- Phone Row --}}
        @if ($ad->contact_phone)
            @if ($ad->contact_is_hide == config('settings.general_status.active'))
                <a href="#" class="sid-action-row sid-phone-trigger{{ $phonePrefix }}"
                    data-phone="{{ $ad->contact_phone }}" data-prefix="{{ $phonePrefix }}">
                    <div class="sid-icon-wrap sid-icon-phone">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"
                                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="sid-action-text">
                        <span class="sid-phone-display{{ $phonePrefix }}">{{ substr($ad->contact_phone, 0, 4) }}
                            XXXXXXX</span>
                        <small
                            class="sid-phone-hint{{ $phonePrefix }}">{{ __tr('Click to see phone number') }}</small>
                    </div>
                    <div class="sid-action-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
            @else
                <a href="tel:{{ $ad->contact_phone }}" class="sid-action-row">
                    <div class="sid-icon-wrap sid-icon-phone">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"
                                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="sid-action-text">
                        <span>{{ $ad->contact_phone }}</span>
                        <small>{{ __tr('Click to call') }}</small>
                    </div>
                    <div class="sid-action-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
            @endif
        @endif

        {{-- Chat Row --}}
        @if ($user && !($user->id === auth()->id()))
            @if ($ad->contact_phone)
                <div class="sid-divider"></div>
            @endif

            @auth
                <button type="button" class="sid-action-row" data-bs-toggle="modal" data-bs-target="#seller-message-modal">
                    <div class="sid-icon-wrap sid-icon-chat">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 15c0 .53-.21 1.04-.59 1.41-.37.38-.88.59-1.41.59H7l-4 4V5c0-.53.21-1.04.59-1.41C3.96 3.21 4.47 3 5 3h14c.53 0 1.04.21 1.41.59.38.37.59.88.59 1.41v10z"
                                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="sid-action-text">
                        <span>{{ __tr('Chat') }}</span>
                        <small>{{ __tr('Send a message') }}</small>
                    </div>
                    <div class="sid-action-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </button>
            @else
                <a href="{{ route('member.login') }}?redirect={{ urlencode(request()->url()) }}" class="sid-action-row">
                    <div class="sid-icon-wrap sid-icon-chat">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 15c0 .53-.21 1.04-.59 1.41-.37.38-.88.59-1.41.59H7l-4 4V5c0-.53.21-1.04.59-1.41C3.96 3.21 4.47 3 5 3h14c.53 0 1.04.21 1.41.59.38.37.59.88.59 1.41v10z"
                                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="sid-action-text">
                        <span>{{ __tr('Chat') }}</span>
                        <small>{{ __tr('Login to send a message') }}</small>
                    </div>
                    <div class="sid-action-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
            @endauth

            {{-- WhatsApp Row --}}
            @if ($whatsappClean)
                <div class="sid-divider"></div>
                <a href="https://wa.me/{{ $whatsappClean }}" target="_blank" rel="noopener" class="sid-action-row">
                    <div class="sid-icon-wrap sid-icon-whatsapp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                    </div>
                    <div class="sid-action-text">
                        <span>{{ __tr('WhatsApp') }}</span>
                        <small>{{ __tr('Chat on WhatsApp') }}</small>
                    </div>
                    <div class="sid-action-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
            @endif
        @endif
    </div>
@endif
