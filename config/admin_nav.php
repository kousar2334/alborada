<?php

return [

    [
        'label'        => 'Dashboard',
        'route'        => 'admin.dashboard',
        'icon'         => 'fas fa-tachometer-alt',
        'permission'   => 'View Dashboard',
        'active_routes' => ['admin.dashboard'],
    ],

    [
        'label'        => 'Members',
        'route'        => 'admin.members.list',
        'icon'         => 'fas fa-users',
        'permission'   => 'Manage Members',
        'active_routes' => ['admin.members.list'],
    ],
    [
        'label'        => 'Subscriptions',
        'route'        => 'admin.subscriptions.list',
        'icon'         => 'fas fa-crown',
        'active_routes' => ['admin.subscriptions.list'],
    ],
    [
        'label'        => 'Pricing Plans',
        'route'        => 'admin.pricing.plans.list',
        'icon'         => 'fas fa-tags',
        'active_routes' => ['admin.pricing.plans.list'],
    ],
    [
        'label'        => 'Resellers',
        'route'        => 'admin.resellers.index',
        'icon'         => 'fas fa-store',
        'active_routes' => ['admin.resellers.*'],
    ],
    [
        'label'        => 'Support Tickets',
        'route'        => 'admin.tickets.index',
        'icon'         => 'fas fa-ticket-alt',
        'active_routes' => ['admin.tickets.*'],
    ],

    [
        'label'        => 'Reports',
        'route'        => 'admin.reports.index',
        'icon'         => 'fas fa-chart-bar',
        'active_routes' => ['admin.reports.*'],
    ],

    [
        'label'        => 'API Logs',
        'route'        => 'admin.api.logs',
        'icon'         => 'fas fa-stream',
        'active_routes' => ['admin.api.logs'],
    ],

    [
        'label'        => 'Contents',
        'route'        => 'admin.featured-content.index',
        'icon'         => 'fas fa-film',
        'active_routes' => ['admin.featured-content.*'],
    ],

    [
        'label'        => 'Channels',
        'route'        => 'admin.channels.index',
        'icon'         => 'fas fa-tv',
        'active_routes' => ['admin.channels.*'],
    ],

    [
        'label'        => 'Media',
        'route'        => 'admin.media.list',
        'icon'         => 'fas fa-photo-video',
        'permission'   => 'Manage Media',
        'active_routes' => ['admin.media.list'],
    ],

    [
        'label'        => 'Blogs',
        'icon'         => 'fas fa-blog',
        'permission'   => 'Manage Blog',
        'active_routes' => [
            'admin.blogs.categories.edit',
            'admin.blogs.comment.list',
            'admin.blogs.edit',
            'admin.blogs.list',
            'admin.blogs.create',
            'admin.blogs.categories.list',
        ],
        'children' => [
            [
                'label'        => 'Write New Blog',
                'route'        => 'admin.blogs.create',
                'icon'         => 'far fa-circle',
                'permission'   => 'Create New Blog',
                'active_routes' => ['admin.blogs.create'],
            ],
            [
                'label'        => 'All Blogs',
                'route'        => 'admin.blogs.list',
                'icon'         => 'far fa-circle',
                'active_routes' => ['admin.blogs.list'],
            ],
            [
                'label'        => 'Categories',
                'route'        => 'admin.blogs.categories.list',
                'icon'         => 'far fa-circle',
                'permission'   => 'Manage Blog Category',
                'active_routes' => ['admin.blogs.categories.edit', 'admin.blogs.categories.list'],
            ],
        ],
    ],

    [
        'label'        => 'Pages',
        'icon'         => 'fas fa-file',
        'permission'   => 'Manage Pages',
        'active_routes' => ['admin.page.edit', 'admin.page.list', 'admin.page.create'],
        'children' => [
            [
                'label'        => 'All Page',
                'route'        => 'admin.page.list',
                'icon'         => 'fa fa-minus',
                'active_routes' => ['admin.page.list'],
            ],
            [
                'label'        => 'Create New Page',
                'route'        => 'admin.page.create',
                'icon'         => 'fa fa-minus',
                'permission'   => 'Create New Page',
                'active_routes' => ['admin.page.create'],
            ],
        ],
    ],

    [
        'label'        => 'Appearances',
        'icon'         => 'fas fa-desktop',
        'permission'   => 'Manage Appearances',
        'active_routes' => [
            'admin.home.builder',
            'admin.appearance.menu.builder',
        ],
        'children' => [
            [
                'label'        => 'Menus',
                'route'        => 'admin.appearance.menu.builder',
                'icon'         => 'fa fa-minus',
                'permission'   => 'Manage Menu',
                'active_routes' => ['admin.appearance.menu.builder'],
            ],
            [
                'label'        => 'Home Builder',
                'route'        => 'admin.home.builder',
                'icon'         => 'fa fa-minus',
                'permission'   => 'Manage Home Builder',
                'active_routes' => ['admin.home.builder'],
            ],
        ],
    ],

    [
        'label'           => 'Users',
        'icon'            => 'fas fa-users-cog',
        'any_permissions' => ['User List', 'Role List View', 'Permission List View'],
        'active_routes'   => ['admin.users.list', 'admin.users.permission.list', 'admin.users.role.list'],
        'children' => [
            [
                'label'        => 'Users',
                'route'        => 'admin.users.list',
                'icon'         => 'fa fa-minus',
                'permission'   => 'User List',
                'active_routes' => ['admin.users.list'],
            ],
            [
                'label'        => 'Roles',
                'route'        => 'admin.users.role.list',
                'icon'         => 'fa fa-minus',
                'permission'   => 'Role List View',
                'active_routes' => ['admin.users.role.list'],
            ],
            [
                'label'        => 'Permissions',
                'route'        => 'admin.users.permission.list',
                'icon'         => 'fa fa-minus',
                'permission'   => 'Permission List View',
                'active_routes' => ['admin.users.permission.list'],
            ],
        ],
    ],

    [
        'label'           => 'Settings',
        'route'           => 'admin.appearance.site.setting',
        'icon'            => 'fas fa-cog',
        'any_permissions' => ['Update Environment', 'Update SMTP', 'Manage Social Login', 'Manage Site Settings', 'Manage Language'],
        'active_routes'   => [
            'admin.system.settings.environment',
            'admin.system.settings.smtp',
            'admin.system.settings.social.login',
            'admin.appearance.site.setting',
            'admin.appearance.site.setting.*',
            'admin.system.settings.language.list',
            'admin.system.settings.language.translation',
            'admin.payment.settings',
            'admin.system.settings.iptv',
            'admin.downloader-codes.*',
            'admin.settings.chat-widget',
        ],
    ],

];
