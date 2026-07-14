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

    /**
     * True when the page is built in design mode: it is rendered full-width with
     * no breadcrumb header or narrow column, so the admin's own section markup
     * controls the whole layout.
     */
    public function getIsDesignModeAttribute(): bool
    {
        return (int) $this->build_with === (int) config('settings.page_build_with.builder');
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
