<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use App\Models\AdsCondition;
use App\Models\AdsConditionTranslation;

class ConditionRepository
{
    //Will return ads conditions
    public function adsConditionList($request, $status = [1, 2])
    {
        $query = AdsCondition::orderBy('id', 'DESC');

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
    //Will return ads conditions
    public function allAdsCondition()
    {
        $query = AdsCondition::orderBy('id', 'DESC');

        return $query->get();
    }
    /**
     * Will store new condition
     */
    public function storeNewCondition($request)
    {
        try {
            $condition = new AdsCondition();
            $condition->title = $request['title'];
            $condition->status = $request['status'];
            $condition->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * will return condition details
     */
    public function conditionDetails($id)
    {
        return AdsCondition::with('condition_translations')->findOrFail($id);
    }
    /**
     * Will delete condition
     */
    public function deleteAdsCondition($id)
    {
        try {
            $condition = AdsCondition::find($id);
            if ($condition != null) {
                $condition->delete();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Will update condition
     */
    public function updateAdsCondition($request): bool
    {
        try {
            DB::beginTransaction();

            if (!empty($request['lang']) && $request['lang'] != defaultLangCode()) {
                $translation = AdsConditionTranslation::firstOrNew([
                    'condition_id' => $request['id'],
                    'lang'         => $request['lang'],
                ]);
                $translation->title = xss_clean($request['title']);
                $translation->save();
            } else {
                $condition = AdsCondition::findOrFail($request['id']);
                $condition->title  = xss_clean($request['title']);
                $condition->status = $request['status'];
                $condition->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
