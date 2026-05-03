<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageContentTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'page_id',
        'key',
        'lang',
        'value',
    ];
}
