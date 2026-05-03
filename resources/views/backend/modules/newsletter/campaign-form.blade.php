@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ isset($campaign) ? 'Edit Campaign' : 'New Campaign' }}
@endsection
@section('page-content')
    <x-admin-page-header title="{{ isset($campaign) ? 'Edit Campaign' : 'New Campaign' }}" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ isset($campaign) ? __tr('Edit Campaign') : __tr('Create Campaign') }}</h3>
                </div>
                <div class="card-body">
                    @if (isset($campaign))
                        <form method="POST" action="{{ route('admin.newsletter.campaigns.update', $campaign->id) }}">
                        @else
                            <form method="POST" action="{{ route('admin.newsletter.campaigns.store') }}">
                    @endif
                    @csrf
                    <div class="form-group">
                        <label class="black font-14">{{ __tr('Subject') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                            value="{{ old('subject', $campaign->subject ?? '') }}" placeholder="Email subject line">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="black font-14">{{ __tr('Content') }} <span class="text-danger">*</span></label>
                        <textarea name="content" id="newsletter-content" class="form-control @error('content') is-invalid @enderror"
                            rows="20">{{ old('content', $campaign->content ?? '') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.newsletter.campaigns') }}" class="btn btn-secondary">
                            {{ __tr('Back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($campaign) ? __tr('Update Campaign') : __tr('Save Campaign') }}
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        ClassicEditor.create(document.querySelector('#newsletter-content'), {
            toolbar: {
                items: [
                    'heading', '|', 'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'link', 'blockQuote', '|',
                    'imageUpload', 'insertTable', '|',
                    'undo', 'redo'
                ]
            },
            simpleUpload: {
                uploadUrl: '{{ route('utility.store.editor.image') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        }).catch(err => console.error(err));
    </script>
@endsection
