<form id="update-role-form">
    @csrf
    <input type="hidden" name="id" value="{{ $role->id }}">

    <div class="form-group mb-3">
        <span class="field-label">{{ __tr('Name') }}</span>
        <input type="text" class="form-control" name="name" maxlength="255" id="edit-name-input"
            placeholder="{{ __tr('e.g. Store Manager, Content Editor') }}" value="{{ $role->name }}">
        <div class="char-count">
            <span id="edit-name-count">{{ strlen($role->name) }}</span>/255
        </div>
    </div>

    <div class="form-group mb-3">
        <span class="field-label">{{ __tr('Description') }}</span>
        <input type="text" class="form-control" name="description" maxlength="255" id="edit-desc-input"
            placeholder="{{ __tr('Brief description of this role\'s responsibilities') }}"
            value="{{ $role->description ?? '' }}">
        <div class="char-count">
            <span id="edit-desc-count">{{ strlen($role->description ?? '') }}</span>/255
        </div>
    </div>

    <div class="mb-0">
        <div class="permissions-section-title">{{ __tr('Permissions') }}</div>
        <div class="permissions-section-subtitle">
            {{ __tr('Select permissions for this role, grouped by module.') }}
        </div>

        <div class="perm-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" class="perm-search-input" placeholder="{{ __tr('Search permissions or modules') }}"
                data-context="edit">
        </div>

        <div class="perm-select-all-row">
            <label class="perm-select-all-label">
                <input type="checkbox" class="edit-select-all-global">
                {{ __tr('Select all permissions') }}
            </label>
            <button type="button" class="expand-all-btn" data-context="edit"
                data-expanded="1">{{ __tr('Collapse all') }}</button>
        </div>

        <div id="edit-perm-modules">
            @foreach ($permissions as $module => $permission_list)
                @php
                    $selectedCount = $role->permissions->whereIn('name', $permission_list->pluck('name'))->count();
                    $allSelected = $selectedCount === $permission_list->count();
                @endphp
                <div class="perm-module" data-module-name="{{ strtolower($module) }}">
                    <div class="perm-module-header" data-toggle="collapse"
                        data-target="#edit-mod-{{ Str::slug($module) }}">
                        <i class="fas fa-chevron-down perm-module-chevron"></i>
                        <input type="checkbox" class="perm-module-check module-select-all"
                            data-module="{{ Str::slug($module) }}" id="e-all-{{ Str::slug($module) }}"
                            onclick="event.stopPropagation();" @checked($allSelected)>
                        <span class="perm-module-name">{{ $module }}</span>
                        <span class="perm-module-count" data-slug="edit-{{ Str::slug($module) }}"
                            data-total="{{ $permission_list->count() }}">
                            {{ $selectedCount }}/{{ $permission_list->count() }}
                        </span>
                    </div>
                    <div class="collapse show" id="edit-mod-{{ Str::slug($module) }}">
                        <div class="perm-module-body">
                            @foreach ($permission_list as $permission)
                                <div class="perm-item" data-perm-name="{{ strtolower($permission->name) }}">
                                    <input type="checkbox" class="module-permission-{{ Str::slug($module) }}"
                                        id="edit-perm-{{ $permission->id }}" name="permission[]"
                                        value="{{ $permission->name }}" @checked($role->permissions->contains($permission))>
                                    <label for="edit-perm-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</form>
