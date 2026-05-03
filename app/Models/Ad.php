<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\AdsCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ad extends Model
{
    protected $fillable = ['uid'];

    public function cityInfo(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function stateInfo(): HasOne
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }
    public function countryInfo(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(AdGalleryImage::class, 'ad_id');
    }
    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(AdsTag::class, AdHasTag::class, 'ad_id', 'id', 'id', 'tag_id');
    }

    public function categoryInfo(): HasOne
    {
        return $this->hasOne(AdsCategory::class, 'id', 'category_id');
    }

    public function userInfo(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function condition(): HasOne
    {
        return $this->hasOne(AdsCondition::class, 'id', 'condition_id');
    }

    function location()
    {
        return (!is_null($this->cityInfo) && !is_null($this->stateInfo) && !is_null($this->countryInfo)) ? $this->cityInfo->name . ', ' . $this->stateInfo->name . ', ' . $this->countryInfo->name : null;
    }
    public function customFields()
    {
        return json_decode($this->custom_field, true);
    }

    public function isPaymentPending()
    {
        if ($this->cost > 0 && $this->payment_status != config('settings.general_status.active')) {
            return config('settings.general_status.active');
        }

        return config('settings.general_status.in_active');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uid = Str::uuid()->toString();
        });
    }
}
