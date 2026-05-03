<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdReport extends Model
{
    protected $fillable = ['ad_id', 'user_id', 'reason_id', 'message', 'status'];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class, 'ad_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(ReportReason::class, 'reason_id');
    }
}
