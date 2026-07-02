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

    // ── Order / service lifecycle ──────────────────────────────────────────────

    /**
     * Place an order for the configured IPTV product. Returns the raw response,
     * which includes `orderid` and `productids` on success.
     */
    public function addOrder(array $orderData): array
    {
        return $this->call('AddOrder', $orderData);
    }

    /**
     * Accept a pending order — provisions the module and generates the invoice.
     */
    public function acceptOrder(int $orderId, array $params = []): array
    {
        return $this->call('AcceptOrder', array_merge(['orderid' => $orderId], $params));
    }

    public function moduleCreate(int $serviceId): bool
    {
        $result = $this->call('ModuleCreate', ['serviceid' => $serviceId]);
        return ($result['result'] ?? '') === 'success';
    }

    public function suspendService(int $serviceId, string $reason = ''): bool
    {
        $result = $this->call('ModuleSuspend', array_filter([
            'serviceid'     => $serviceId,
            'suspendreason' => $reason ?: null,
        ]));
        return ($result['result'] ?? '') === 'success';
    }

    public function unsuspendService(int $serviceId): bool
    {
        $result = $this->call('ModuleUnsuspend', ['serviceid' => $serviceId]);
        return ($result['result'] ?? '') === 'success';
    }

    public function terminateService(int $serviceId): bool
    {
        $result = $this->call('ModuleTerminate', ['serviceid' => $serviceId]);
        return ($result['result'] ?? '') === 'success';
    }

    /**
     * Record a payment against a WHMCS invoice (marks it Paid).
     */
    public function addInvoicePayment(int $invoiceId, string $transactionId, float $amount = 0, string $gateway = 'stripe'): bool
    {
        $result = $this->call('AddInvoicePayment', array_filter([
            'invoiceid' => $invoiceId,
            'transid'   => $transactionId,
            'gateway'   => $gateway,
            'amount'    => $amount > 0 ? $amount : null,
        ]));
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
