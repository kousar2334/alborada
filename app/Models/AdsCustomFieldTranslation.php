<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsCustomFieldTranslation extends Model
{
    protected $fillable = ['field_id', 'lang', 'title'];
}
