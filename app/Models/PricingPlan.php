<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration_days',
        'price',
        'listing_quantity',
        'featured_listing_quantity',
        'gallery_image_quantity',
        'membership_badge',
        'status',
    ];

    protected $casts = [
        'price'             => 'float',
        'membership_badge'  => 'boolean',
    ];

    public function pricing_plan_translations(): HasMany
    {
        return $this->hasMany(PricingPlanTranslation::class, 'plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->pricing_plan_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
