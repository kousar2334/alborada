<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingPlanTranslation extends Model
{
    protected $fillable = ['plan_id', 'lang', 'title'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'plan_id');
    }
}
