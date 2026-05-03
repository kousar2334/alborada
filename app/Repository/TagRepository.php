<?php

namespace App\Repository;

use App\Models\AdsTag;

class TagRepository
{
    //Will return ads tags
    public function adsTagList($request)
    {
        $query = AdsTag::orderBy('id', 'DESC');

        if ($request->has('search')) {
            $query = $query->where('title', 'like', '%' . $request['search'] . '%');
        }
        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Will store new tag
     */
    public function storeNewTags($request)
    {
        try {
            $tags = explode(',', $request['tags']);
            foreach ($tags as $key => $tag) {
                $new_tag = new AdsTag();
                $new_tag->title = xss_clean($tag);
                $new_tag->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will delete a tag
     */
    public function deleteATag(int $id)
    {
        try {
            $tag = AdsTag::findOrFail($id);
            $tag->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will update a tag
     */
    public function updateATag($request)
    {
        try {
            $tag = AdsTag::findOrFail($request['id']);
            $tag->title = $request['title'];
            $tag->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will apply bulk action
     */
    public function bulkAction($request)
    {
        try {
            foreach ($request['items'] as $item) {
                $tag = AdsTag::find($item);
                if ($tag != null & $request['action'] == 'delete_all') {
                    $tag->delete();
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
