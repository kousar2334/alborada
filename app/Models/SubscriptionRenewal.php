<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single renewal of a subscription — admin-initiated or paid for by the
 * customer. The subscription row itself only carries the current period, so the
 * audit trail of who renewed, how, and for which period lives here.
 */
class SubscriptionRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'plan_id',
        'transaction_id',
        'amount',
        'days',
        'months',
        'payment_method',
        'status',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_mode',
        'bank_transaction_number',
        'bank_slip',
        'invoice_id',
        'processed_by',
        'admin_note',
        'previous_expires_at',
        'new_expires_at',
        'paid_at',
    ];

    protected $casts = [
        'amount'              => 'float',
        'days'                => 'integer',
        'months'              => 'integer',
        'previous_expires_at' => 'datetime',
        'new_expires_at'      => 'datetime',
        'paid_at'             => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'plan_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
