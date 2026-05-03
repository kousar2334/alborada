<?php

namespace App\Models;

use App\Models\BlogCategoryTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogCategory extends Model
{
    use HasFactory;

    public function parentCat(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent');
    }

    public function child(): HasMany
    {
        return $this->hasMany(self::class, 'parent');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $blog_category_translations = $this->blog_category_translations->where('lang', $lang)->first();
        return $blog_category_translations != null ? $blog_category_translations->$field : $this->$field;
    }

    public function blog_category_translations(): HasMany
    {
        return $this->hasMany(BlogCategoryTranslation::class, 'category_id');
    }
}
