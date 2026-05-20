<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedContent extends Model
{
    protected $table = 'featured_content';

    protected $fillable = [
        'title',
        'subtitle',
        'thumbnail',
        'trailer_url',
        'type',
        'genre',
        'release_year',
        'rating',
        'event_date',
        'badge_text',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'event_date'   => 'date',
        'release_year' => 'integer',
        'rating'       => 'float',
    ];

    public function getYoutubeEmbedIdAttribute(): ?string
    {
        if (!$this->trailer_url) {
            return null;
        }

        // Accept full YouTube URL or embed ID
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->trailer_url, $m)) {
            return $m[1];
        }

        // Raw 11-char ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $this->trailer_url)) {
            return $this->trailer_url;
        }

        return null;
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'movie'        => 'fa-film',
            'series'       => 'fa-clapperboard',
            'sports_event' => 'fa-trophy',
            'new_release'  => 'fa-star',
            default        => 'fa-play',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'movie'        => 'Movie',
            'series'       => 'Series',
            'sports_event' => 'Live Event',
            'new_release'  => 'New Release',
            default        => 'Content',
        };
    }
}
