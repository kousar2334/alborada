<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateTranslation extends Model
{
    protected $fillable = ['state_id', 'lang', 'name'];
}
