<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedAd extends Model
{
    protected $fillable = ['user_id', 'ad_id'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
