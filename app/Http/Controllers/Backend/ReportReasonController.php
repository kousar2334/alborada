<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ReportReason;
use App\Models\ReportReasonTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportReasonController extends Controller
{
    public function index(): View
    {
        $reasons = ReportReason::with('reason_translations')
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return view('backend.modules.ads.report-reasons.index', compact('reasons'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'  => 'required|string|max:200',
            'status' => 'required|in:1,2',
        ]);

        try {
            ReportReason::create([
                'title'  => xss_clean($request->title),
                'status' => $request->status,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function edit($id, Request $request): View
    {
        $reason = ReportReason::with('reason_translations')->findOrFail($id);
        return view('backend.modules.ads.report-reasons.edit', compact('reason'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'id'     => 'required|exists:report_reasons,id',
            'title'  => 'required|string|max:200',
            'status' => 'required|in:1,2',
        ]);

        try {
            DB::beginTransaction();

            if ($request->lang && $request->lang !== defaultLangCode()) {
                $translation = ReportReasonTranslation::firstOrNew([
                    'reason_id' => $request->id,
                    'lang'      => $request->lang,
                ]);
                $translation->title = xss_clean($request->title);
                $translation->save();
            } else {
                $reason = ReportReason::findOrFail($request->id);
                $reason->title  = xss_clean($request->title);
                $reason->status = $request->status;
                $reason->save();
            }

            DB::commit();
            toastNotification('success', 'Report reason updated successfully', 'Success');
            return to_route('classified.ads.report.reasons.edit', [
                'id'   => $request->id,
                'lang' => $request->lang ?? defaultLangCode(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        }
    }

    public function delete(Request $request): RedirectResponse
    {
        try {
            ReportReason::findOrFail($request->id)->delete();
            toastNotification('success', 'Report reason deleted successfully', 'Success');
        } catch (\Exception $e) {
            toastNotification('error', 'Delete failed', 'Error');
        }

        return to_route('classified.ads.report.reasons.list');
    }
}
