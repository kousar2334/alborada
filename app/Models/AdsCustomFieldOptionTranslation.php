<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsCustomFieldOptionTranslation extends Model
{
    protected $fillable = ['option_id', 'lang', 'value'];
}
