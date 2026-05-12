<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FeaturedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeaturedContentController extends Controller
{
    public function index()
    {
        $items = FeaturedContent::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('backend.modules.featured-content.index', compact('items'));
    }

    public function create()
    {
        return view('backend.modules.featured-content.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('featured', 'public');
            $data['thumbnail'] = 'storage/' . $path;
        }

        FeaturedContent::create($data);
        toastNotification('success', __tr('Featured content added successfully.'));
        return redirect()->route('admin.featured-content.index');
    }

    public function edit(FeaturedContent $featuredContent)
    {
        return view('backend.modules.featured-content.form', ['item' => $featuredContent]);
    }

    public function update(Request $request, FeaturedContent $featuredContent)
    {
        $data = $this->validate($request);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('featured', 'public');
            $data['thumbnail'] = 'storage/' . $path;
        } else {
            unset($data['thumbnail']);
        }

        $featuredContent->update($data);
        toastNotification('success', __tr('Featured content updated successfully.'));
        return redirect()->route('admin.featured-content.index');
    }

    public function destroy(FeaturedContent $featuredContent)
    {
        $featuredContent->delete();
        toastNotification('success', __tr('Featured content deleted.'));
        return redirect()->route('admin.featured-content.index');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'thumbnail'   => 'nullable|image|max:2048',
            'trailer_url' => 'nullable|string|max:500',
            'type'        => 'required|in:movie,series,sports_event,new_release',
            'genre'       => 'nullable|string|max:100',
            'event_date'  => 'nullable|date',
            'badge_text'  => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}
