<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhmcsService
{
    protected string $apiUrl;
    protected string $identifier;
    protected string $secret;

    public function __construct()
    {
        $this->apiUrl     = (string) get_setting('whmcs_api_url', '');
        $this->identifier = (string) get_setting('whmcs_api_identifier', '');
        $this->secret     = (string) get_setting('whmcs_api_secret', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->identifier) && !empty($this->secret);
    }

    public function createClient(array $clientData): array
    {
        return $this->call('AddClient', $clientData);
    }

    public function updateClient(int $whmcsClientId, array $data): bool
    {
        $result = $this->call('UpdateClient', array_merge(['clientid' => $whmcsClientId], $data));
        return ($result['result'] ?? '') === 'success';
    }

    public function getClient(int $whmcsClientId): array
    {
        return $this->call('GetClientsDetails', ['clientid' => $whmcsClientId, 'stats' => false]);
    }

    public function createInvoice(array $invoiceData): array
    {
        return $this->call('CreateInvoice', $invoiceData);
    }

    public function getInvoice(int $whmcsInvoiceId): array
    {
        return $this->call('GetInvoice', ['invoiceid' => $whmcsInvoiceId]);
    }

    public function syncSubscriptionStatus(int $whmcsClientId, bool $active): bool
    {
        $result = $this->call('UpdateClient', [
            'clientid' => $whmcsClientId,
            'status'   => $active ? 'Active' : 'Inactive',
        ]);
        return ($result['result'] ?? '') === 'success';
    }

    protected function call(string $action, array $params = []): array
    {
        if (!$this->isConfigured()) {
            return ['result' => 'error', 'message' => 'WHMCS not configured'];
        }

        $start = microtime(true);
        $statusCode = 500;
        $response = [];

        try {
            $result = Http::timeout(15)->asForm()->post($this->apiUrl, array_merge([
                'identifier' => $this->identifier,
                'secret'     => $this->secret,
                'action'     => $action,
                'responsetype' => 'json',
            ], $params));

            $statusCode = $result->status();
            $response   = $result->json() ?? [];
        } catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => $e->getMessage()];
        }

        $this->logRequest('whmcs:' . $action, $params, $response, $statusCode, (int)((microtime(true) - $start) * 1000));

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
            // fail silently
        }
    }
}
