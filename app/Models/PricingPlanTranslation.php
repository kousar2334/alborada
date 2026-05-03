<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlanTranslation extends Model
{
    protected $fillable = ['plan_id', 'lang', 'title'];
}
