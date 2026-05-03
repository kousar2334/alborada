<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdReport;
use App\Models\ReportReason;
use Illuminate\Http\Request;

class AdReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = AdReport::with(['ad', 'user', 'reason.reason_translations'])
            ->when($request->filled('reason_id'), fn($q) => $q->where('reason_id', $request->reason_id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $reasons = ReportReason::with('reason_translations')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('id')
            ->get();

        return view('backend.modules.ads.reports.list', compact('reports', 'reasons'));
    }

    public function delete(Request $request)
    {
        AdReport::findOrFail($request->id)->delete();

        toastNotification('success', 'Report deleted successfully', 'Success');
        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['id' => 'required|exists:ad_reports,id', 'status' => 'required|in:0,1,2']);

        AdReport::where('id', $request->id)->update(['status' => $request->status]);

        toastNotification('success', 'Report status updated', 'Success');
        return redirect()->back();
    }
}
