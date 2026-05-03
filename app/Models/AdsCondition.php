<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdsCondition extends Model
{
    protected $fillable = ['title', 'status'];

    public function condition_translations(): HasMany
    {
        return $this->hasMany(AdsConditionTranslation::class, 'condition_id');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->condition_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
