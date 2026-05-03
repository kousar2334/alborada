<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SafetyTips extends Model
{
    protected $fillable = ['title', 'status'];

    public function tip_translations(): HasMany
    {
        return $this->hasMany(SafetyTipTranslation::class, 'tips_id');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $tip_translation = $this->tip_translations->where('lang', $lang)->first();
        return $tip_translation != null ? $tip_translation->$field : $this->$field;
    }
}
