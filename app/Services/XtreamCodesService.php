<?php

namespace App\Services;

use App\Contracts\IptvProvider;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class XtreamCodesService implements IptvProvider
{
    protected string $baseUrl;
    protected string $adminUsername;
    protected string $adminPassword;

    public function __construct()
    {
        $this->baseUrl       = rtrim((string) get_setting('xtream_base_url', ''), '/');
        $this->adminUsername = (string) get_setting('xtream_admin_username', '');
        $this->adminPassword = (string) get_setting('xtream_admin_password', '');
    }

    // ── IptvProvider interface ────────────────────────────────────────────────

    public function key(): string
    {
        return 'xtream';
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->adminUsername !== '';
    }

    public function createAccount(UserSubscription $subscription): array
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        $username = $subscription->iptv_username ?: ('alb_' . $user->id . '_' . strtolower(Str::random(4)));
        $password = $subscription->iptv_password ?: Str::random(12);

        $expDate = $subscription->expires_at
            ? $subscription->expires_at->timestamp
            : now()->addDays($plan->duration_days ?? 30)->timestamp;

        $result = $this->createLine([
            'username'               => $username,
            'password'               => $password,
            'max_connections'        => $plan->max_connections ?? 1,
            'allowed_output_formats' => ['ts', 'm3u8'],
            'is_trial'               => $plan->is_trial ? '1' : '0',
            'exp_date'               => $expDate,
        ]);

        $lineId = $result['user_info']['id'] ?? $result['id'] ?? null;

        return [
            'user_id'     => $lineId,
            'username'    => $username,
            'password'    => $password,
            'mac'         => null,
            'm3u_url'     => $this->getM3UUrl($username, $password),
            'device_type' => 'm3u',
        ];
    }

    public function renew(UserSubscription $subscription, int $months): bool
    {
        if (!$subscription->iptv_username) {
            return false;
        }

        return $this->updateLine($subscription->iptv_username, [
            'exp_date' => $subscription->expires_at?->timestamp,
        ]);
    }

    public function enable(UserSubscription $subscription): bool
    {
        return $subscription->iptv_username
            ? $this->unbanLine($subscription->iptv_username)
            : false;
    }

    public function disable(UserSubscription $subscription): bool
    {
        return $subscription->iptv_username
            ? $this->banLine($subscription->iptv_username)
            : false;
    }

    public function fetchInfo(UserSubscription $subscription): array
    {
        if (!$subscription->iptv_username) {
            return [];
        }

        $info = $this->getLineInfo($subscription->iptv_username);
        if (empty($info)) {
            return [];
        }

        return [
            'active'     => ($info['user_info']['active'] ?? 1) == 1,
            'expires_at' => isset($info['user_info']['exp_date'])
                ? Carbon::createFromTimestamp($info['user_info']['exp_date'])
                : null,
        ];
    }

    public function deleteAccount(UserSubscription $subscription): bool
    {
        return $subscription->iptv_username
            ? $this->deleteLine($subscription->iptv_username)
            : true;
    }

    // ── Low-level Xtream Codes reseller API ───────────────────────────────────

    public function createLine(array $params): array
    {
        return $this->request('add_user', $params);
    }

    public function updateLine(string $username, array $params): bool
    {
        $result = $this->request('edit_user', array_merge(['username' => $username], $params));
        return ($result['user_info']['result'] ?? '') === 'success' || isset($result['result']);
    }

    public function deleteLine(string $username): bool
    {
        $result = $this->request('delete_user', ['username' => $username]);
        return isset($result['result']) || empty($result);
    }

    public function getLineInfo(string $username): array
    {
        return $this->request('get_user_info', ['username' => $username]);
    }

    public function banLine(string $username): bool
    {
        return $this->updateLine($username, ['status' => 0]);
    }

    public function unbanLine(string $username): bool
    {
        return $this->updateLine($username, ['status' => 1]);
    }

    public function getM3UUrl(string $username, string $password): string
    {
        return $this->baseUrl . '/get.php?username=' . urlencode($username)
            . '&password=' . urlencode($password)
            . '&type=m3u_plus&output=ts';
    }

    public function getEpgUrl(string $username, string $password): string
    {
        return $this->baseUrl . '/xmltv.php?username=' . urlencode($username)
            . '&password=' . urlencode($password);
    }

    protected function request(string $action, array $params = []): array
    {
        if (empty($this->baseUrl)) {
            return [];
        }

        $start = microtime(true);
        $statusCode = 500;
        $response = [];

        try {
            $result = Http::timeout(15)->post($this->baseUrl . '/api.php', array_merge([
                'username' => $this->adminUsername,
                'password' => $this->adminPassword,
                'action'   => $action,
            ], $params));

            $statusCode = $result->status();
            $response   = $result->json() ?? [];
        } catch (\Exception $e) {
            $response = ['error' => $e->getMessage()];
        }

        $this->logRequest('xtream:' . $action, $params, $response, $statusCode, (int)((microtime(true) - $start) * 1000));

        return $response;
    }

    private function logRequest(string $endpoint, array $request, array $response, int $status, int $duration): void
    {
        try {
            \App\Models\ApiLog::create([
                'endpoint'         => $endpoint,
                'method'           => 'POST',
                'request_payload'  => $request,
                'response_payload' => $response,
                'status_code'      => $status,
                'ip_address'       => request()->ip() ?? '127.0.0.1',
                'duration_ms'      => $duration,
            ]);
        } catch (\Exception) {
            // fail silently — logging must not break the main flow
        }
    }
}
