<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    /**
     * Seed the four subscription plans. Regular prices are $20/$60/$120/$240;
     * the offer prices ($15/$45/$90/$180) are the launch promotion for the
     * first 20 clients and are shown as a limited-time offer in the pricing
     * table. Remove the offer by clearing offer_price in the admin panel.
     * No free-trial plans are seeded — trials are not part of the offering.
     */
    public function run(): void
    {
        $plans = [
            [
                'title'         => '1 Month',
                'duration_days' => 30,
                'price'         => 20.00,
                'offer_price'   => 15.00,
                'sort_order'    => 1,
            ],
            [
                'title'         => '3 Months',
                'duration_days' => 90,
                'price'         => 60.00,
                'offer_price'   => 45.00,
                'sort_order'    => 2,
            ],
            [
                'title'         => '6 Months',
                'duration_days' => 180,
                'price'         => 120.00,
                'offer_price'   => 90.00,
                'sort_order'    => 3,
            ],
            [
                'title'         => '12 Months',
                'duration_days' => 365,
                'price'         => 240.00,
                'offer_price'   => 180.00,
                'sort_order'    => 4,
            ],
        ];

        foreach ($plans as $plan) {
            PricingPlan::updateOrCreate(
                ['title' => $plan['title']],
                $plan + [
                    'status'            => 1,
                    'max_connections'   => 2,
                    'streaming_quality' => '4K',
                    'catchup_days'      => 7,
                    'dvr_enabled'       => true,
                    'is_trial'          => false,
                    'trial_days'        => null,
                ]
            );
        }

        // The client's offering is exactly these four plans, with no free
        // trial — retire trial/free plans and any leftover plans from earlier
        // setups so the pricing table shows only the confirmed lineup.
        // (Deactivated, not deleted — admins can re-enable from the panel.)
        PricingPlan::whereNotIn('title', array_column($plans, 'title'))
            ->orWhere('is_trial', true)
            ->orWhere('price', 0)
            ->update(['status' => 0]);
    }
}
