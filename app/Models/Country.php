<?php

namespace App\Models;

use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id', 'id');
    }

    public function country_translations(): HasMany
    {
        return $this->hasMany(CountryTranslation::class, 'country_id');
    }

    public function translation(string $field = 'name', $lang = false): string
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->country_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
