<?php

namespace App\Repository;

use App\Models\AdsCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\AdsCategoryTranslation;

class CategoryRepository
{

    //Will return ads category
    public function adsCategoryList($request, $status = [1, 2])
    {
        $query = AdsCategory::with(['child'])->orderBy('id', 'DESC');


        if ($request->has('search')) {
            $query = $query->where('title', 'like', '%' . $request['search'] . '%');
        }
        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Will store a new category
     */
    public function storeCategory($data)
    {
        try {
            $category = new AdsCategory();
            $category->title = $data['title'];
            $category->permalink = Str::slug($data['title']);
            $category->icon = $data['icon'];
            $category->image = $data['image'];
            $category->parent = $data['parent'];
            $category->status = $data['status'];
            $category->save();
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }
    /**
     * Will delete a category
     * 
     * @param int $id
     */
    public function deleteCategory(int $id): bool
    {
        try {
            DB::beginTransaction();
            $category = AdsCategory::findOrFail($id);
            $category->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return category details with translations
     */
    public function categoryDetails(int $id)
    {
        return AdsCategory::with('ads_category_translations')->findOrFail($id);
    }

    /**
     * Will update category; handles translation vs default lang
     */
    public function updateCategory($request): bool
    {
        try {
            DB::beginTransaction();
            $category = AdsCategory::findOrFail($request['id']);

            if (!empty($request['lang']) && $request['lang'] != defaultLangCode()) {
                $translation = AdsCategoryTranslation::firstOrNew([
                    'category_id' => $category->id,
                    'lang'        => $request['lang'],
                ]);
                $translation->title = x_clean($request['title']);
                $translation->save();
            } else {
                $category->title  = $request['title'];
                $category->icon   = $request['icon_edit'];
                $category->image  = $request['image_edit'];
                $category->parent = $request['parent'];
                $category->status = $request['status'];
                $category->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
