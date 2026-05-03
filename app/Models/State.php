<?php

namespace App\Models;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state_translations(): HasMany
    {
        return $this->hasMany(StateTranslation::class, 'state_id');
    }

    public function translation(string $field = 'name', $lang = false): string
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->state_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
