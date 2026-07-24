<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PricingPlanRequest;
use App\Repository\PricingPlanRepository;
use Illuminate\Http\Request;

class PricingPlanController extends Controller
{
    public function __construct(protected PricingPlanRepository $repo) {}

    public function index(Request $request)
    {
        $plans = $this->repo->planList($request);
        $packages = \App\Models\IptvPackage::orderBy('name')->get();
        return view('backend.modules.pricing-plans.list', compact('plans', 'packages'));
    }

    public function store(PricingPlanRequest $request)
    {
        $result = $this->repo->storePlan($request->validated());

        if ($result) {
            return response()->json(['success' => true, 'message' => __tr('Plan created successfully')]);
        }

        return response()->json(['success' => false, 'message' => __tr('Failed to create plan')], 500);
    }

    public function edit(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:pricing_plans,id']);

        $plan = $this->repo->planDetails((int) $request->id);
        $lang = $request->lang ?? defaultLangCode();
        $packages = \App\Models\IptvPackage::orderBy('name')->get();
        $html = view('backend.modules.pricing-plans.edit', compact('plan', 'lang', 'packages'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function update(Request $request)
    {
        $result = $this->repo->updatePlan($request->all());

        if ($result) {
            return response()->json(['success' => true, 'message' => __tr('Plan updated successfully')]);
        }

        return response()->json(['success' => false, 'message' => __tr('Failed to update plan')], 500);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:pricing_plans,id']);

        $result = $this->repo->deletePlan((int) $request->id);

        if ($result) {
            return redirect()->route('admin.pricing.plans.list')
                ->with('success', __tr('Plan deleted successfully'));
        }

        return back()->with('error', __tr('Failed to delete plan'));
    }
}
