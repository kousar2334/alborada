<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ConditionRequest;
use App\Repository\ConditionRepository;
use BeyondCode\QueryDetector\Outputs\Json;

class ConditionController extends Controller
{
    public function __construct(public ConditionRepository $condition_repository) {}

    /**
     * Will return all condition list
     */
    public function conditions(Request $request): View
    {
        $status = $request->has('status') && $request['status'] != null ? [$request['status']] : [config('settings.general_status.active'), config('settings.general_status.in_active')];
        $conditions = $this->condition_repository->adsConditionList($request, $status);

        return view('backend.modules.ads.condition.list', ['conditions' => $conditions]);
    }
    /**
     * Will store condition
     */
    public function storeCondition(ConditionRequest $request): JsonResponse
    {
        $res = $this->condition_repository->storeNewCondition($request);

        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        }
        return response()->json(
            [
                'success' => false,
            ]
        );
    }
    /**
     * Will delete condition
     */
    public function deleteCondition(Request $request): RedirectResponse
    {
        $res = $this->condition_repository->deleteAdsCondition($request['id']);

        if ($res) {
            toastNotification('success', 'Condition deleted successfully', 'Success');
            return to_route('classified.ads.condition.list');
        } else {
            toastNotification('error', 'Condition delete failed');
            return redirect()->back();
        }
    }

    /**
     * Will redirect category edit page (modal)
     */
    public function editCondition(Request $request): JsonResponse|View
    {
        $condition = $this->condition_repository->conditionDetails($request['id']);
        return response()->json([
            'success' => true,
            'html' => view('backend.modules.ads.condition.edit', compact('condition'))->render(),
        ]);
    }

    /**
     * Will show condition edit page with language tabs
     */
    public function editConditionPage(int $id, Request $request): View
    {
        $condition = $this->condition_repository->conditionDetails($id);
        return view('backend.modules.ads.condition.edit_page', compact('condition'));
    }

    /**
     * Will update Condition
     */
    public function updateCondition(ConditionRequest $request): JsonResponse|RedirectResponse
    {
        $res = $this->condition_repository->updateAdsCondition($request);
        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'Condition updated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Condition update failed',
        ]);
    }
}
