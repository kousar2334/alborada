<?php

namespace App\Http\Controllers\Backend;

use App\Models\AdsTag;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use Illuminate\Http\JsonResponse;
use App\Repository\TagRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TagUpdateRequest;

class TagController extends Controller
{
    public function __construct(public TagRepository $tag_repository) {}

    /**
     * Will return tag list
     */
    public function tags(Request $request): View
    {
        $tags = $this->tag_repository->adsTagList($request);

        return view('backend.modules.ads.tags.list', ['tags' => $tags]);
    }
    /**
     * Will store new tag
     */
    public function storeTag(TagRequest $request): JsonResponse
    {
        $res = $this->tag_repository->storeNewTags($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
    /**
     * Will delete tag
     */
    public function deleteTag(Request $request): RedirectResponse
    {
        $res = $this->tag_repository->deleteATag($request['id']);

        if ($res) {
            toastNotification('success', 'Tag deleted successfully', 'Success');
            return to_route('classified.ads.tag.list');
        } else {
            toastNotification('error', 'Tag delete failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will update tag
     */
    public function updateTag(TagUpdateRequest $request)
    {
        $res = $this->tag_repository->updateATag($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }

    /**
     * Will bulk action of  tag
     */
    public function tagBulkAction(Request $request)
    {
        $res = $this->tag_repository->bulkAction($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }

    /**
     * Will return  tag options
     */
    public function tagOption(Request $request): JsonResponse
    {
        $query = AdsTag::query();

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('title', 'LIKE',  '%' . $term . '%');
        }

        $tags = $query->orderBy('id', 'asc')->paginate(2);

        $output = [];

        foreach ($tags->items() as $tag) {
            $item['id'] = $tag->id;
            $item['text'] = $tag->title;
            array_push($output, $item);
        }

        $morePages = true;

        if (empty($tags->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results" => $output,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
