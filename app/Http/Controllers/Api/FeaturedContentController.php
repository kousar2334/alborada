<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeaturedContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Public, read-only feed of promotional content (movies, series, sports
 * events, new releases) for external player apps (XCIPTV, Smarters, etc.)
 * to display as commercials / "coming soon" tiles.
 */
class FeaturedContentController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Cache::remember('api:featured-content', now()->addMinutes(10), function () {
            return FeaturedContent::where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get()
                ->map(fn (FeaturedContent $item) => [
                    'id'           => $item->id,
                    'title'        => $item->title,
                    'subtitle'     => $item->subtitle,
                    'type'         => $item->type,
                    'type_label'   => $item->type_label,
                    'genre'        => $item->genre,
                    'release_year' => $item->release_year,
                    'rating'       => $item->rating,
                    'event_date'   => $item->event_date?->toDateString(),
                    'badge_text'   => $item->badge_text,
                    'thumbnail'    => $item->thumbnail ? asset(getFilePath($item->thumbnail, true)) : null,
                    'trailer_url'  => $item->trailer_url,
                    'youtube_id'   => $item->youtube_embed_id,
                ])
                ->values()
                ->all();
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
