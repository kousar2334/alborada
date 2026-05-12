@extends('backend.layouts.dashboard_layout')
@section('page-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __tr('Chat Widget / AI Customer Service') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __tr('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __tr('Chat Widget') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="callout callout-info">
                        <h5><i class="fas fa-info-circle"></i> {{ __tr('How to set up 24/7 AI Customer Service') }}</h5>
                        <p>{{ __tr('Paste your live chat embed script from any of these providers:') }}</p>
                        <ul>
                            <li><strong>Tidio</strong> — <a href="https://www.tidio.com" target="_blank">tidio.com</a> — {{ __tr('AI + live chat, free tier available') }}</li>
                            <li><strong>Crisp</strong> — <a href="https://crisp.chat" target="_blank">crisp.chat</a> — {{ __tr('Free forever plan with chatbot') }}</li>
                            <li><strong>Tawk.to</strong> — <a href="https://tawk.to" target="_blank">tawk.to</a> — {{ __tr('Completely free live chat') }}</li>
                            <li><strong>Intercom</strong> — {{ __tr('Premium AI support platform') }}</li>
                        </ul>
                        <p>{{ __tr('The script will be injected before </body> on all frontend pages when enabled.') }}</p>
                    </div>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('Chat Widget Settings') }}</h3>
                        </div>
                        <form action="{{ route('admin.settings.chat-widget.update') }}" method="POST">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="chat_widget_enabled" value="1"
                                            class="custom-control-input" id="chat_widget_enabled"
                                            {{ get_setting('chat_widget_enabled') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="chat_widget_enabled">
                                            <strong>{{ __tr('Enable Chat Widget') }}</strong>
                                            <small class="text-muted d-block">{{ __tr('When enabled, the widget script will appear on all public pages.') }}</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __tr('Widget Embed Code') }}</label>
                                    <textarea name="chat_widget_code" rows="12" class="form-control"
                                        style="font-family:monospace;font-size:.8rem;"
                                        placeholder="<!-- Paste your chat widget script here. Example: -->&#10;<script>&#10;  // Tawk.to script&#10;  var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();&#10;  (function(){&#10;    var s1 = document.createElement('script'), s0 = document.getElementsByTagName('script')[0];&#10;    s1.async = true;&#10;    s1.src = 'https://embed.tawk.to/YOUR_ID/default';&#10;    ...&#10;  })();&#10;</script>">{{ get_setting('chat_widget_code') }}</textarea>
                                    <small class="text-muted">
                                        {{ __tr('Paste the full embed script exactly as provided by your chat provider. HTML is allowed.') }}
                                    </small>
                                </div>

                                @if(get_setting('chat_widget_code'))
                                    <div class="callout callout-success">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __tr('A widget script is currently saved. It will display on the frontend when enabled.') }}
                                    </div>
                                @endif

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __tr('Save Settings') }}
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
