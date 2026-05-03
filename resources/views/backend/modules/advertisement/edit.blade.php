<form id="edit-ad-form">
    <input type="hidden" name="id" value="{{ $advertisement->id }}">

    <div class="form-row">
        <div class="form-group col-lg-8">
            <label class="black font-14">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ $advertisement->title }}">
        </div>
        <div class="form-group col-lg-4">
            <label class="black font-14">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ $advertisement->sort_order }}"
                min="0">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-lg-6">
            <label class="black font-14">Position <span class="text-danger">*</span></label>
            <select name="position" class="form-control">
                <option value="home_top" {{ $advertisement->position === 'home_top' ? 'selected' : '' }}>Homepage
                    — After Hero Banner</option>
                <option value="listing_top" {{ $advertisement->position === 'listing_top' ? 'selected' : '' }}>
                    Listing Page — Top of Results</option>
                <option value="details_sidebar" {{ $advertisement->position === 'details_sidebar' ? 'selected' : '' }}>
                    Ad Details — Sidebar</option>
                <option value="details_top" {{ $advertisement->position === 'details_top' ? 'selected' : '' }}>
                    Ad Details — Top</option>
            </select>
        </div>
        <div class="form-group col-lg-6">
            <label class="black font-14">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control">
                <option value="1" {{ $advertisement->status == 1 ? 'selected' : '' }}>Active</option>
                <option value="2" {{ $advertisement->status == 2 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-lg-6">
            <label class="black font-14">Start Date</label>
            <input type="date" name="start_date" class="form-control"
                value="{{ $advertisement->start_date ? $advertisement->start_date->format('Y-m-d') : '' }}">
        </div>
        <div class="form-group col-lg-6">
            <label class="black font-14">End Date</label>
            <input type="date" name="end_date" class="form-control"
                value="{{ $advertisement->end_date ? $advertisement->end_date->format('Y-m-d') : '' }}">
        </div>
    </div>

    <div class="form-group">
        <label class="black font-14">Ad Type <span class="text-danger">*</span></label>
        <select name="type" id="edit-type-select" class="form-control">
            <option value="image" {{ $advertisement->type === 'image' ? 'selected' : '' }}>Image Banner</option>
            <option value="html" {{ $advertisement->type === 'html' ? 'selected' : '' }}>HTML / Google AdSense Code
            </option>
        </select>
    </div>

    {{-- Image fields --}}
    <div id="edit-image-fields" class="ad-type-fields {{ $advertisement->type === 'image' ? 'active' : '' }}">
        <div class="form-group">
            <label class="black font-14">Banner Image <span class="text-danger">*</span></label>
            <x-media name="edit_image_path" :value="$advertisement->image_path ?? ''"></x-media>
        </div>
        <div class="form-group">
            <label class="black font-14">Click-through URL</label>
            <input type="url" name="click_url" class="form-control" value="{{ $advertisement->click_url }}"
                placeholder="https://example.com (optional)">
        </div>
    </div>

    {{-- HTML / AdSense fields --}}
    <div id="edit-html-fields" class="ad-type-fields {{ $advertisement->type === 'html' ? 'active' : '' }}">
        <div class="form-group">
            <label class="black font-14">Ad Code (HTML / Google AdSense) <span class="text-danger">*</span></label>
            <textarea name="html_code" class="form-control" rows="8"
                placeholder="Paste your Google AdSense code, ad script, or any HTML here...">{{ $advertisement->html_code }}</textarea>
            <small class="text-muted">Tip: Paste your &lt;script&gt; or &lt;ins&gt; AdSense tag here.</small>
        </div>
    </div>

    <div class="btn-area d-flex justify-content-between">
        <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
    </div>
</form>

<script>
    (function($) {
        "use strict";

        function toggleEditTypeFields(val) {
            $('#edit-image-fields, #edit-html-fields').removeClass('active');
            if (val === 'image') {
                $('#edit-image-fields').addClass('active');
            } else if (val === 'html') {
                $('#edit-html-fields').addClass('active');
            }
        }

        $('#edit-type-select').on('change', function() {
            toggleEditTypeFields($(this).val());
        });
    })(jQuery);
</script>
