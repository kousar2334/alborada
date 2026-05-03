<?php

namespace App\Models;

use App\Models\User;
use App\Models\BlogTag;
use App\Models\BlogHasTag;
use App\Models\BlogComment;
use App\Models\BlogCategory;
use App\Models\BlogTranslation;
use App\Models\BlogHasCategories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Blog extends Model
{
    use HasFactory;

    public function authorInfo(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $blog_translations = $this->blog_translations->where('lang', $lang)->first();
        return $blog_translations != null ? $blog_translations->$field : $this->$field;
    }

    public function blog_translations()
    {
        return $this->hasMany(BlogTranslation::class, 'blog_id');
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(BlogCategory::class, BlogHasCategories::class, 'blog_id', 'id', 'id', 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_id')
            ->with(['commentAuthor' => function ($q) {
                $q->select(['id', 'name', 'email', 'image']);
            }])->orderBy('id', 'DESC');
    }

    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(BlogTag::class, BlogHasTag::class, 'blog_id', 'id', 'id', 'tag_id');
    }
}
