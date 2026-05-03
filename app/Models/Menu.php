<?php

namespace App\Models;

use App\Models\MenuItem;
use App\Models\MenuPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->orderBy('position', 'ASC');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(MenuPosition::class, 'id', 'menu_id');
    }
}
