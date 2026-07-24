<?php

namespace App\Services;

use App\Contracts\IptvProvider;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * 8K CMS reseller API client.
 *
 * Docs: https://8k.cms-only.ru/api/panel_api.html
 * Auth is a single api_key query parameter. Actions: new, renew,
 * device_status, bouquet, device_info, reseller. `sub` is a number of months
 * (1/3/6/12). M3U account creation does NOT accept a username/password — the
 * panel generates them and returns them inside the response `url`.
 */
class EightKApiService implements IptvProvider
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = (string) get_setting('iptv_api_url', 'https://8k.cms-only.ru/api/api.php');
        $this->apiKey = (string) get_setting('iptv_api_key', '');
    }

    // ── IptvProvider interface ────────────────────────────────────────────────

    public function key(): string
    {
        return '8k';
    }

    public function isConfigured(): bool
    {
        return $this->apiUrl !== '' && $this->apiKey !== '';
    }

    public function createAccount(UserSubscription $subscription): array
    {
        $plan    = $subscription->plan;
        $months  = $plan->iptvMonths();
        $pack    = (string) ($plan->iptv_package_id ?? '');
        $country = (string) ($plan->iptv_country ?? 'ALL');
        $type    = $plan->iptv_device_type === 'mag' ? 'mag' : 'm3u';
        $notes   = 'Order ' . ($subscription->transaction_id ?? ('#' . $subscription->id));

        if ($type === 'mag') {
            $response = $this->createMag((string) $subscription->iptv_mac, $months, $pack, $country, $notes);
        } else {
            $response = $this->createM3U($months, $pack, $country, $notes);
        }

        if (($response['status'] ?? '') !== 'true') {
            return [];
        }

        $url  = $response['url'] ?? '';
        $creds = $type === 'm3u' ? self::parseM3UCredentials($url) : ['username' => null, 'password' => null];

        return [
            'user_id'     => $response['user_id'] ?? null,
            'username'    => $creds['username'],
            'password'    => $creds['password'],
            'mac'         => $response['mac'] ?? ($type === 'mag' ? $subscription->iptv_mac : null),
            'm3u_url'     => $url,
            'device_type' => $type,
        ];
    }

    public function renew(UserSubscription $subscription, int $months): bool
    {
        $type = $subscription->iptv_device_type === 'mag' ? 'mag' : 'm3u';

        if ($type === 'mag') {
            $response = $this->renewMag((string) $subscription->iptv_mac, $months);
        } else {
            $response = $this->renewM3U(
                (string) $subscription->iptv_username,
                (string) $subscription->iptv_password,
                $months
            );
        }

        return ($response['status'] ?? '') === 'true';
    }

    public function enable(UserSubscription $subscription): bool
    {
        $response = $this->enableDevice((string) $subscription->iptv_user_id);
        return ($response['status'] ?? '') === 'true';
    }

    public function disable(UserSubscription $subscription): bool
    {
        $response = $this->disableDevice((string) $subscription->iptv_user_id);
        return ($response['status'] ?? '') === 'true';
    }

    public function fetchInfo(UserSubscription $subscription): array
    {
        $type = $subscription->iptv_device_type === 'mag' ? 'mag' : 'm3u';

        $info = $type === 'mag'
            ? $this->deviceInfoMag((string) $subscription->iptv_mac)
            : $this->deviceInfoM3U((string) $subscription->iptv_username, (string) $subscription->iptv_password);

        if (($info['status'] ?? '') !== 'true') {
            return [];
        }

        return [
            'active'     => ($info['enabled'] ?? '1') == '1',
            'expires_at' => !empty($info['expire'])
                ? Carbon::parse($info['expire'])
                : null,
        ];
    }

    public function deleteAccount(UserSubscription $subscription): bool
    {
        // 8K has no delete endpoint — disable the device instead.
        return $this->disable($subscription);
    }

    // ── Low-level 8K actions ──────────────────────────────────────────────────

    public function createM3U(int $sub, string $pack, string $country = 'ALL', string $notes = ''): array
    {
        return $this->request([
            'action'  => 'new',
            'type'    => 'm3u',
            'sub'     => $sub,
            'pack'    => $pack,
            'country' => $country,
            'notes'   => $notes,
        ]);
    }

    public function createMag(string $mac, int $sub, string $pack, string $country = 'ALL', string $notes = ''): array
    {
        return $this->request([
            'action'  => 'new',
            'type'    => 'mag',
            'user'    => $mac,
            'sub'     => $sub,
            'pack'    => $pack,
            'country' => $country,
            'notes'   => $notes,
        ]);
    }

    public function renewM3U(string $username, string $password, int $sub): array
    {
        return $this->request([
            'action'   => 'renew',
            'type'     => 'm3u',
            'username' => $username,
            'password' => $password,
            'sub'      => $sub,
        ]);
    }

    public function renewMag(string $mac, int $sub): array
    {
        return $this->request([
            'action' => 'renew',
            'type'   => 'mag',
            'mac'    => $mac,
            'sub'    => $sub,
        ]);
    }

    public function enableDevice(string $id): array
    {
        return $this->request([
            'action' => 'device_status',
            'status' => 'enable',
            'id'     => $id,
        ]);
    }

    public function disableDevice(string $id): array
    {
        return $this->request([
            'action' => 'device_status',
            'status' => 'disable',
            'id'     => $id,
        ]);
    }

    /**
     * List available packages (bouquets): [['id' => .., 'name' => ..], ...].
     */
    public function getBouquets(): array
    {
        $response = $this->request(['action' => 'bouquet']);

        // The bouquet endpoint returns a bare JSON array.
        return array_is_list($response) ? $response : [];
    }

    public function deviceInfoM3U(string $username, string $password): array
    {
        return $this->request([
            'action'   => 'device_info',
            'username' => $username,
            'password' => $password,
        ]);
    }

    public function deviceInfoMag(string $mac): array
    {
        return $this->request([
            'action' => 'device_info',
            'mac'    => $mac,
        ]);
    }

    public function reseller(): array
    {
        return $this->request(['action' => 'reseller']);
    }

    /**
     * Extract username/password from a returned M3U url
     * (…/get.php?username=xxx&password=yyy&type=m3u_plus).
     */
    public static function parseM3UCredentials(string $url): array
    {
        $query = parse_url($url, PHP_URL_QUERY) ?: '';
        parse_str($query, $params);

        return [
            'username' => $params['username'] ?? null,
            'password' => $params['password'] ?? null,
        ];
    }

    protected function request(array $params): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $start      = microtime(true);
        $statusCode = 500;
        $response   = [];

        try {
            $result = Http::timeout(15)->get($this->apiUrl, array_merge($params, [
                'api_key' => $this->apiKey,
            ]));

            $statusCode = $result->status();
            $response   = $result->json() ?? [];
        } catch (\Exception $e) {
            $response = ['error' => $e->getMessage()];
        }

        // Never log the API key.
        $this->logRequest(
            '8k:' . ($params['action'] ?? 'unknown'),
            $params,
            $response,
            $statusCode,
            (int) ((microtime(true) - $start) * 1000)
        );

        return is_array($response) ? $response : [];
    }

    private function logRequest(string $endpoint, array $request, array $response, int $status, int $duration): void
    {
        try {
            \App\Models\ApiLog::create([
                'endpoint'         => $endpoint,
                'method'           => 'GET',
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
