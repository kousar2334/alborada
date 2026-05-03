<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuPosition extends Model
{
    use HasFactory;

    protected $fillable = ['position_id'];

    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }
}
