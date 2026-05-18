<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MediaContent;
use Illuminate\Http\Request;

class MediaContentController extends Controller
{
    public function index()
    {
        $items = MediaContent::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('backend.modules.media-content.index', compact('items'));
    }

    public function create()
    {
        return view('backend.modules.media-content.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validateItem($request);
        MediaContent::create($data);
        toastNotification('success', __tr('Media content added successfully.'));
        return redirect()->route('admin.media-content.index');
    }

    public function edit(MediaContent $mediaContent)
    {
        return view('backend.modules.media-content.form', ['item' => $mediaContent]);
    }

    public function update(Request $request, MediaContent $mediaContent)
    {
        $data = $this->validateItem($request);

        if (empty($data['thumbnail'])) {
            unset($data['thumbnail']);
        }

        $mediaContent->update($data);
        toastNotification('success', __tr('Media content updated successfully.'));
        return redirect()->route('admin.media-content.index');
    }

    public function destroy(MediaContent $mediaContent)
    {
        $mediaContent->delete();
        toastNotification('success', __tr('Media content deleted.'));
        return redirect()->route('admin.media-content.index');
    }

    private function validateItem(Request $request): array
    {
        $data = $request->validate([
            'title'            => 'required|string|max:200',
            'subtitle'         => 'nullable|string|max:300',
            'description'      => 'nullable|string|max:2000',
            'thumbnail'        => 'nullable|string|max:500',
            'trailer_url'      => 'nullable|string|max:500',
            'type'             => 'required|in:movie,tv_show',
            'genre'            => 'nullable|string|max:200',
            'release_year'     => 'nullable|integer|min:1900|max:2099',
            'seasons'          => 'nullable|integer|min:1',
            'episodes'         => 'nullable|integer|min:1',
            'cast'             => 'nullable|string|max:500',
            'rating'           => 'nullable|numeric|min:0|max:10',
            'badge_text'       => 'nullable|string|max:20',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
            'featured_on_home' => 'nullable|boolean',
        ]);

        $data['is_active']        = $request->boolean('is_active', true);
        $data['featured_on_home'] = $request->boolean('featured_on_home', false);

        return $data;
    }
}
