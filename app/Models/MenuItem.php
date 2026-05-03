<?php

namespace App\Models;

use App\Models\MenuItemTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItem extends Model
{
    use HasFactory;

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $menu_translations = $this->menu_translations->where('lang', $lang)->first();
        return $menu_translations != null ? $menu_translations->$field : $this->$field;
    }

    public function menu_translations()
    {
        return $this->hasMany(MenuItemTranslation::class, 'item_id');
    }


    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent')->with(['children', 'linkable', 'menu_translations'])->orderBy('position', 'ASC');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function link()
    {
        if ($this->linkable_type == null) {
            return $this->link;
        }
        $site_url = $url = url('/');
        $linkable = $this->linkable;
        if ($linkable instanceof \App\Models\Page) {
            $page_permalink = $linkable->permalink;
            return $site_url . '/' . $page_permalink;
        } elseif ($linkable instanceof \App\Models\ProductCategory) {
            $category_id = $linkable->id;
            return route('products.page', ['category_id' => $category_id]);
        } else {
            return $site_url;
        }
    }
}
