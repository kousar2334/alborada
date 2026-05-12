<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppDownloaderCode;
use Illuminate\Http\Request;

class AppDownloaderCodeController extends Controller
{
    public function index()
    {
        $codes = AppDownloaderCode::orderBy('sort_order')->orderBy('device_type')->get();
        return view('backend.modules.downloader-codes.index', compact('codes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'       => 'required|string|max:100',
            'code'        => 'required|string|max:50',
            'device_type' => 'required|in:firestick,android,ios,smart_tv,desktop,other',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        AppDownloaderCode::create($data);
        toastNotification('success', __tr('App code added successfully.'));
        return redirect()->route('admin.downloader-codes.index');
    }

    public function update(Request $request, AppDownloaderCode $downloaderCode)
    {
        $data = $request->validate([
            'label'       => 'required|string|max:100',
            'code'        => 'required|string|max:50',
            'device_type' => 'required|in:firestick,android,ios,smart_tv,desktop,other',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $downloaderCode->update($data);
        toastNotification('success', __tr('App code updated successfully.'));
        return redirect()->route('admin.downloader-codes.index');
    }

    public function destroy(AppDownloaderCode $downloaderCode)
    {
        $downloaderCode->delete();
        toastNotification('success', __tr('App code deleted.'));
        return redirect()->route('admin.downloader-codes.index');
    }

    public function toggleStatus(AppDownloaderCode $downloaderCode)
    {
        $downloaderCode->update(['is_active' => !$downloaderCode->is_active]);
        return response()->json(['success' => true, 'is_active' => $downloaderCode->is_active]);
    }
}
