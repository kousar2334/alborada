<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyTipTranslation extends Model
{
    protected $fillable = ['tips_id', 'lang', 'title'];
}
