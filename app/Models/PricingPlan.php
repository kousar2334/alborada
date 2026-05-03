<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function pricing_plan_translations(): HasMany
    {
        return $this->hasMany(PricingPlanTranslation::class, 'plan_id');
    }

    public function translation(string $field, $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->pricing_plan_translations->where('lang', $lang)->first();
        return ($translation && $translation->$field) ? $translation->$field : $this->$field;
    }
}
