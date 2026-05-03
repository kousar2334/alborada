<?php

namespace App\Models;

use App\Models\BlogHasTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogTag extends Model
{
    use HasFactory;


    public function blogs(): HasMany
    {
        return $this->hasMany(BlogHasTag::class, 'tag_id');
    }
}
