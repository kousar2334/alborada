<?php

namespace App\Models;

use App\Models\PageContentTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageContent extends Model
{
    use HasFactory;

    protected $fillable = ['page_id', 'key', 'value'];

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? getLocale() : $lang;
        $page_content_translations = $this->page_content_translations->where('lang', $lang)->first();
        return $page_content_translations != null ? $page_content_translations->$field : $this->$field;
    }

    public function page_content_translations(): HasMany
    {
        return $this->hasMany(PageContentTranslation::class, 'page_content_id');
    }
}
