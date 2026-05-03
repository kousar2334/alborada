<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterCampaignMail;
use App\Repository\NewsletterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function __construct(public NewsletterRepository $newsletter_repository) {}

    // ──────────────────────────── SUBSCRIBERS ────────────────────────────

    public function subscribers(Request $request): View
    {
        $subscribers = $this->newsletter_repository->subscriberList($request);
        $stats       = $this->newsletter_repository->subscriberStats();
        $links = [
            ['title' => 'Newsletter', 'route' => '', 'active' => false],
            ['title' => 'Subscribers', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.newsletter.subscribers', compact('subscribers', 'stats', 'links'));
    }

    public function deleteSubscriber(Request $request): RedirectResponse
    {
        $res = $this->newsletter_repository->deleteSubscriber($request['id']);

        if ($res) {
            toastNotification('success', 'Subscriber deleted successfully', 'Success');
        } else {
            toastNotification('error', 'Failed to delete subscriber', 'Error');
        }

        return to_route('admin.newsletter.subscribers');
    }

    // ──────────────────────────── CAMPAIGNS ────────────────────────────

    public function campaigns(Request $request): View
    {
        $campaigns = $this->newsletter_repository->campaignList($request);
        $links = [
            ['title' => 'Newsletter', 'route' => '', 'active' => false],
            ['title' => 'Campaigns', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.newsletter.campaigns', compact('campaigns', 'links'));
    }

    public function createCampaign(): View
    {
        $links = [
            ['title' => 'Newsletter', 'route' => '', 'active' => false],
            ['title' => 'Campaigns', 'route' => 'admin.newsletter.campaigns', 'active' => false],
            ['title' => 'Create', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.newsletter.campaign-form', compact('links'));
    }

    public function storeCampaign(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $this->newsletter_repository->createCampaign($request->only('subject', 'content'));

        toastNotification('success', 'Campaign created successfully', 'Success');

        return to_route('admin.newsletter.campaigns');
    }

    public function editCampaign(int $id): View
    {
        $campaign = $this->newsletter_repository->findCampaign($id);
        $links = [
            ['title' => 'Newsletter', 'route' => '', 'active' => false],
            ['title' => 'Campaigns', 'route' => 'admin.newsletter.campaigns', 'active' => false],
            ['title' => 'Edit', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.newsletter.campaign-form', compact('campaign', 'links'));
    }

    public function updateCampaign(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $res = $this->newsletter_repository->updateCampaign($id, $request->only('subject', 'content'));

        if ($res) {
            toastNotification('success', 'Campaign updated successfully', 'Success');
        } else {
            toastNotification('error', 'Cannot update a sent campaign', 'Error');
        }

        return to_route('admin.newsletter.campaigns');
    }

    public function deleteCampaign(Request $request): RedirectResponse
    {
        $res = $this->newsletter_repository->deleteCampaign($request['id']);

        if ($res) {
            toastNotification('success', 'Campaign deleted successfully', 'Success');
        } else {
            toastNotification('error', 'Failed to delete campaign', 'Error');
        }

        return to_route('admin.newsletter.campaigns');
    }

    public function sendCampaign(int $id): RedirectResponse
    {
        $campaign    = $this->newsletter_repository->findCampaign($id);
        $subscribers = $this->newsletter_repository->allActiveSubscribers();

        if ($subscribers->isEmpty()) {
            toastNotification('error', 'No active subscribers to send to', 'Error');
            return to_route('admin.newsletter.campaigns');
        }

        $this->newsletter_repository->markCampaignSending($id);

        $sent = 0;
        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterCampaignMail($campaign, $subscriber));
                $this->newsletter_repository->createStatRecord($id, $subscriber->id);
                $sent++;
            } catch (\Exception) {
                // Continue sending to remaining subscribers
            }
        }

        $this->newsletter_repository->markCampaignSent($id, $sent);

        toastNotification('success', "Campaign sent to {$sent} subscriber(s)", 'Sent');

        return to_route('admin.newsletter.campaigns');
    }

    // ──────────────────────────── STATS ────────────────────────────

    public function campaignStats(int $id): View
    {
        $data  = $this->newsletter_repository->getCampaignStats($id);
        $links = [
            ['title' => 'Newsletter', 'route' => '', 'active' => false],
            ['title' => 'Campaigns', 'route' => 'admin.newsletter.campaigns', 'active' => false],
            ['title' => 'Stats', 'route' => '', 'active' => true],
        ];

        return view('backend.modules.newsletter.stats', $data + compact('links'));
    }
}
