<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------
        // 1. Create all permissions
        // ----------------------------
        $permissions = [

            // ── Dashboard ────────────────────────────────────────────────
            ['name' => 'View Dashboard',                    'module' => 'Dashboard'],

            // ── Admin Users ───────────────────────────────────────────────
            ['name' => 'User List',                         'module' => 'Users'],
            ['name' => 'User Create',                       'module' => 'Users'],
            ['name' => 'User Edit',                         'module' => 'Users'],
            ['name' => 'User Delete',                       'module' => 'Users'],
            ['name' => 'Permission List View',              'module' => 'Users'],
            ['name' => 'Role List View',                    'module' => 'Users'],
            ['name' => 'Role Create',                       'module' => 'Users'],
            ['name' => 'Role Edit',                         'module' => 'Users'],
            ['name' => 'Role Delete',                       'module' => 'Users'],

            // ── Members (frontend) ────────────────────────────────────────
            ['name' => 'Manage Members',                    'module' => 'Members'],
            ['name' => 'Create Member',                     'module' => 'Members'],
            ['name' => 'Edit Member',                       'module' => 'Members'],
            ['name' => 'Delete Member',                     'module' => 'Members'],

            // ── Ads / Listings ────────────────────────────────────────────
            ['name' => 'Manage Ads',                        'module' => 'Ads'],
            ['name' => 'Edit Ad',                           'module' => 'Ads'],
            ['name' => 'Delete Ad',                         'module' => 'Ads'],

            // ── Ad Categories ─────────────────────────────────────────────
            ['name' => 'Manage Ad Categories',              'module' => 'Ads'],
            ['name' => 'Create Ad Category',                'module' => 'Ads'],
            ['name' => 'Edit Ad Category',                  'module' => 'Ads'],
            ['name' => 'Delete Ad Category',                'module' => 'Ads'],

            // ── Conditions ────────────────────────────────────────────────
            ['name' => 'Manage Conditions',                 'module' => 'Ads'],
            ['name' => 'Create Condition',                  'module' => 'Ads'],
            ['name' => 'Edit Condition',                    'module' => 'Ads'],
            ['name' => 'Delete Condition',                  'module' => 'Ads'],

            // ── Tags ──────────────────────────────────────────────────────
            ['name' => 'Manage Tags',                       'module' => 'Ads'],
            ['name' => 'Create Tag',                        'module' => 'Ads'],
            ['name' => 'Edit Tag',                          'module' => 'Ads'],
            ['name' => 'Delete Tag',                        'module' => 'Ads'],

            // ── Custom Fields ─────────────────────────────────────────────
            ['name' => 'Manage Custom Fields',              'module' => 'Ads'],
            ['name' => 'Create Custom Field',               'module' => 'Ads'],
            ['name' => 'Edit Custom Field',                 'module' => 'Ads'],
            ['name' => 'Delete Custom Field',               'module' => 'Ads'],

            // ── Ad Reports ────────────────────────────────────────────────
            ['name' => 'Manage Ad Reports',                 'module' => 'Ads'],
            ['name' => 'Delete Ad Report',                  'module' => 'Ads'],

            // ── Report Reasons ────────────────────────────────────────────
            ['name' => 'Manage Report Reasons',             'module' => 'Ads'],
            ['name' => 'Create Report Reason',              'module' => 'Ads'],
            ['name' => 'Edit Report Reason',                'module' => 'Ads'],
            ['name' => 'Delete Report Reason',              'module' => 'Ads'],

            // ── Pricing Plans ─────────────────────────────────────────────
            ['name' => 'Manage Pricing Plans',              'module' => 'Pricing'],
            ['name' => 'Create Pricing Plan',               'module' => 'Pricing'],
            ['name' => 'Edit Pricing Plan',                 'module' => 'Pricing'],
            ['name' => 'Delete Pricing Plan',               'module' => 'Pricing'],

            // ── Subscriptions ─────────────────────────────────────────────
            ['name' => 'Manage Subscriptions',              'module' => 'Subscriptions'],
            ['name' => 'Approve Subscription',              'module' => 'Subscriptions'],
            ['name' => 'Reject Subscription',               'module' => 'Subscriptions'],
            ['name' => 'Delete Subscription',               'module' => 'Subscriptions'],

            // ── Bank Payments ─────────────────────────────────────────────
            ['name' => 'Manage Bank Payments',              'module' => 'Payments'],
            ['name' => 'Approve Bank Payment',              'module' => 'Payments'],
            ['name' => 'Reject Bank Payment',               'module' => 'Payments'],

            // ── Payment Settings ──────────────────────────────────────────
            ['name' => 'Manage Payment Settings',           'module' => 'Payments'],

            // ── Locations ─────────────────────────────────────────────────
            ['name' => 'Manage Locations',                  'module' => 'Locations'],
            ['name' => 'Create Location',                   'module' => 'Locations'],
            ['name' => 'Edit Location',                     'module' => 'Locations'],
            ['name' => 'Delete Location',                   'module' => 'Locations'],

            // ── Safety Tips ───────────────────────────────────────────────
            ['name' => 'Manage Safety Tips',                'module' => 'Classified Settings'],
            ['name' => 'Create Safety Tip',                 'module' => 'Classified Settings'],
            ['name' => 'Edit Safety Tip',                   'module' => 'Classified Settings'],
            ['name' => 'Delete Safety Tip',                 'module' => 'Classified Settings'],

            // ── Advertisements (banner/slot ads) ──────────────────────────
            ['name' => 'Manage Advertisements',             'module' => 'Advertisements'],
            ['name' => 'Create Advertisement',              'module' => 'Advertisements'],
            ['name' => 'Edit Advertisement',                'module' => 'Advertisements'],
            ['name' => 'Delete Advertisement',              'module' => 'Advertisements'],

            // ── Newsletter ────────────────────────────────────────────────
            ['name' => 'Manage Newsletter',                 'module' => 'Newsletter'],
            ['name' => 'Create Newsletter Campaign',        'module' => 'Newsletter'],
            ['name' => 'Edit Newsletter Campaign',          'module' => 'Newsletter'],
            ['name' => 'Delete Newsletter Campaign',        'module' => 'Newsletter'],
            ['name' => 'Send Newsletter Campaign',          'module' => 'Newsletter'],
            ['name' => 'Delete Newsletter Subscriber',      'module' => 'Newsletter'],

            // ── Conversations ─────────────────────────────────────────────
            ['name' => 'Manage Conversations',              'module' => 'Conversations'],

            // ── Contact Messages ──────────────────────────────────────────
            ['name' => 'Manage Message',                    'module' => 'Messages'],
            ['name' => 'Reply Message',                     'module' => 'Messages'],
            ['name' => 'Delete Message',                    'module' => 'Messages'],

            // ── Media ─────────────────────────────────────────────────────
            ['name' => 'Manage Media',                      'module' => 'Media'],
            ['name' => 'Delete Media',                      'module' => 'Media'],

            // ── Blogs ─────────────────────────────────────────────────────
            ['name' => 'Manage Blog',                       'module' => 'Blogs'],
            ['name' => 'Create New Blog',                   'module' => 'Blogs'],
            ['name' => 'Edit Blog',                         'module' => 'Blogs'],
            ['name' => 'Delete Blog',                       'module' => 'Blogs'],
            ['name' => 'Delete Blog Comment',               'module' => 'Blogs'],

            // ── Blog Categories ───────────────────────────────────────────
            ['name' => 'Manage Blog Category',              'module' => 'Blogs'],
            ['name' => 'Create Blog Category',              'module' => 'Blogs'],
            ['name' => 'Edit Blog Category',                'module' => 'Blogs'],
            ['name' => 'Delete Blog Category',              'module' => 'Blogs'],

            // ── Pages ─────────────────────────────────────────────────────
            ['name' => 'Manage Pages',                      'module' => 'Pages'],
            ['name' => 'Create New Page',                   'module' => 'Pages'],
            ['name' => 'Edit Page',                         'module' => 'Pages'],
            ['name' => 'Delete Page',                       'module' => 'Pages'],

            // ── Appearances ───────────────────────────────────────────────
            ['name' => 'Manage Appearances',                'module' => 'Appearances'],
            ['name' => 'Manage Menu',                       'module' => 'Appearances'],
            ['name' => 'Delete Menu',                       'module' => 'Appearances'],
            ['name' => 'Manage Site Settings',              'module' => 'Appearances'],
            ['name' => 'Manage Home Builder',               'module' => 'Appearances'],

            // ── Language ──────────────────────────────────────────────────
            ['name' => 'Manage Language',                   'module' => 'Language'],
            ['name' => 'Add Language',                      'module' => 'Language'],
            ['name' => 'Edit Language',                     'module' => 'Language'],
            ['name' => 'Delete Language',                   'module' => 'Language'],

            // ── System ────────────────────────────────────────────────────
            ['name' => 'Update Environment',                'module' => 'System'],
            ['name' => 'Update SMTP',                       'module' => 'System'],
            ['name' => 'Manage Social Login',               'module' => 'System'],

            // ── Classified Settings ───────────────────────────────────────
            ['name' => 'Manage General Settings',           'module' => 'Classified Settings'],
            ['name' => 'Manage Currency Settings',          'module' => 'Classified Settings'],
            ['name' => 'Manage Member Settings',            'module' => 'Classified Settings'],
            ['name' => 'Manage Ads Settings',               'module' => 'Classified Settings'],
            ['name' => 'Manage Map Settings',               'module' => 'Classified Settings'],

            ['name' => 'Manage Quick Sell Tips',            'module' => 'Classified Settings'],
            ['name' => 'Create Quick Sell Tip',             'module' => 'Classified Settings'],
            ['name' => 'Edit Quick Sell Tip',               'module' => 'Classified Settings'],
            ['name' => 'Delete Quick Sell Tip',             'module' => 'Classified Settings'],

            ['name' => 'Manage Ad Share Options',           'module' => 'Classified Settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['module' => $perm['module']]
            );
        }

        // ----------------------------
        // 2. Create Super Admin Role (all permissions)
        // ----------------------------
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );
        $superAdminRole->syncPermissions(Permission::all());

        // ----------------------------
        // 3. Create Manager Role (operational, no system/settings)
        // ----------------------------
        $managerRole = Role::firstOrCreate(
            ['name' => 'Manager', 'guard_name' => 'web']
        );
        $managerPermissions = [
            'View Dashboard',
            'User List',
            'Manage Members',
            'Create Member',
            'Edit Member',
            'Delete Member',
            'Manage Ads',
            'Edit Ad',
            'Delete Ad',
            'Manage Ad Categories',
            'Create Ad Category',
            'Edit Ad Category',
            'Delete Ad Category',
            'Manage Conditions',
            'Create Condition',
            'Edit Condition',
            'Delete Condition',
            'Manage Tags',
            'Create Tag',
            'Edit Tag',
            'Delete Tag',
            'Manage Custom Fields',
            'Create Custom Field',
            'Edit Custom Field',
            'Delete Custom Field',
            'Manage Ad Reports',
            'Delete Ad Report',
            'Manage Report Reasons',
            'Create Report Reason',
            'Edit Report Reason',
            'Delete Report Reason',
            'Manage Pricing Plans',
            'Create Pricing Plan',
            'Edit Pricing Plan',
            'Delete Pricing Plan',
            'Manage Subscriptions',
            'Approve Subscription',
            'Reject Subscription',
            'Delete Subscription',
            'Manage Bank Payments',
            'Approve Bank Payment',
            'Reject Bank Payment',
            'Manage Locations',
            'Create Location',
            'Edit Location',
            'Delete Location',
            'Manage Safety Tips',
            'Create Safety Tip',
            'Edit Safety Tip',
            'Delete Safety Tip',
            'Manage Advertisements',
            'Create Advertisement',
            'Edit Advertisement',
            'Delete Advertisement',
            'Manage Newsletter',
            'Create Newsletter Campaign',
            'Edit Newsletter Campaign',
            'Delete Newsletter Campaign',
            'Send Newsletter Campaign',
            'Delete Newsletter Subscriber',
            'Manage Conversations',
            'Manage Message',
            'Reply Message',
            'Delete Message',
            'Manage Media',
            'Delete Media',
            'Manage Blog',
            'Create New Blog',
            'Edit Blog',
            'Delete Blog',
            'Delete Blog Comment',
            'Manage Blog Category',
            'Create Blog Category',
            'Edit Blog Category',
            'Delete Blog Category',
            'Manage Pages',
            'Create New Page',
            'Edit Page',
            'Delete Page',
            'Manage Appearances',
            'Manage Menu',
            'Delete Menu',
            'Manage Site Settings',
            'Manage Home Builder',
        ];
        $managerRole->syncPermissions(Permission::whereIn('name', $managerPermissions)->get());

        // ----------------------------
        // 4. Create Moderator Role (read + limited actions)
        // ----------------------------
        $moderatorRole = Role::firstOrCreate(
            ['name' => 'Moderator', 'guard_name' => 'web']
        );
        $moderatorPermissions = [
            'View Dashboard',
            'Manage Members',
            'Manage Ads',
            'Edit Ad',
            'Delete Ad',
            'Manage Ad Reports',
            'Delete Ad Report',
            'Manage Conversations',
            'Manage Message',
            'Reply Message',
            'Delete Message',
            'Manage Media',
            'Manage Blog',
            'Delete Blog Comment',
            'Manage Blog Category',
        ];
        $moderatorRole->syncPermissions(Permission::whereIn('name', $moderatorPermissions)->get());

        // ----------------------------
        // 5. Create / update default admin user
        // ----------------------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('111111'),
                'type'     => 1,
                'status'   => 1,
            ]
        );

        if (! $admin->hasRole('Super Admin')) {
            $admin->assignRole($superAdminRole);
        }
    }
}
