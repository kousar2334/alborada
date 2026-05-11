<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subscription_id', 'invoice_number',
        'amount', 'tax_amount', 'total_amount',
        'status', 'due_date', 'paid_at', 'pdf_path', 'notes',
    ];

    protected $casts = [
        'paid_at'  => 'datetime',
        'due_date' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $invoice) {
            if (empty($invoice->invoice_number)) {
                $next = (static::max('id') ?? 0) + 1;
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }
}
