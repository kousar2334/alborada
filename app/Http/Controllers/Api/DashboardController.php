<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Ad;
use App\Http\Controllers\Api\ApiController;
use App\Models\SavedAd;

class DashboardController extends ApiController
{
    /**
     * Will return customer dashboard overview
     */
    public function customerDashboardOverview(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->customerDashboardContent(auth('jwt-customer')->user()->id)
            ]);
        } catch (\Exception $e) {
            return $this->jsonError();
        }
    }

    public function customerDashboardContent($customer_id)
    {
        $now = now();
        $firstDayLastMonth = $now->subMonth()->startOfMonth();
        $lastMonthName = $firstDayLastMonth->format('F');

        $lastDayLastMonth = $now->subMonth()->endOfMonth();
        $last_month_ads =  Ad::where('user_id', $customer_id)->whereBetween('created_at', [$firstDayLastMonth, $lastDayLastMonth])->count();

        $total_ads = Ad::where('user_id', $customer_id)->count();
        $active_ads = Ad::where('status', config('settings.general_status.active'))->where('user_id', $customer_id)->count();
        $pending_ads = Ad::where('status', config('settings.general_status.in_active'))->where('user_id', $customer_id)->count();
        $total_favourite_ads = SavedAd::where('user_id', $customer_id)->count();

        return [
            'total_ads' => $total_ads,
            'active_ads' => $active_ads,
            'pending_ads' => $pending_ads,
            'total_favourite_ads' => $total_favourite_ads,
            'last_month_ads' => $last_month_ads,
            'last_month_name' => $lastMonthName
        ];
    }
}
