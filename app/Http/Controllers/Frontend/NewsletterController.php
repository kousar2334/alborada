<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repository\NewsletterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(public NewsletterRepository $newsletter_repository) {}

    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:100',
        ]);

        $this->newsletter_repository->subscribe($request->email, $request->name);

        return response()->json(['success' => true, 'message' => 'You have subscribed successfully!']);
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $this->newsletter_repository->unsubscribeByToken($token);

        toastNotification('success', 'You have been unsubscribed successfully.', 'Unsubscribed');

        return redirect('/');
    }

    public function trackOpen(Request $request, int $campaign, int $subscriber): \Symfony\Component\HttpFoundation\Response
    {
        $this->newsletter_repository->trackOpen($campaign, $subscriber);

        // Return 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
