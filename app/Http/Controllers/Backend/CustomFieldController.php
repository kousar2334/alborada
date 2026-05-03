<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CustomFieldRequest;
use App\Repository\CustomFieldRepository;
use App\Http\Requests\CustomFieldOptionRequest;
use BeyondCode\QueryDetector\Outputs\Json;

class CustomFieldController extends Controller
{
    public function __construct(public CustomFieldRepository $customFieldRepository) {}
    /**
     * Will return custom fields
     */
    public function customFields(Request $request): View
    {
        $status = $request->has('status') && $request['status'] != null ? [$request['status']] : [config('settings.general_status.active'), config('settings.general_status.in_active')];
        $fields = $this->customFieldRepository->adsCustomFiledList($request, $status);

        return view('backend.modules.ads.custom-field.list', ['fields' => $fields]);
    }
    /**
     * Will store new custom field
     */
    public function storeCustomField(CustomFieldRequest $request): JsonResponse
    {
        $res = $this->customFieldRepository->storeNewCustomField($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
    /**
     * Will delete custom field
     */
    public function deleteCustomField(Request $request): RedirectResponse
    {
        $res = $this->customFieldRepository->deleteAField($request['id']);

        if ($res) {
            toastNotification('success', 'Custom field delete successfully', 'Success');
            return to_route('classified.ads.custom.field.list');
        } else {
            toastNotification('error', 'Field delete failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will redirect edit page (modal)
     */
    public function editCustomField(Request $request): JsonResponse
    {
        $field = $this->customFieldRepository->fieldDetails($request['id']);
        return response()->json([
            'success' => true,
            'html' => view('backend.modules.ads.custom-field.edit', ['field' => $field])->render(),
        ]);
    }

    /**
     * Will show custom field edit page with language tabs
     */
    public function editCustomFieldPage(int $id, Request $request): View
    {
        $field = $this->customFieldRepository->fieldDetails($id);
        return view('backend.modules.ads.custom-field.edit_page', compact('field'));
    }

    /**
     * Will update custom field
     */
    public function updateCustomField(CustomFieldRequest $request): JsonResponse
    {
        $res = $this->customFieldRepository->updateAField($request);

        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'Custom field updated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Field update failed',
        ]);
    }
    /**
     * Will apply bulk action
     */
    public function customFieldBulkAction(Request $request): JsonResponse
    {
        $res = $this->customFieldRepository->bulkAction($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
    /**
     * Will assign category
     */
    public function assignCategory(Request $request): JsonResponse
    {
        $res = $this->customFieldRepository->assignACategory($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Will return custom field options
     */
    public function customFieldOptions($id, Request $request): View
    {
        $status = $request->has('status') && $request['status'] != null ? [$request['status']] : [config('settings.general_status.active'), config('settings.general_status.in_active')];

        $options = $this->customFieldRepository->fieldOptions($id, $request, $status);
        $field = $this->customFieldRepository->fieldDetails($id);

        return view('backend.modules.ads.custom-field.options', ['options' => $options, 'field' => $field]);
    }

    /**
     * Will return new option
     */
    public function customFieldOptionStore(CustomFieldOptionRequest $request): JsonResponse
    {
        $res = $this->customFieldRepository->storeCustomFieldOption($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
    /**
     * Will delete field option
     */
    public function customFieldOptionDelete(Request $request): RedirectResponse
    {
        $res = $this->customFieldRepository->deleteCustomFieldOption($request);

        if ($res) {
            toastNotification('success', 'Option Deleted Successfully', 'Success');
        } else {
            toastNotification('error', 'Option Delete Failed', 'Error');
        }
        return to_route('classified.ads.custom.field.options', ['id' => $request['field_id']]);
    }
    /**
     * Will redirect option edit page (modal)
     */
    public function customFieldOptionEdit(Request $request): JsonResponse|View
    {
        $option = $this->customFieldRepository->optionDetails($request['id']);

        return response()->json([
            'success' => true,
            'html' => view('backend.modules.ads.custom-field.option_edit', ['option' => $option])->render(),
        ]);
    }

    /**
     * Will show option edit page with language tabs
     */
    public function editOptionPage(int $id, Request $request): View
    {
        $option = $this->customFieldRepository->optionDetails($id);
        return view('backend.modules.ads.custom-field.option_edit_page', compact('option'));
    }

    /**
     * Will update option
     */
    public function customFieldOptionUpdate(CustomFieldOptionRequest $request): JsonResponse
    {
        $res = $this->customFieldRepository->updateCustomFieldOption($request);

        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'Option updated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Option update failed',
        ]);
    }
    /**
     * Will apply bulk action
     */
    public function customFieldOptionBulkAction(Request $request): JsonResponse
    {
        $res = $this->customFieldRepository->optionBulkAction($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
}
