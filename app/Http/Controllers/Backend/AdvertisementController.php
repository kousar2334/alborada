<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Repository\AdvertisementRepository;
use App\Http\Requests\Backend\AdvertisementRequest;

class AdvertisementController extends Controller
{
    public function __construct(public AdvertisementRepository $advertisement_repository) {}

    /**
     * Show all advertisements
     */
    public function index(Request $request): View
    {
        $advertisements = $this->advertisement_repository->advertisementList($request);

        return view('backend.modules.advertisement.list', compact('advertisements'));
    }

    /**
     * Store new advertisement
     */
    public function store(AdvertisementRequest $request): JsonResponse
    {
        $res = $this->advertisement_repository->store($request);

        if ($res) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to create advertisement']);
    }

    /**
     * Return advertisement data for edit modal
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $advertisement = $this->advertisement_repository->findById($request['id']);
            return response()->json([
                'success' => true,
                'html' => view('backend.modules.advertisement.edit', compact('advertisement'))->render(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Advertisement not found']);
        }
    }

    /**
     * Update advertisement
     */
    public function update(AdvertisementRequest $request): JsonResponse
    {
        $res = $this->advertisement_repository->update($request);

        if ($res) {
            return response()->json(['success' => true, 'message' => 'Advertisement updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Advertisement update failed']);
    }

    /**
     * Delete advertisement
     */
    public function delete(Request $request): RedirectResponse
    {
        $res = $this->advertisement_repository->delete($request['id']);

        if ($res) {
            toastNotification('success', 'Advertisement deleted successfully', 'Success');
        } else {
            toastNotification('error', 'Advertisement delete failed', 'Error');
        }

        return to_route('admin.advertisement.list');
    }

    /**
     * Analytics page for a single advertisement
     */
    public function analytics(Request $request, int $id): View
    {
        $days = (int) ($request->query('days', 30));
        if (!in_array($days, [7, 14, 30, 90])) {
            $days = 30;
        }

        $data = $this->advertisement_repository->getAnalytics($id, $days);

        return view('backend.modules.advertisement.analytics', $data + compact('days'));
    }

    /**
     * Record an impression (called via AJAX from frontend)
     */
    public function trackImpression(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        if ($id > 0) {
            $this->advertisement_repository->recordImpression($id);
        }
        return response()->json(['ok' => true]);
    }

    /**
     * Record a click (called via AJAX from frontend)
     */
    public function trackClick(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        if ($id > 0) {
            $this->advertisement_repository->recordClick($id);
        }
        return response()->json(['ok' => true]);
    }
}
