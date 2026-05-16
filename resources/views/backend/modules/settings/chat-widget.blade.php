@extends('backend.layouts.settings_layout')

@section('settings-title', __tr('Chat Widget'))
@section('settings-description', __tr('Embed a live chat or AI customer service widget on all public pages.'))

@section('settings-content')
    <div class="card mb-4">
        <div class="card-body">
            <div class="callout callout-info mb-0">
                <h6><i class="fas fa-info-circle mr-1"></i>{{ __tr('How to set up 24/7 AI Customer Service') }}</h6>
                <p class="mb-1">{{ __tr('Paste your live chat embed script from any of these providers:') }}</p>
                <ul class="mb-1">
                    <li><strong>Tidio</strong> — tidio.com — {{ __tr('AI + live chat, free tier available') }}</li>
                    <li><strong>Crisp</strong> — crisp.chat — {{ __tr('Free forever plan with chatbot') }}</li>
                    <li><strong>Tawk.to</strong> — tawk.to — {{ __tr('Completely free live chat') }}</li>
                    <li><strong>Intercom</strong> — {{ __tr('Premium AI support platform') }}</li>
                </ul>
                <p class="mb-0 text-muted">
                    {{ __tr('The script will be injected before </body> on all frontend pages when enabled.') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">{{ __tr('Chat Widget Settings') }}</h6>
        </div>
        <form action="{{ route('admin.settings.chat-widget.update') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="chat_widget_enabled" value="1" class="custom-control-input"
                            id="chat_widget_enabled" {{ get_setting('chat_widget_enabled') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="chat_widget_enabled">
                            <strong>{{ __tr('Enable Chat Widget') }}</strong>
                            <small
                                class="text-muted d-block">{{ __tr('When enabled, the widget script will appear on all public pages.') }}</small>
                        </label>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label>{{ __tr('Widget Embed Code') }}</label>
                    <textarea name="chat_widget_code" rows="12" class="form-control settings-css-editor"
                        placeholder="{{ __tr('Paste your chat widget script here...') }}">{{ get_setting('chat_widget_code') }}</textarea>
                    <small
                        class="text-muted">{{ __tr('Paste the full embed script exactly as provided by your chat provider. HTML is allowed.') }}</small>
                </div>
                @if (get_setting('chat_widget_code'))
                    <div class="callout callout-success mt-3 mb-0">
                        <i class="fas fa-check-circle mr-1"></i>
                        {{ __tr('A widget script is currently saved. It will display on the frontend when enabled.') }}
                    </div>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>{{ __tr('Save Settings') }}
                </button>
            </div>
        </form>
    </div>
@endsection
