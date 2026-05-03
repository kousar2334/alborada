<?php

namespace App\Models;

use App\Models\AdsCustomField;
use App\Models\AdsCustomFieldOptionTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdsCustomFieldOption extends Model
{
    protected $fillable = ['field_id', 'value', 'status'];

    public function field(): HasOne
    {
        return $this->hasOne(AdsCustomField::class, 'id', 'field_id');
    }

    public function option_translations(): HasMany
    {
        return $this->hasMany(AdsCustomFieldOptionTranslation::class, 'option_id');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->option_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
