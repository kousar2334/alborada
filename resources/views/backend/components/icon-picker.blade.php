<div>
    <select name="{{ $name }}" class="form-control">
        @foreach ($icons['icons'] as $icon)
            <option value="{{ $icon }}" @selected($value == $icon)>{{ $icon }}</option>
        @endforeach
    </select>
</div>
