<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportTicket extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_NEW         = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_CLOSED      = 3;
    const STATUS_RE_OPEN     = 4;

    protected $fillable = [
        'user_id', 'ticket_number', 'subject', 'priority',
        'status', 'department', 'assigned_to', 'first_reply_at', 'closed_at',
    ];

    protected $casts = [
        'first_reply_at' => 'datetime',
        'closed_at'      => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $ticket) {
            if (empty($ticket->ticket_number)) {
                $next = (static::max('id') ?? 0) + 1;
                $ticket->ticket_number = 'TKT-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->oldest();
    }

    public function latestReply(): HasOne
    {
        return $this->hasOne(TicketReply::class, 'ticket_id')->latestOfMany();
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_NEW         => 'New',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_CLOSED      => 'Closed',
            self::STATUS_RE_OPEN     => 'Re-opened',
            default                  => 'Unknown',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_NEW         => 'badge-primary',
            self::STATUS_IN_PROGRESS => 'badge-warning',
            self::STATUS_CLOSED      => 'badge-secondary',
            self::STATUS_RE_OPEN     => 'badge-info',
            default                  => 'badge-light',
        };
    }
}
