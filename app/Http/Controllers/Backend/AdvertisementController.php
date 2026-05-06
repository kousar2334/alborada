<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\AdvertisementRequest;
use App\Repository\AdvertisementRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    public function __construct(private AdvertisementRepository $advertisementRepository) {}

    public function index(Request $request): View
    {
        $advertisements = $this->advertisementRepository->advertisementList($request);

        return view('backend.modules.advertisement.list', compact('advertisements'));
    }

    public function store(AdvertisementRequest $request): JsonResponse
    {
        return response()->json([
            'success' => $this->advertisementRepository->store($request),
        ]);
    }

    public function edit(Request $request): JsonResponse
    {
        $advertisement = $this->advertisementRepository->findById((int) $request->id);

        return response()->json([
            'success' => true,
            'html' => view('backend.modules.advertisement.edit', compact('advertisement'))->render(),
        ]);
    }

    public function update(AdvertisementRequest $request): JsonResponse
    {
        return response()->json([
            'success' => $this->advertisementRepository->update($request),
        ]);
    }

    public function delete(Request $request): RedirectResponse
    {
        if ($this->advertisementRepository->delete((int) $request->id)) {
            toastNotification('success', 'Advertisement deleted successfully', 'Success');
        } else {
            toastNotification('error', 'Advertisement delete failed', 'Error');
        }

        return to_route('admin.advertisement.list');
    }

    public function analytics(int $id, Request $request): View
    {
        $analytics = $this->advertisementRepository->getAnalytics($id, (int) $request->get('days', 30));

        return view('backend.modules.advertisement.analytics', $analytics);
    }

    public function trackImpression(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $this->advertisementRepository->recordImpression((int) $request->id);

        return response()->json(['success' => true]);
    }

    public function trackClick(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $this->advertisementRepository->recordClick((int) $request->id);

        return response()->json(['success' => true]);
    }
}
