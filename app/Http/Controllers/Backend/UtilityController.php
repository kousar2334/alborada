<?php

namespace App\Http\Controllers\Backend;

use App\Mail\CommonMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class UtilityController extends Controller
{
    /**
     * Upload editor image
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function storeEditorImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,svg,jpeg,bmp,gif,webp|max:1020',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $image = 'editor' . time() . rand() . '.' . $extension;
            $file->move('public/uploads/editor/', $image);
            $path = '/public/uploads/editor/' . $image;
            return response()->json(['url' => $path]);
        }
    }
    /**
     * Sending mail
     */
    public function sendingEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|max:250',
            'message' => 'required'
        ]);

        try {
            Mail::to($request['email'])->send(new CommonMail(['subject' => $request['subject'], 'message' => $request['message']]));
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }
    /**
     * Will clear cache
     */
    public function clearCache(): RedirectResponse
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('optimize:clear');
            toastNotification('success', 'Cache clear successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            toastNotification('error', 'Cache clear failed', 'Error');
            return redirect()->back();
        }
    }
}
