<?php

namespace App\Models;

use App\Models\User;
use App\Models\PageTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

    public function authorInfo(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $page_translations = $this->page_translations->where('lang', $lang)->first();
        return $page_translations != null ? $page_translations->$field : $this->$field;
    }

    public function page_translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class, 'page_id');
    }
}
