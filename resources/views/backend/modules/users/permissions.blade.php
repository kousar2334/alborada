@php
    $links = [
        ['title' => 'Users', 'route' => 'admin.users.list', 'active' => false],
        ['title' => 'Permissions', 'route' => '', 'active' => true],
    ];
    $grouped = $permissions->groupBy('module')->sortKeys();
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    {{ __tr('Permissions') }}
@endsection
@section('page-style')
    <style>
        .module-card {
            border: 1px solid #e3e6ea;
            border-radius: 6px;
            box-shadow: none;
        }

        .module-card .card-header {
            background: #fff;
            border-bottom: 1px solid #e3e6ea;
            padding: 0.65rem 1rem;
            border-radius: 6px 6px 0 0;
        }

        .module-card .card-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #343a40;
            margin: 0;
        }

        .module-card .card-body {
            padding: 0.75rem 1rem;
        }

        .permission-badge {
            display: inline-block;
            background: #f8f9fa;
            border: 1px solid #e3e6ea;
            border-radius: 3px;
            padding: 5px 12px;
            font-size: 0.775rem;
            color: #495057;
            margin: 4px 5px 4px 0;
            line-height: 1.4;
        }

        .permission-badge.search-highlight {
            background: #fff3cd;
            border-color: #ffc107;
        }

        #permission-search {
            max-width: 280px;
        }

        .perm-count-badge {
            font-size: 0.75rem;
            background: #f1f3f5;
            color: #495057;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1px 8px;
            font-weight: 600;
        }
    </style>
@endsection
@section('page-content')
    <x-admin-page-header title="Permissions" :links="$links" />
    <section class="content">
        <div class="container-fluid">

            {{-- Summary bar --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted" style="font-size:0.85rem;">
                            <strong>{{ $permissions->count() }}</strong> {{ __tr('permissions') }}
                            &nbsp;&middot;&nbsp;
                            <strong>{{ $grouped->count() }}</strong> {{ __tr('modules') }}
                        </div>
                        <input type="text" id="permission-search" class="form-control form-control-sm"
                            placeholder="{{ __tr('Search permissions...') }}">
                    </div>
                </div>
            </div>

            {{-- Grouped permission cards --}}
            <div class="row" id="permission-groups">
                @foreach ($grouped as $module => $perms)
                    <div class="col-md-6 col-xl-4 mb-3 permission-group-col" data-module="{{ strtolower($module) }}">
                        <div class="card module-card h-100 mb-0">
                            <div class="card-header d-flex gap-10 align-items-center">
                                <h3 class="card-title">{{ $module }}</h3>
                                <span class="perm-count-badge">{{ $perms->count() }}</span>
                            </div>
                            <div class="card-body">
                                @foreach ($perms as $perm)
                                    <span class="permission-badge permission-item"
                                        data-name="{{ strtolower($perm->name) }}">
                                        {{ $perm->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No results message --}}
            <div id="no-results" class="text-center text-muted py-4" style="display:none;">
                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                {{ __tr('No permissions found matching your search.') }}
            </div>

        </div>
    </section>
@endsection

@section('page-script')
    <script>
        (function($) {
            "use strict";

            $('#permission-search').on('keyup', function() {
                var query = $(this).val().toLowerCase().trim();
                var anyVisible = false;

                $('.permission-item').removeClass('search-highlight');

                if (query === '') {
                    $('.permission-group-col').show();
                    $('#no-results').hide();
                    return;
                }

                $('.permission-group-col').each(function() {
                    var moduleName = $(this).data('module');
                    var items = $(this).find('.permission-item');
                    var moduleMatch = moduleName.includes(query);
                    var matchCount = 0;

                    items.each(function() {
                        if ($(this).data('name').includes(query) || moduleMatch) {
                            $(this).show().addClass('search-highlight');
                            matchCount++;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (matchCount > 0 || moduleMatch) {
                        $(this).show();
                        anyVisible = true;
                        if (moduleMatch) items.show().addClass('search-highlight');
                    } else {
                        $(this).hide();
                    }
                });

                $('#no-results').toggle(!anyVisible);
            });

        })(jQuery);
    </script>
@endsection
