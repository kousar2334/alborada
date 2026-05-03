<?php

namespace App\Repository;

use App\Models\Advertisement;
use App\Models\AdvertisementStat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdvertisementRepository
{
    /**
     * Paginated list for admin panel
     */
    public function advertisementList($request)
    {
        $query = Advertisement::withCount([
            'stats as total_impressions' => fn($q) => $q->select(DB::raw('COALESCE(SUM(impressions),0)')),
            'stats as total_clicks'      => fn($q) => $q->select(DB::raw('COALESCE(SUM(clicks),0)')),
        ])->orderBy('sort_order', 'ASC')->orderBy('id', 'DESC');

        if ($request->has('search') && $request['search'] != null) {
            $query->where('title', 'like', '%' . $request['search'] . '%');
        }

        if ($request->has('position') && $request['position'] != null) {
            $query->where('position', $request['position']);
        }

        $per_page = ($request->has('per_page') && $request['per_page'] != null)
            ? $request['per_page']
            : 10;

        if ($per_page == 'all') {
            return $query->paginate($query->count())->withQueryString();
        }

        return $query->paginate($per_page)->withQueryString();
    }

    /**
     * Returns active ads for a given position (used on frontend)
     */
    public function getActiveByPosition(string $position)
    {
        return Advertisement::active()
            ->forPosition($position)
            ->orderBy('sort_order', 'ASC')
            ->get();
    }

    /**
     * Store a new advertisement
     */
    public function store($request): bool
    {
        try {
            $ad = new Advertisement();
            $ad->title      = $request['title'];
            $ad->position   = $request['position'];
            $ad->type       = $request['type'];
            $ad->status     = $request['status'];
            $ad->sort_order = $request['sort_order'] ?? 0;
            $ad->start_date = $request['start_date'] ?? null;
            $ad->end_date   = $request['end_date'] ?? null;

            if ($request['type'] === 'image') {
                $ad->image_path = $request['image_path'];
                $ad->click_url  = $request['click_url'] ?? null;
            } else {
                $ad->html_code = $request['html_code'];
            }

            $ad->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Find advertisement by id
     */
    public function findById(int $id): Advertisement
    {
        return Advertisement::findOrFail($id);
    }

    /**
     * Update advertisement
     */
    public function update($request): bool
    {
        try {
            DB::beginTransaction();
            $ad = Advertisement::findOrFail($request['id']);
            $ad->title      = $request['title'];
            $ad->position   = $request['position'];
            $ad->type       = $request['type'];
            $ad->status     = $request['status'];
            $ad->sort_order = $request['sort_order'] ?? 0;
            $ad->start_date = $request['start_date'] ?? null;
            $ad->end_date   = $request['end_date'] ?? null;

            if ($request['type'] === 'image') {
                $ad->image_path = $request['edit_image_path'];
                $ad->click_url  = $request['click_url'] ?? null;
                $ad->html_code  = null;
            } else {
                $ad->html_code  = $request['html_code'];
                $ad->image_path = null;
                $ad->click_url  = null;
            }

            $ad->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Delete advertisement
     */
    public function delete(int $id): bool
    {
        try {
            $ad = Advertisement::find($id);
            if ($ad) {
                $ad->delete();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Increment today's impression count for an ad
     */
    public function recordImpression(int $id): void
    {
        $stat = AdvertisementStat::firstOrCreate(
            ['advertisement_id' => $id, 'date' => Carbon::today()->toDateString()],
            ['impressions' => 0, 'clicks' => 0]
        );
        $stat->increment('impressions');
    }

    /**
     * Increment today's click count for an ad
     */
    public function recordClick(int $id): void
    {
        $stat = AdvertisementStat::firstOrCreate(
            ['advertisement_id' => $id, 'date' => Carbon::today()->toDateString()],
            ['impressions' => 0, 'clicks' => 0]
        );
        $stat->increment('clicks');
    }

    /**
     * Get daily analytics for a single ad (last 30 days by default)
     */
    public function getAnalytics(int $id, int $days = 30): array
    {
        $ad = Advertisement::withCount([
            'stats as total_impressions' => fn($q) => $q->select(DB::raw('COALESCE(SUM(impressions),0)')),
            'stats as total_clicks'      => fn($q) => $q->select(DB::raw('COALESCE(SUM(clicks),0)')),
        ])->findOrFail($id);

        $from = Carbon::today()->subDays($days - 1);

        $rows = AdvertisementStat::where('advertisement_id', $id)
            ->where('date', '>=', $from)
            ->orderBy('date')
            ->get(['date', 'impressions', 'clicks']);

        // Build a full date range so days with 0 stats show on the chart
        $dates       = [];
        $impressions = [];
        $clicks      = [];
        $indexed     = $rows->keyBy(fn($r) => $r->date);

        for ($i = 0; $i < $days; $i++) {
            $d = $from->copy()->addDays($i)->toDateString();
            $dates[]       = $d;
            $impressions[] = $indexed->has($d) ? $indexed[$d]->impressions : 0;
            $clicks[]      = $indexed->has($d) ? $indexed[$d]->clicks      : 0;
        }

        return compact('ad', 'dates', 'impressions', 'clicks');
    }
}
