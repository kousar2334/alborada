<ol class="dd-list">
    @foreach ($children as $key => $subitem)
        <li class="dd-item dd3-item" data-id="{{ $subitem->id }}">
            <div class="dd-handle dd3-handle">
                <i class="fas fa-arrows-alt"></i>
            </div>
            <div class="dd3-content">
                <!--Menu Item Header-->
                <div class="align-items-center d-flex justify-content-between dd-item-header">
                    <div class="menu-item-title">
                        <h6 class="mb-0">
                            {{ $subitem->translation('title', $selected_language) }}
                        </h6>
                    </div>
                    <div class="menu-item-actions">
                        <a href="#" class="btn menu-item-content-toggle-btn">
                            <i class="fas fa-angle-down"></i>
                        </a>
                    </div>
                </div>
                <!--End Menu Item Header--->

                <!--Menu Item Content-->
                <div class="border bg-white menu-item-content-body p-2">
                    <div class="menu-item-content">
                        <div class="form-group">
                            <label>{{ __tr('Text') }}</label>
                            <input type="text" id="text-{{ $subitem->id }}" class="form-control" name="text"
                                value="{{ $subitem->translation('title', $selected_language) }}"
                                placeholder="{{ __tr('Enter Text') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __tr('URL') }}</label>
                            <input type="text" id="link-{{ $subitem->id }}" class="form-control" name="link"
                                value="{{ $subitem->link }}" {{ $subitem->linkable_type != null ? 'disabled' : '' }}
                                placeholder="{{ __tr('https://') }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between menu-item-update-actions">
                        <button class="btn btn-outline-danger btn-sm menu-item-remove-btn"
                            data-id="{{ $subitem->id }}">
                            {{ __tr('Remove Item') }}
                        </button>
                        <button class="btn btn-outline-primary btn-sm menu-item-update-btn"
                            data-id="{{ $subitem->id }}">
                            {{ __tr('Save Change') }}
                        </button>

                    </div>
                </div>
                <!--End Menu Item Content-->
            </div>
            <!--Child Items-->
            @if ($subitem->children->count() > 0)
                @include('backend.modules.appearances.menus.includes.sub_menu', [
                    'children' => $subitem->children,
                ])
            @endif
            <!--End Child Items-->
        </li>
    @endforeach
</ol>
