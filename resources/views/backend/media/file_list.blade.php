<ul class="media-file-list list-unstyled">
    @foreach ($files as $key => $file)
        <li id="list-item-{{ $file->id }}" date-id="{{ $file->id }}"
            class="single-media-item single-media-key-{{ $key }} {{ in_array($file->id, $selected_items) ? 'selected' : '' }}"
            onclick="selectMedia(event,{{ $file->id }})">
            <div class="thumbnail">
                <img src="{{ asset('public/' . $file->path) }}" alt="{{ $file->id }}" width="150"
                    class="preview_image" id="single-media-file" />
            </div>
            <button type="button" class="check" id="check_{{ $file->id }}">
                <i class="fas fa-check"></i>
            </button>
        </li>
    @endforeach
</ul>
