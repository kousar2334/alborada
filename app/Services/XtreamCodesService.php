<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class XtreamCodesService
{
    protected string $baseUrl;
    protected string $adminUsername;
    protected string $adminPassword;

    public function __construct()
    {
        $this->baseUrl       = rtrim(get_setting('xtream_base_url', ''), '/');
        $this->adminUsername = get_setting('xtream_admin_username', '');
        $this->adminPassword = get_setting('xtream_admin_password', '');
    }

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
        } catch (\Exception $e) {
            // fail silently — logging must not break the main flow
        }
    }
}
