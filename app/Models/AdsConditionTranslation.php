<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsConditionTranslation extends Model
{
    protected $fillable = ['condition_id', 'lang', 'title'];
}
