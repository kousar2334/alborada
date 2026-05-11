<?php

namespace App\Repository;

use App\Models\PricingPlan;
use App\Models\PricingPlanTranslation;
use Illuminate\Support\Facades\DB;

class PricingPlanRepository
{
    public function planList($request, $status = [1, 0])
    {
        $query = PricingPlan::orderBy('id', 'DESC');

        if ($request->has('search') && $request['search'] != null) {
            $query = $query->where('title', 'like', '%' . $request['search'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page == 'all') {
            return $query->whereIn('status', $status)->paginate($query->count())->withQueryString();
        }

        return $query->whereIn('status', $status)->paginate($per_page)->withQueryString();
    }

    public function storePlan($data): bool
    {
        try {
            DB::beginTransaction();
            $plan = new PricingPlan();
            $plan->title = $data['title'];
            $plan->duration_days = $data['duration_days'];
            $plan->price = $data['price'];
            $plan->listing_quantity = $data['listing_quantity'];
            $plan->featured_listing_quantity = $data['featured_listing_quantity'];
            $plan->gallery_image_quantity = $data['gallery_image_quantity'];
            $plan->membership_badge = $data['membership_badge'] ?? 0;
            $plan->status = $data['status'];
            $plan->max_connections = $data['max_connections'] ?? 1;
            $plan->streaming_quality = $data['streaming_quality'] ?? 'HD';
            $plan->catchup_days = $data['catchup_days'] ?? 0;
            $plan->dvr_enabled = $data['dvr_enabled'] ?? 0;
            $plan->is_trial = $data['is_trial'] ?? 0;
            $plan->trial_days = $data['trial_days'] ?? null;
            $plan->sort_order = $data['sort_order'] ?? 0;
            $plan->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function planDetails(int $id)
    {
        return PricingPlan::with('pricing_plan_translations')->findOrFail($id);
    }

    public function updatePlan($request): bool
    {
        try {
            DB::beginTransaction();
            $plan = PricingPlan::findOrFail($request['id']);
            $lang = $request['lang'] ?? defaultLangCode();

            if ($lang !== defaultLangCode()) {
                $translation = PricingPlanTranslation::firstOrNew([
                    'plan_id' => $plan->id,
                    'lang'    => $lang,
                ]);
                $translation->title = x_clean($request['title']);
                $translation->save();
            } else {
                $plan->title = $request['title'];
                $plan->duration_days = $request['duration_days'];
                $plan->price = $request['price'];
                $plan->listing_quantity = $request['listing_quantity'];
                $plan->featured_listing_quantity = $request['featured_listing_quantity'];
                $plan->gallery_image_quantity = $request['gallery_image_quantity'];
                $plan->membership_badge = $request['membership_badge'] ?? 0;
                $plan->status = $request['status'];
                $plan->max_connections = $request['max_connections'] ?? 1;
                $plan->streaming_quality = $request['streaming_quality'] ?? 'HD';
                $plan->catchup_days = $request['catchup_days'] ?? 0;
                $plan->dvr_enabled = $request['dvr_enabled'] ?? 0;
                $plan->is_trial = $request['is_trial'] ?? 0;
                $plan->trial_days = $request['trial_days'] ?? null;
                $plan->sort_order = $request['sort_order'] ?? 0;
                $plan->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function deletePlan(int $id): bool
    {
        try {
            DB::beginTransaction();
            $plan = PricingPlan::findOrFail($id);
            $plan->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
