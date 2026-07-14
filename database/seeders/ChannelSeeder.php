<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Seed the homepage "Channel Lineup" ticker. No logo images are shipped —
     * the homepage renders a colored tile with the channel abbreviation when
     * logo is null, so the section works out of the box. Admins can attach
     * real logos later from the Channels module.
     */
    public function run(): void
    {
        $channels = [
            ['name' => 'ESPN',             'bg_color' => '#d00000'],
            ['name' => 'Sky Sports',       'bg_color' => '#001a4d'],
            ['name' => 'beIN Sports',      'bg_color' => '#5c2d91'],
            ['name' => 'HBO',              'bg_color' => '#1a1a1a'],
            ['name' => 'Showtime',         'bg_color' => '#b30000'],
            ['name' => 'BBC One',          'bg_color' => '#8b0000'],
            ['name' => 'CNN',              'bg_color' => '#cc0000'],
            ['name' => 'FOX',              'bg_color' => '#111111'],
            ['name' => 'NBC',              'bg_color' => '#0f3d6e'],
            ['name' => 'NFL Network',      'bg_color' => '#013369'],
            ['name' => 'NBA TV',           'bg_color' => '#17408b'],
            ['name' => 'Discovery',        'bg_color' => '#0a4d8c'],
            ['name' => 'Nat Geo',          'bg_color' => '#c7a008'],
            ['name' => 'AMC',              'bg_color' => '#0d0d0d'],
            ['name' => 'Cartoon Network',  'bg_color' => '#2b2b2b'],
            ['name' => 'MTV',              'bg_color' => '#c21e56'],
            ['name' => 'Disney Channel',   'bg_color' => '#123c8c'],
            ['name' => 'Paramount',        'bg_color' => '#0057a3'],
        ];

        foreach ($channels as $index => $channel) {
            Channel::updateOrCreate(
                ['name' => $channel['name']],
                [
                    'bg_color'   => $channel['bg_color'],
                    'status'     => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
