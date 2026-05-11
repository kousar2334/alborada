<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Ad;
use App\Models\UserSubscription;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'type',
        'status',
        'reseller_id',
        'social_provider',
        'social_id',
        'credits',
        'markup_percentage',
        'xtream_username',
        'xtream_password',
        'whmcs_client_id',
        'stripe_customer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class, 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'user_id');
    }

    public function resellerClients(): HasMany
    {
        return $this->hasMany(User::class, 'reseller_id');
    }

    public function isReseller(): bool
    {
        return $this->type === config('settings.user_type.reseller');
    }

    public function isCustomer(): bool
    {
        return $this->type === config('settings.user_type.customer');
    }

    public function creditLogs(): HasMany
    {
        return $this->hasMany(ResellerCreditLog::class, 'reseller_id');
    }

    public function deductCredits(float $amount, string $description, ?int $clientUserId = null): bool
    {
        if ($this->credits < $amount) {
            return false;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $description, $clientUserId) {
            $this->decrement('credits', $amount);
            ResellerCreditLog::create([
                'reseller_id'  => $this->id,
                'user_id'      => $clientUserId,
                'type'         => 'debit',
                'amount'       => $amount,
                'balance_after' => $this->credits,
                'description'  => $description,
            ]);
        });

        return true;
    }

    public function addCredits(float $amount, string $description): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $description) {
            $this->increment('credits', $amount);
            ResellerCreditLog::create([
                'reseller_id'  => $this->id,
                'user_id'      => null,
                'type'         => 'credit',
                'amount'       => $amount,
                'balance_after' => $this->credits + $amount,
                'description'  => $description,
            ]);
        });
    }
}
