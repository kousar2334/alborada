<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdsCategory extends Model
{
    public function parentCategory()
    {
        return $this->belongsTo(self::class, 'parent')->select(['id', 'title']);
    }

    public function child()
    {
        return $this->hasMany($this, 'parent')->orderBy('id', 'ASC');
    }

    public function ads()
    {
        return $this->hasMany(Ad::class, 'category_id');
    }

    public function ads_category_translations(): HasMany
    {
        return $this->hasMany(AdsCategoryTranslation::class, 'category_id');
    }

    public function translation(string $field, $lang = false)
    {
        $lang = $lang == false ?  session()->get('locale') : $lang;
        $translation = $this->ads_category_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
