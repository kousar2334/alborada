<?php
return [
    'general_status' => [
        'active'    => 1,
        'in_active' => 0,
    ],
    'user_type' => [
        'admin'   => 1,
        'member' => 2,
    ],
    'blog_status' => [
        'publish'   => 1,
        'unpublish' => 0,
        'draft'     => 2
    ],
    'cause_status' => [
        'publish'   => 1,
        'unpublish' => 0,
        'draft'     => 2
    ],

    'event_status' => [
        'publish'   => 1,
        'unpublish' => 0,
        'draft'     => 2
    ],
    'page_status' => [
        'active'    => 1,
        'in_active' => 0,
    ],
    'page_build_with' => [
        'builder' => 1,
        'editor'  => 0,
    ],
    'menu_position' => [
        'header'    => 1,
        'footer'    => 2,
    ],
    'menu_item_type' => [
        'custom'       => 1,
        'page'         => 2,
        'product'      => 4,
        'category'     => 5,
    ],
    'ticket_status' => [
        'new'         => 1,
        'progressing' => 2,
        'closed'      => 3,
        're_open'      => 4,
    ],
    'currency_position' => [
        2 => "[Amount][Currency]",
        1 => "[Currency][Amount]",
    ],
    'image_cropping_sizes' => [
        '266x242',
        '92x92',
        '416x267'
    ],
    'page_list' => [
        'home' => 'home',
        'about'  => 'about',
        'contact' => 'contact',
        'dealer' => 'dealer',
        'parts' => 'parts',
        'case' => 'case',
        'fleet' => 'fleet',
        'brochures' => 'brochures',
    ],
    'input_types' => [
        'text' => 1,
        'number' => 2,
        'select' => 3,
        'text_area' => 4,
        'checkbox' => 5,
        'radio' => 6,
        'file' => 7,
        'date' => 8,
    ],
];
