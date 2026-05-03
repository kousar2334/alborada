<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportReasonTranslation extends Model
{
    protected $fillable = ['reason_id', 'lang', 'title'];
}
