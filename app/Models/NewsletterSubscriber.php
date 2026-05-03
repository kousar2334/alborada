<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'status', 'token', 'unsubscribed_at'];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
    ];

    public function campaignStats(): HasMany
    {
        return $this->hasMany(NewsletterCampaignStat::class, 'subscriber_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
