<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuPosition;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed the header and footer navigation menus.
     *
     * If a menu is already assigned to a position (admin built their own),
     * that menu is reused and only missing items are added by title — nothing
     * an admin customized gets overwritten. Items are "custom" links so they
     * work without any Page records existing.
     */
    public function run(): void
    {
        // ── Header ────────────────────────────────────────────────────────
        $headerMenu = $this->menuForPosition(config('settings.menu_position.header'), 'Header Menu');

        $this->addItems($headerMenu, [
            ['title' => 'Home',          'link' => '/'],
            ['title' => 'Pricing Plans', 'link' => '/pricing-plans'],
            ['title' => 'Blog',          'link' => '/blog'],
            ['title' => 'Contact Us',    'link' => '/contact'],
        ]);

        // ── Footer ────────────────────────────────────────────────────────
        // Parents with children render as titled columns in the footer.
        $footerMenu = $this->menuForPosition(config('settings.menu_position.footer'), 'Footer Menu');

        $company = $this->addItems($footerMenu, [
            ['title' => 'Company', 'link' => '#'],
        ])[0];
        $this->addItems($footerMenu, [
            ['title' => 'About Us',      'link' => '/#about'],
            ['title' => 'Pricing Plans', 'link' => '/pricing-plans'],
            ['title' => 'Blog',          'link' => '/blog'],
            ['title' => 'Contact Us',    'link' => '/contact'],
        ], $company->id);

        $support = $this->addItems($footerMenu, [
            ['title' => 'Support', 'link' => '#'],
        ])[0];
        $this->addItems($footerMenu, [
            ['title' => 'Help Center',  'link' => '/member/support'],
            ['title' => 'Setup Guide',  'link' => '/member/setup-guide'],
            ['title' => 'Download App', 'link' => '/member/download-app'],
            ['title' => 'FAQ',          'link' => '/#faq'],
        ], $support->id);
    }

    /**
     * Return the menu assigned to the given position, creating and assigning
     * one when the position is still empty.
     */
    private function menuForPosition(int $positionId, string $title): Menu
    {
        $position = MenuPosition::where('position_id', $positionId)->first();

        if ($position && $position->menu_id) {
            $menu = Menu::find($position->menu_id);
            if ($menu) {
                return $menu;
            }
        }

        $menu = Menu::where('title', $title)->first();
        if (!$menu) {
            $menu = new Menu();
            $menu->title = $title;
            $menu->save();
        }

        if ($position) {
            $position->menu_id = $menu->id;
            $position->save();
        } else {
            $position = new MenuPosition();
            $position->position_id = $positionId;
            $position->menu_id = $menu->id;
            $position->save();
        }

        return $menu;
    }

    /**
     * Add the given items to the menu, skipping titles that already exist at
     * the same level. Returns the (found or created) items in input order.
     */
    private function addItems(Menu $menu, array $items, ?int $parentId = null): array
    {
        $customType = config('settings.menu_item_type.custom', 1);
        $nextPosition = (int) MenuItem::where('menu_id', $menu->id)
            ->where('parent', $parentId)
            ->max('position') + 1;

        $result = [];
        foreach ($items as $item) {
            $existing = MenuItem::where('menu_id', $menu->id)
                ->where('parent', $parentId)
                ->where('title', $item['title'])
                ->first();

            if ($existing) {
                $result[] = $existing;
                continue;
            }

            $menuItem = new MenuItem();
            $menuItem->menu_id = $menu->id;
            $menuItem->parent = $parentId;
            $menuItem->item_type = $customType;
            $menuItem->title = $item['title'];
            $menuItem->link = $item['link'];
            $menuItem->position = $nextPosition++;
            $menuItem->target = 0;
            $menuItem->save();

            $result[] = $menuItem;
        }

        return $result;
    }
}
