<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HomePageSection;
use App\Models\PageContent;
use App\Models\PageContentTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomePageBuilderController extends Controller
{
    private array $defaultSections = [
        'hero'         => 'Hero',
        'about'        => 'About',
        'movies'       => 'Movies',
        'series'       => 'Series',
        'sport_events' => 'Sport Events',
        'categories'   => 'Content Categories',
        'features'     => 'Features',
        'pricing'      => 'Pricing Plans',
        'reviews'      => 'Reviews',
        'portal'       => 'Client Portal Preview',
        'devices'      => 'Compatible Devices',
        'setup'        => 'How to Order',
        'why'          => 'Why Choose Us',
        'channels'     => 'Channel Lineup',
        'faq'          => 'FAQ',
        'reseller'     => 'Reseller Program',
        'cta'          => 'CTA Banner',
        'newsletter'   => 'Newsletter',
    ];

    public function index(Request $request): View
    {
        $lang = $request->get('lang', defaultLangCode());
        $sections = $this->sections();
        $links = [
            ['title' => 'Appearance', 'route' => 'admin.appearance.site.setting', 'active' => false],
            ['title' => 'Home Page Builder', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.home_builder.index', compact('sections', 'lang', 'links'));
    }

    public function updateContent(Request $request): RedirectResponse
    {
        $lang = $request->get('lang', defaultLangCode());

        foreach ($request->except('_token', 'lang', 'section_key') as $key => $value) {
            // Empty inputs arrive as null (ConvertEmptyStringsToNull middleware),
            // but page_contents.value is NOT NULL — store an empty string instead.
            $value = $value ?? '';

            if ($lang === defaultLangCode()) {
                PageContent::updateOrCreate(
                    ['key' => $key],
                    ['page_id' => 'home', 'value' => $value]
                );
            } else {
                PageContentTranslation::updateOrCreate(
                    ['key' => $key, 'lang' => $lang],
                    ['page_id' => 'home', 'value' => $value]
                );
            }
        }

        toastNotification('success', 'Home page content updated successfully', 'Success');

        return to_route('admin.home.builder', ['lang' => $lang])->withInput($request->only('section_key'));
    }

    public function updateOrder(Request $request): JsonResponse
    {
        if (!Schema::hasTable('home_page_sections')) {
            return response()->json(['success' => false], 500);
        }

        foreach ($request->get('sections', []) as $section) {
            HomePageSection::where('id', $section['id'])->update([
                'sort_order' => $section['sort_order'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function toggle(Request $request): JsonResponse
    {
        if (!Schema::hasTable('home_page_sections')) {
            return response()->json(['success' => false], 500);
        }

        $section = HomePageSection::findOrFail($request->section_id);
        $section->is_active = !$section->is_active;
        $section->save();

        return response()->json([
            'success' => true,
            'is_active' => $section->is_active,
            'message' => $section->is_active ? 'Section is now visible' : 'Section is now hidden',
        ]);
    }

    private function sections()
    {
        if (!Schema::hasTable('home_page_sections')) {
            return collect($this->defaultSections)->map(function ($title, $key) {
                return (object) [
                    'id' => $key,
                    'key' => $key,
                    'title' => $title,
                    'sort_order' => 0,
                    'is_active' => true,
                ];
            })->values();
        }

        foreach ($this->defaultSections as $key => $title) {
            HomePageSection::firstOrCreate(
                ['key' => $key],
                ['title' => $title, 'sort_order' => (array_search($key, array_keys($this->defaultSections), true) + 1) * 10]
            );
        }

        return HomePageSection::orderBy('sort_order')->get();
    }
}
