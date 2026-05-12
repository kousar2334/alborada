<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDownloaderCode extends Model
{
    protected $fillable = [
        'label',
        'code',
        'device_type',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function deviceTypeLabel(string $type): string
    {
        return match ($type) {
            'firestick' => 'Amazon Firestick',
            'android'   => 'Android TV / Box',
            'ios'       => 'iPhone / iPad',
            'smart_tv'  => 'Smart TV',
            'desktop'   => 'Windows / Mac',
            default     => 'Other Device',
        };
    }

    public static function deviceTypeIcon(string $type): string
    {
        return match ($type) {
            'firestick' => 'fa-fire',
            'android'   => 'fa-robot',
            'ios'       => 'fa-apple',
            'smart_tv'  => 'fa-tv',
            'desktop'   => 'fa-desktop',
            default     => 'fa-mobile-screen',
        };
    }
}
