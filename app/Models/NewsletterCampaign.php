<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterCampaign extends Model
{
    protected $fillable = ['subject', 'content', 'status', 'total_sent', 'total_opened', 'total_clicked', 'sent_at'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function stats(): HasMany
    {
        return $this->hasMany(NewsletterCampaignStat::class, 'campaign_id');
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->total_sent === 0) return 0;
        return round(($this->total_opened / $this->total_sent) * 100, 1);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->total_sent === 0) return 0;
        return round(($this->total_clicked / $this->total_sent) * 100, 1);
    }
}
