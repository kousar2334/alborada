@php
    $links = [
        [
            'title' => 'System',
            'route' => 'admin.system.settings.environment',
            'active' => false,
        ],
        [
            'title' => 'Environment Setup',
            'route' => 'admin.system.settings.environment',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('System') }}
@endsection
@section('page-style')
@endsection
@section('page-content')
    <x-admin-page-header title="System" :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @include('backend.includes.settings_navbar')
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.system.settings.environment.update') }}">
                                @csrf
                                <div class="form-group">
                                    <label>APP NAME</label>
                                    <input type="text" class="form-control" name="APP_NAME" placeholder="Enter Role Name"
                                        value="{{ env('APP_NAME') }}">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-lg-4 col-12">
                                        <label>APP URL</label>
                                        <input type="text" class="form-control" name="APP_URL"
                                            placeholder="Enter Role Name" value="{{ env('APP_URL') }}">
                                    </div>

                                    <div class="form-group col-lg-4 col-12">
                                        <label>APP DEBUG</label>
                                        <select class="form-control" name="APP_DEBUG">
                                            <option @selected(env('APP_DEBUG')) value="true">True</option>
                                            <option @selected(!env('APP_DEBUG')) value="false">False</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-4 col-12">
                                        <label>APP ENVIRONMENT</label>
                                        <select class="form-control" name="APP_ENV">
                                            <option @selected(env('APP_ENV') == 'local') value="local">Local</option>
                                            <option @selected(env('APP_ENV') == 'production') value="production">Production</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-lg-4 col-12">
                                        <label>DB_CONNECTION</label>
                                        <input type="text" class="form-control" name="DB_CONNECTION"
                                            placeholder="Enter Role Name" value="{{ env('DB_CONNECTION') }}" readonly>
                                    </div>

                                    <div class="form-group col-lg-4 col-12">
                                        <label>DB_HOST</label>
                                        <input type="text" class="form-control" name="DB_HOST"
                                            placeholder="Enter Database Host" value="{{ env('DB_HOST') }}">
                                    </div>
                                    <div class="form-group col-lg-4 col-12">
                                        <label>DB_PORT</label>
                                        <input type="text" class="form-control" name="DB_PORT"
                                            placeholder="Enter Database Post" value="{{ env('DB_PORT') }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-lg-4 col-12">
                                        <label>DATABASE NAME</label>
                                        <input type="text" class="form-control" name="DB_DATABASE"
                                            placeholder="Enter Database User Name" value="{{ env('DB_DATABASE') }}">
                                    </div>

                                    <div class="form-group col-lg-4 col-12">
                                        <label>DB USERNAME</label>
                                        <input type="text" class="form-control" name="DB_USERNAME"
                                            placeholder="Enter Database User Name" value="{{ env('DB_USERNAME') }}">
                                    </div>
                                    <div class="form-group col-lg-4 col-12">
                                        <label>DB PASSWORD</label>
                                        <input type="text" class="form-control" name="DB_PASSWORD"
                                            placeholder="Enter Database Password" value="{{ env('DB_PASSWORD') }}">
                                    </div>
                                </div>
                                <div class="form-row justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Change</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
