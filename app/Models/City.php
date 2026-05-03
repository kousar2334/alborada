<?php

namespace App\Models;

use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city_translations(): HasMany
    {
        return $this->hasMany(CityTranslation::class, 'city_id');
    }

    public function translation(string $field = 'name', $lang = false): string
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->city_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
