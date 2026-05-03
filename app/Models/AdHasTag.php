<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdHasTag extends Model
{

    protected $fillable = [
        'ad_id',
        'tag_id',
    ];
}
