<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'position',
        'type',
        'image_path',
        'click_url',
        'html_code',
        'start_date',
        'end_date',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function stats()
    {
        return $this->hasMany(AdvertisementStat::class);
    }

    public function totalImpressions(): int
    {
        return (int) $this->stats()->sum('impressions');
    }

    public function totalClicks(): int
    {
        return (int) $this->stats()->sum('clicks');
    }

    /**
     * Scope: only active and within date range
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();

        return $query->where('status', 1)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            });
    }

    /**
     * Scope: filter by position
     */
    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
