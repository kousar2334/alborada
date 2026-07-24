<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'transaction_id',
        'amount',
        'payment_method',
        'status',
        'bank_transaction_number',
        'bank_slip',
        'ssl_session_key',
        'ssl_val_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'xtream_username',
        'xtream_password',
        'xtream_line_id',
        'iptv_provider',
        'iptv_user_id',
        'iptv_username',
        'iptv_password',
        'iptv_mac',
        'iptv_m3u_url',
        'iptv_device_type',
        'invoice_id',
        'whmcs_order_id',
        'whmcs_service_id',
        'whmcs_invoice_id',
        'auto_renew',
        'renewal_reminder_sent',
        'expiry_alert_sent',
        'admin_note',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'amount'                 => 'float',
        'starts_at'              => 'datetime',
        'expires_at'             => 'datetime',
        'auto_renew'             => 'boolean',
        'renewal_reminder_sent'  => 'boolean',
        'expiry_alert_sent'      => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'plan_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}
