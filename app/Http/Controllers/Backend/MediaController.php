<?php

namespace App\Http\Controllers\Backend;

use App\Models\Media;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Repository\MediaRepository;
use Illuminate\Http\RedirectResponse;

class MediaController extends Controller
{

    public function __construct(protected MediaRepository $mediaRepository)
    {
    }

    /**
     * Will return media manager page
     */
    public function mediaManager(Request $request): View
    {
        $query = Media::with(['user']);
        $files = $query->orderBy('id', 'DESC')->paginate(15)->withQueryString();

        return view('backend.modules.media.media_manager', ['files' => $files]);
    }
    /**
     * Will upload media
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function uploadMediaFile(Request $request): JsonResponse
    {
        $res = $this->mediaRepository->uploadMedia($request);

        return response()->json(
            [
                'result' => $res
            ]
        );
    }
    /**
     * Will return media files list
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function mediaList(Request $request): JsonResponse
    {
        try {
            $files = $this->mediaRepository->mediaLists($request);
            $has_more_page = $files->currentPage() == $files->lastPage() ? false : true;
            return response()->json([
                'success' => true,
                'has_more_page' => $has_more_page,
                'currentPage' => $files->currentPage(),
                'items' => view('backend.media.file_list', ['files' => $files, 'selected_items' => json_decode($request['selected_items'], true)])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Will return selected media preview
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function selectedMediaDetails(Request $request): JsonResponse
    {
        try {
            $items = $this->mediaRepository->selectedMediaDetails(json_decode($request['items'], true));
            return response()->json([
                'success' => true,
                'view' => view('backend.media.file_preview', ['files' => $items])->render(),
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }
    /**
     * Will delete a media
     */
    public function deleteMedia(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $media = Media::findOrFail($request['id']);
            $file = getFilePath($media->path, false);
            if (file_exists($file)) {
                unlink($file);
            }
            $media->delete();

            DB::commit();
            toastNotification('success', 'Media Deleted Successfully', 'Success');
            return to_route('admin.media.list');
        } catch (\Exception $e) {
            DB::rollback();
            toastNotification('error', 'Media delete failed', 'Failed');
            return redirect()->back();
        } catch (\Error $e) {
            DB::rollback();
            toastNotification('error', 'Media delete failed', 'Error');
            return redirect()->back();
        }
    }
}
