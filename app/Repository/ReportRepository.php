<?php

namespace App\Repository;

use Plugin\Payment\Models\PaymentTransaction;

class ReportRepository
{
    /**
     * Will return admin business stats
     */
    public function businessStats(string $time = null): array
    {

        if ($time == 'over_all' || $time == null) {
            $total_member = \Core\Models\User::where('user_type', config('settings.user_type.member'))->count();
            $total_payments = PaymentTransaction::sum('paid_amount');
        }
        if ($time == 'today') {
            $total_member = \Core\Models\User::where('user_type', config('settings.user_type.member'))->whereDate('created_at', today())->count();
            $total_payments = PaymentTransaction::where('status', config('settings.general_status.in_active'))->whereDate('created_at', today())->sum('paid_amount');
        }
        if ($time == 'month') {
            $total_member = \Core\Models\User::where('user_type', config('settings.user_type.member'))->whereMonth('created_at', '=', now()->month)->count();
            $total_payments = PaymentTransaction::where('status', config('settings.general_status.in_active'))->whereMonth('created_at', '=', now()->month)->sum('paid_amount');
        }

        return [
            'total_member' => $total_member,
            'total_payments' => currencyExchange($total_payments, true, null, false),
        ];
    }
}
