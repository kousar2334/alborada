<?php

/**
 * Settings sidebar navigation configuration.
 *
 * Types:
 *   section  – { type, label }
 *   link     – { type, label, icon, route, active_routes[], permission }
 *   group    – { type, label, icon, active_routes[], permission, children[] }
 */

return [

    // ── System ────────────────────────────────────────────────────────────
    ['type' => 'section', 'label' => 'System'],

    [
        'type'          => 'link',
        'label'         => 'Environment',
        'icon'          => 'fas fa-server',
        'route'         => 'admin.system.settings.environment',
        'active_routes' => ['admin.system.settings.environment'],
        'permission'    => 'Update Environment',
    ],

    [
        'type'          => 'link',
        'label'         => 'SMTP',
        'icon'          => 'fas fa-envelope',
        'route'         => 'admin.system.settings.smtp',
        'active_routes' => ['admin.system.settings.smtp'],
        'permission'    => 'Update SMTP',
    ],

    [
        'type'          => 'link',
        'label'         => 'Social Logins',
        'icon'          => 'fas fa-share-alt',
        'route'         => 'admin.system.settings.social.login',
        'active_routes' => ['admin.system.settings.social.login'],
        'permission'    => 'Manage Social Login',
    ],

    // ── Site Settings ─────────────────────────────────────────────────────
    ['type' => 'section', 'label' => 'Site Settings'],

    [
        'type'          => 'link',
        'label'         => 'General',
        'icon'          => 'fas fa-cog',
        'route'         => 'admin.appearance.site.setting',
        'active_routes' => ['admin.appearance.site.setting'],
        'permission'    => 'Manage Site Settings',
    ],

    // ── Content ───────────────────────────────────────────────────────────
    ['type' => 'section', 'label' => 'Content'],

    [
        'type'          => 'link',
        'label'         => 'Footer',
        'icon'          => 'fas fa-layer-group',
        'route'         => 'admin.appearance.site.setting.footer',
        'active_routes' => ['admin.appearance.site.setting.footer'],
        'permission'    => 'Manage Site Settings',
    ],

    [
        'type'          => 'link',
        'label'         => 'SEO Settings',
        'icon'          => 'fas fa-search',
        'route'         => 'admin.appearance.site.setting.seo',
        'active_routes' => ['admin.appearance.site.setting.seo'],
        'permission'    => 'Manage Site Settings',
    ],

    // ── Appearance ────────────────────────────────────────────────────────
    ['type' => 'section', 'label' => 'Appearance'],

    [
        'type'          => 'group',
        'label'         => 'Theme',
        'icon'          => 'fas fa-paint-brush',
        'active_routes' => [
            'admin.appearance.site.setting.colors',
            'admin.appearance.site.setting.custom.css',
        ],
        'permission'    => 'Manage Site Settings',
        'children'      => [
            [
                'label'         => 'Colors Setup',
                'icon'          => 'fas fa-palette',
                'route'         => 'admin.appearance.site.setting.colors',
                'active_routes' => ['admin.appearance.site.setting.colors'],
                'permission'    => 'Manage Site Settings',
            ],
            [
                'label'         => 'Custom CSS',
                'icon'          => 'fas fa-code',
                'route'         => 'admin.appearance.site.setting.custom.css',
                'active_routes' => ['admin.appearance.site.setting.custom.css'],
                'permission'    => 'Manage Site Settings',
            ],
        ],
    ],

];
