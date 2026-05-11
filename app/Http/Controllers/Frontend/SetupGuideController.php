<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\IptvProvisioningService;
use Illuminate\View\View;

class SetupGuideController extends Controller
{
    public function __construct(protected IptvProvisioningService $provisioning) {}

    public function index(): View
    {
        $user         = auth()->user();
        $subscription = $user->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->latest()
            ->first();

        $credentials = null;

        if ($subscription) {
            $credentials = $this->provisioning->generateCredentials($user);
        }

        return view('frontend.pages.member.setup-guide', compact('subscription', 'credentials'));
    }
}
