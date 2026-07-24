<?php

namespace App\Contracts;

use App\Models\UserSubscription;

/**
 * A pluggable IPTV streaming provider (Xtream Codes, 8K CMS, …).
 *
 * All methods are normalised around a UserSubscription so the provisioning
 * layer never has to know which panel it is talking to. The active provider is
 * chosen at runtime from the `active_iptv_provider` setting — see
 * App\Providers\AppServiceProvider.
 */
interface IptvProvider
{
    /**
     * Machine key used in settings and stored on the subscription
     * (`iptv_provider`). e.g. 'xtream' or '8k'.
     */
    public function key(): string;

    /**
     * Whether the provider has enough configuration to make API calls.
     */
    public function isConfigured(): bool;

    /**
     * Create a streaming account for the subscription.
     *
     * @return array{
     *     user_id: string|null,
     *     username: string|null,
     *     password: string|null,
     *     mac: string|null,
     *     m3u_url: string|null,
     *     device_type: string
     * } Empty array on failure.
     */
    public function createAccount(UserSubscription $subscription): array;

    /**
     * Extend the account by the given number of months.
     */
    public function renew(UserSubscription $subscription, int $months): bool;

    /**
     * Re-enable a previously disabled account.
     */
    public function enable(UserSubscription $subscription): bool;

    /**
     * Disable (suspend) an account.
     */
    public function disable(UserSubscription $subscription): bool;

    /**
     * Fetch the current remote state of the account.
     *
     * @return array{active?: bool, expires_at?: \Carbon\Carbon|null}
     */
    public function fetchInfo(UserSubscription $subscription): array;

    /**
     * Tear down the account. Providers without a delete endpoint should
     * disable instead.
     */
    public function deleteAccount(UserSubscription $subscription): bool;
}
