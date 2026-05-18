<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaContent extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'thumbnail',
        'trailer_url',
        'type',
        'genre',
        'release_year',
        'seasons',
        'episodes',
        'cast',
        'rating',
        'badge_text',
        'is_active',
        'featured_on_home',
        'sort_order',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'featured_on_home' => 'boolean',
        'rating'           => 'decimal:1',
    ];

    public function getYoutubeEmbedIdAttribute(): ?string
    {
        if (!$this->trailer_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->trailer_url, $m)) {
            return $m[1];
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $this->trailer_url)) {
            return $this->trailer_url;
        }

        return null;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'movie'   => 'Movie',
            'tv_show' => 'TV Show',
            default   => 'Content',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'movie'   => 'fa-film',
            'tv_show' => 'fa-tv',
            default   => 'fa-play',
        };
    }

    public function getRatingStarsAttribute(): string
    {
        if (!$this->rating) {
            return '';
        }
        $full  = floor($this->rating / 2);
        $half  = ($this->rating / 2 - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;
        return str_repeat('★', $full) . str_repeat('½', $half) . str_repeat('☆', $empty);
    }
}
