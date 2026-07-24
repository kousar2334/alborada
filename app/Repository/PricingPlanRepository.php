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
            $plan->offer_price = $data['offer_price'] ?? null;
            $plan->status = $data['status'];
            $plan->max_connections = $data['max_connections'] ?? 1;
            $plan->streaming_quality = $data['streaming_quality'] ?? 'HD';
            $plan->catchup_days = $data['catchup_days'] ?? 0;
            $plan->dvr_enabled = $data['dvr_enabled'] ?? 0;
            $plan->is_trial = $data['is_trial'] ?? 0;
            $plan->trial_days = $data['trial_days'] ?? null;
            $plan->sort_order = $data['sort_order'] ?? 0;
            $plan->iptv_package_id = ($data['iptv_package_id'] ?? '') !== '' ? $data['iptv_package_id'] : null;
            $plan->iptv_sub_months = $data['iptv_sub_months'] ?? 1;
            $plan->iptv_device_type = $data['iptv_device_type'] ?? 'm3u';
            $plan->iptv_country = $data['iptv_country'] ?? 'ALL';
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
                $plan->offer_price = ($request['offer_price'] ?? '') !== '' ? $request['offer_price'] : null;
                $plan->status = $request['status'];
                $plan->max_connections = $request['max_connections'] ?? 1;
                $plan->streaming_quality = $request['streaming_quality'] ?? 'HD';
                $plan->catchup_days = $request['catchup_days'] ?? 0;
                $plan->dvr_enabled = $request['dvr_enabled'] ?? 0;
                $plan->is_trial = $request['is_trial'] ?? 0;
                $plan->trial_days = $request['trial_days'] ?? null;
                $plan->sort_order = $request['sort_order'] ?? 0;
                $plan->iptv_package_id = ($request['iptv_package_id'] ?? '') !== '' ? $request['iptv_package_id'] : null;
                $plan->iptv_sub_months = $request['iptv_sub_months'] ?? 1;
                $plan->iptv_device_type = $request['iptv_device_type'] ?? 'm3u';
                $plan->iptv_country = $request['iptv_country'] ?? 'ALL';
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
