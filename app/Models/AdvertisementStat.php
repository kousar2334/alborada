<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementStat extends Model
{
    protected $fillable = ['advertisement_id', 'date', 'impressions', 'clicks'];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}
