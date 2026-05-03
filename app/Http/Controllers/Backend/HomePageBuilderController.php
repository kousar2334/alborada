<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HomePageSection;
use App\Models\PageContent;
use App\Models\PageContentTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageBuilderController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->lang ?? defaultLangCode();
        $sections = HomePageSection::orderBy('sort_order')->get();

        $links = [
            ['title' => 'Appearances', 'route' => '', 'active' => false],
            ['title' => 'Home Builder', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.home_builder.index', compact('sections', 'lang', 'links'));
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|integer|exists:home_page_sections,id',
            'sections.*.sort_order' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->sections as $item) {
                HomePageSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update order'], 500);
        }
    }

    public function toggleActive(Request $request)
    {
        $request->validate([
            'section_id' => 'required|integer|exists:home_page_sections,id',
        ]);

        try {
            $section = HomePageSection::findOrFail($request->section_id);
            $section->is_active = !$section->is_active;
            $section->save();

            return response()->json([
                'success' => true,
                'is_active' => $section->is_active,
                'message' => $section->is_active ? 'Section enabled' : 'Section disabled',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to toggle section'], 500);
        }
    }

    public function updateContent(Request $request)
    {
        try {
            DB::beginTransaction();

            $lang = $request->input('lang', defaultLangCode());
            $defaultLang = defaultLangCode();
            $pageId = 'home';

            foreach ($request->except(['_token', 'lang', 'section_key']) as $key => $value) {
                if ($lang === $defaultLang) {
                    PageContent::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value, 'page_id' => $pageId]
                    );
                } else {
                    PageContentTranslation::updateOrCreate(
                        ['key' => $key, 'lang' => $lang],
                        ['value' => $value, 'page_id' => $pageId]
                    );
                }
            }

            DB::commit();
            toastNotification('success', 'Section content updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }
}
