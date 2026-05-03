<?php

namespace App\Repository;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignStat;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsletterRepository
{
    // ────────────────────────────── SUBSCRIBERS ──────────────────────────────

    public function subscriberList($request)
    {
        $query = NewsletterSubscriber::orderByDesc('id');

        if (!empty($request['search'])) {
            $query->where(function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request['search'] . '%')
                    ->orWhere('name', 'like', '%' . $request['search'] . '%');
            });
        }

        if (!empty($request['status'])) {
            $query->where('status', $request['status']);
        }

        $per_page = ($request['per_page'] ?? 10) === 'all'
            ? $query->count()
            : ($request['per_page'] ?? 10);

        return $query->paginate($per_page)->withQueryString();
    }

    public function subscribe(string $email, ?string $name = null): bool
    {
        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            // Re-activate if previously unsubscribed
            if ($existing->status == 2) {
                $existing->status = 1;
                $existing->unsubscribed_at = null;
                $existing->save();
            }
            return true;
        }

        NewsletterSubscriber::create([
            'email'  => $email,
            'name'   => $name,
            'status' => 1,
            'token'  => Str::random(64),
        ]);

        return true;
    }

    public function unsubscribeByToken(string $token): bool
    {
        $sub = NewsletterSubscriber::where('token', $token)->first();
        if (!$sub) return false;

        $sub->status = 2;
        $sub->unsubscribed_at = now();
        $sub->save();

        return true;
    }

    public function deleteSubscriber(int $id): bool
    {
        try {
            NewsletterSubscriber::findOrFail($id)->delete();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function allActiveSubscribers()
    {
        return NewsletterSubscriber::active()->get();
    }

    // ────────────────────────────── CAMPAIGNS ──────────────────────────────

    public function campaignList($request)
    {
        $query = NewsletterCampaign::orderByDesc('id');

        if (!empty($request['search'])) {
            $query->where('subject', 'like', '%' . $request['search'] . '%');
        }

        if (!empty($request['status'])) {
            $query->where('status', $request['status']);
        }

        $per_page = ($request['per_page'] ?? 10) === 'all'
            ? $query->count()
            : ($request['per_page'] ?? 10);

        return $query->paginate($per_page)->withQueryString();
    }

    public function createCampaign(array $data): NewsletterCampaign
    {
        return NewsletterCampaign::create([
            'subject' => $data['subject'],
            'content' => $data['content'],
            'status'  => 'draft',
        ]);
    }

    public function updateCampaign(int $id, array $data): bool
    {
        try {
            $campaign = NewsletterCampaign::findOrFail($id);
            if ($campaign->status === 'sent') return false;

            $campaign->subject = $data['subject'];
            $campaign->content = $data['content'];
            $campaign->save();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function deleteCampaign(int $id): bool
    {
        try {
            NewsletterCampaign::findOrFail($id)->delete();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function findCampaign(int $id): NewsletterCampaign
    {
        return NewsletterCampaign::findOrFail($id);
    }

    public function markCampaignSending(int $id): void
    {
        NewsletterCampaign::where('id', $id)->update(['status' => 'sending']);
    }

    public function markCampaignSent(int $id, int $totalSent): void
    {
        NewsletterCampaign::where('id', $id)->update([
            'status'     => 'sent',
            'total_sent' => $totalSent,
            'sent_at'    => now(),
        ]);
    }

    public function createStatRecord(int $campaignId, int $subscriberId): void
    {
        NewsletterCampaignStat::firstOrCreate(
            ['campaign_id' => $campaignId, 'subscriber_id' => $subscriberId]
        );
    }

    // ────────────────────────────── TRACKING ──────────────────────────────

    public function trackOpen(int $campaignId, int $subscriberId): void
    {
        $stat = NewsletterCampaignStat::where('campaign_id', $campaignId)
            ->where('subscriber_id', $subscriberId)
            ->first();

        if ($stat && !$stat->opened_at) {
            $stat->opened_at = now();
            $stat->save();
            NewsletterCampaign::where('id', $campaignId)->increment('total_opened');
        }
    }

    public function trackClick(int $campaignId, int $subscriberId): void
    {
        $stat = NewsletterCampaignStat::where('campaign_id', $campaignId)
            ->where('subscriber_id', $subscriberId)
            ->first();

        if ($stat && !$stat->clicked_at) {
            $stat->clicked_at = now();
            $stat->save();
            NewsletterCampaign::where('id', $campaignId)->increment('total_clicked');
        }
    }

    // ────────────────────────────── STATS ──────────────────────────────

    public function getCampaignStats(int $id): array
    {
        $campaign = NewsletterCampaign::with('stats.subscriber')->findOrFail($id);

        $opensByDay = NewsletterCampaignStat::where('campaign_id', $id)
            ->whereNotNull('opened_at')
            ->selectRaw('DATE(opened_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $clicksByDay = NewsletterCampaignStat::where('campaign_id', $id)
            ->whereNotNull('clicked_at')
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Build unified date range
        $dates = $opensByDay->keys()->merge($clicksByDay->keys())->unique()->sort()->values();

        $opensData  = $dates->map(fn($d) => $opensByDay->has($d)  ? $opensByDay[$d]->count  : 0);
        $clicksData = $dates->map(fn($d) => $clicksByDay->has($d) ? $clicksByDay[$d]->count : 0);

        return compact('campaign', 'dates', 'opensData', 'clicksData');
    }

    public function subscriberStats(): array
    {
        return [
            'total'        => NewsletterSubscriber::count(),
            'active'       => NewsletterSubscriber::where('status', 1)->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 2)->count(),
        ];
    }
}
