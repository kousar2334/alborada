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
        'offer_price',
        'status',
        'max_connections',
        'streaming_quality',
        'catchup_days',
        'dvr_enabled',
        'is_trial',
        'trial_days',
        'sort_order',
        'iptv_package_id',
        'iptv_sub_months',
        'iptv_device_type',
        'iptv_country',
    ];

    protected $casts = [
        'price'             => 'float',
        'offer_price'       => 'float',
        'dvr_enabled'       => 'boolean',
        'is_trial'          => 'boolean',
    ];

    /**
     * True when a promotional offer price lower than the regular price is set.
     */
    public function getHasOfferAttribute(): bool
    {
        return $this->offer_price !== null
            && $this->offer_price > 0
            && $this->offer_price < $this->price;
    }

    /**
     * The price the customer actually pays — the offer price when a valid
     * promotion is set, otherwise the regular price.
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->has_offer ? $this->offer_price : $this->price;
    }

    /**
     * Subscription length in months for providers that bill in months
     * (8K accepts 1/3/6/12). Falls back to deriving it from duration_days.
     */
    public function iptvMonths(): int
    {
        $months = (int) ($this->iptv_sub_months ?? 0);
        if (in_array($months, [1, 3, 6, 12], true)) {
            return $months;
        }

        $derived = (int) round(($this->duration_days ?? 30) / 30);
        return max(1, $derived);
    }

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
