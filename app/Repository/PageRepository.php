<?php

namespace App\Repository;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Support\Facades\DB;

class PageRepository
{
    /**
     * Will store new page
     * 
     * @param Object $request
     * @return bool
     */
    public function storeNewPage($request, $author)
    {
        try {
            DB::beginTransaction();
            $new_page = new Page();
            $new_page->title = $request['title'];
            $new_page->content = $request['content'];
            $new_page->permalink = $request['permalink'];
            $new_page->meta_title = $request['meta_title'];
            $new_page->meta_description = $request['meta_description'];
            $new_page->meta_image = $request['meta_image'];
            $new_page->status = $request['status'];
            $new_page->build_with = $request->has('build_with') ? config('settings.page_build_with.builder') : config('settings.page_build_with.editor');
            $new_page->author = $author;
            $new_page->has_custom_header = $request->has('has_custom_header') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $new_page->header = $request['header'];
            $new_page->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update a page
     * 
     * @param Object $request
     * @return bool
     */
    public function updatePage($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != defaultLangCode()) {
                $page_translation = PageTranslation::firstOrNew(['page_id' => $request['id'], 'lang' => $request['lang']]);
                $page_translation->title = x_clean($request['title']);
                $page_translation->content = x_clean($request['content']);
                $page_translation->save();
            } else {
                $page = Page::findOrFail($request['id']);
                $page->title = $request['title'];
                $page->content = $request['content'];
                $page->permalink = $request['permalink'];
                $page->meta_title = $request['meta_title'];
                $page->meta_description = $request['meta_description'];
                $page->meta_image = $request['meta_image'];
                $page->status = $request['status'];
                $page->build_with = $request->has('build_with') ? config('settings.page_build_with.builder') : config('settings.page_build_with.editor');
                $page->has_custom_header = $request->has('has_custom_header') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $page->header = $request['header'];
                $page->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return page list
     * 
     * @param Array $request
     * @return Collections
     */
    public function pageList($request)
    {
        return Page::with(['authorInfo' => function ($q) {
            $q->select('name', 'id');
        }])->orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }

    /**
     * Find an active page by its permalink
     *
     * @param string $permalink
     * @return Page|null
     */
    public function getPageByPermalink(string $permalink): ?Page
    {
        return Page::with('page_translations')
            ->where('permalink', $permalink)
            ->where('status', config('settings.page_status.active'))
            ->first();
    }

    /**
     * Will delete a page
     *
     * @param Int $id
     * @return bool
     */
    public function deletePage($id)
    {
        try {
            DB::beginTransaction();
            $page = Page::find($id);
            $page->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
}
