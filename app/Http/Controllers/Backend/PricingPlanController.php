<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Repository\PricingPlanRepository;
use App\Http\Requests\PricingPlanRequest;

class PricingPlanController extends Controller
{
    public function __construct(
        public PricingPlanRepository $planRepository
    ) {}

    /**
     * Will return all pricing plans
     */
    public function plans(Request $request): View
    {
        $status = $request->has('status') && $request['status'] != null
            ? [$request['status']]
            : [config('settings.general_status.active'), config('settings.general_status.in_active')];

        $plans = $this->planRepository->planList($request, $status);

        return view('backend.modules.pricing-plans.list', [
            'plans' => $plans,
        ]);
    }

    /**
     * Will store new pricing plan
     */
    public function planStore(PricingPlanRequest $request): JsonResponse
    {
        $res = $this->planRepository->storePlan($request);
        if ($res) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Will return pricing plan edit form
     */
    public function planEdit(Request $request): JsonResponse
    {
        $plan = $this->planRepository->planDetails($request['id']);
        $lang = $request->input('lang', defaultLangCode());

        return response()->json([
            'success' => true,
            'html' => view('backend.modules.pricing-plans.edit', [
                'plan'  => $plan,
                'lang'  => $lang,
            ])->render()
        ]);
    }

    /**
     * Will update pricing plan
     */
    public function planUpdate(PricingPlanRequest $request): JsonResponse
    {
        $res = $this->planRepository->updatePlan($request);
        if ($res) {
            return response()->json([
                'success' => true,
                'message' => __tr('Pricing plan updated successfully'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __tr('Pricing plan update failed'),
            ]);
        }
    }

    /**
     * Will delete a pricing plan
     */
    public function planDelete(Request $request): RedirectResponse
    {
        $res = $this->planRepository->deletePlan($request['id']);
        if ($res) {
            toastNotification('success', 'Pricing plan deleted successfully', 'Success');
        } else {
            toastNotification('error', 'Pricing plan delete failed', 'Error');
        }
        return redirect()->back();
    }
}
