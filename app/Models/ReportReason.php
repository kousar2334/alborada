<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportReason extends Model
{
    protected $fillable = ['title', 'status'];

    public function reason_translations(): HasMany
    {
        return $this->hasMany(ReportReasonTranslation::class, 'reason_id');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->reason_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }
}
