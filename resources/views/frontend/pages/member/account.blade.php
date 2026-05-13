@extends('frontend.layouts.dashboard')
@section('dash-meta')
    <title>{{ __tr('Account') }} - {{ get_setting('site_name') }}</title>
@endsection

@section('dashboard-content')
    <div class="my-listings-header">
        <h1>{{ __tr('Account Settings') }}</h1>
    </div>

    {{-- ── Profile Photo ── --}}
    <div class="dashboard-card p-0 avatar-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-camera card-title-icon-primary"></i>{{ __tr('Profile Photo') }}
            </h3>
        </div>

        <form class="p-2" action="{{ route('member.account.update.image') }}" method="POST" enctype="multipart/form-data"
            id="avatarForm">
            @csrf

            <div class="avatar-upload-wrap">
                @if ($user->image)
                    <img src="{{ asset(getFilePath($user->image)) }}" alt="{{ $user->name }}" class="avatar-preview"
                        id="avatarPreview">
                @else
                    <div class="avatar-initials" id="avatarInitials">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <img src="" alt="{{ $user->name }}" class="avatar-preview d-none" id="avatarPreview">
                @endif

                <div class="avatar-upload-info">
                    <label for="profileImageInput" class="avatar-file-label">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> {{ __tr('Choose Photo') }}
                    </label>
                    <button type="submit" class="cmn-btn d-none" id="avatarSaveBtn">
                        {{ __tr('Upload') }}
                    </button>
                    <input type="file" name="profile_image" id="profileImageInput" class="avatar-file-input"
                        accept="image/jpeg,image/png,image/jpg,image/webp">
                    <p>{{ __tr('JPG, PNG or WEBP. Max 2 MB.') }}</p>
                    @error('profile_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </form>
    </div>

    <div class="account-grid">

        {{-- ── Profile Information ── --}}
        <div class="dashboard-card p-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-user card-title-icon-primary"></i>{{ __tr('Profile Information') }}
                </h3>
            </div>

            <form class="p-3" action="{{ route('member.account.update.profile') }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group mb-20">
                    <label>{{ __tr('Full Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="input-style @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" placeholder="{{ __tr('Your full name') }}">
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-20">
                    <label>{{ __tr('Email Address') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="input-style @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" placeholder="{{ __tr('your@email.com') }}">
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>


                <div class="form-group mb-0">
                    <button type="submit" class="cmn-btn">
                        {{ __tr('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Change Password ── --}}
        <div class="dashboard-card p-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-lock card-title-icon-primary"></i>{{ __tr('Change Password') }}
                </h3>
            </div>

            @if ($user->social_provider)
                <div class="social-notice p-3">
                    <i class="fa-solid fa-circle-info"></i>
                    {{ __tr('Your account is linked via') }}
                    <strong>{{ ucfirst($user->social_provider) }}</strong>.
                    {{ __tr('Password change is not available for social login accounts.') }}
                </div>
            @else
                <form class="p-3" action="{{ route('member.account.update.password') }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-20">
                        <label>{{ __tr('Current Password') }} <span class="text-danger">*</span></label>
                        <div class="password-field">
                            <input type="password" name="current_password" id="currentPassword"
                                class="input-style @error('current_password') is-invalid @enderror"
                                placeholder="{{ __tr('Enter current password') }}">
                            <button type="button" class="toggle-pw" onclick="togglePw('currentPassword', this)">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-20">
                        <label>{{ __tr('New Password') }} <span class="text-danger">*</span></label>
                        <div class="password-field">
                            <input type="password" name="new_password" id="newPassword"
                                class="input-style @error('new_password') is-invalid @enderror"
                                placeholder="{{ __tr('Min. 8 characters') }}">
                            <button type="button" class="toggle-pw" onclick="togglePw('newPassword', this)">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-20">
                        <label>{{ __tr('Confirm New Password') }} <span class="text-danger">*</span></label>
                        <div class="password-field">
                            <input type="password" name="new_password_confirmation" id="confirmPassword"
                                class="input-style" placeholder="{{ __tr('Repeat new password') }}">
                            <button type="button" class="toggle-pw" onclick="togglePw('confirmPassword', this)">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="cmn-btn">
                            {{ __tr('Update Password') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>

    </div>
@endsection

@section('dashboard-js')
    <script>
        function togglePw(id, btn) {
            const input = document.getElementById(id);
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.querySelector('i').className = isText ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
        }

        document.getElementById('profileImageInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                const initials = document.getElementById('avatarInitials');
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (initials) initials.style.display = 'none';
            };
            reader.readAsDataURL(file);

            document.getElementById('avatarSaveBtn').style.display = 'inline-flex';
        });
    </script>
@endsection
