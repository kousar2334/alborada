<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\UserSubscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function createForSubscription(UserSubscription $subscription, array $overrides = []): Invoice
    {
        $plan   = $subscription->plan;
        $amount = $plan->price ?? 0;
        $tax    = $overrides['tax_amount'] ?? 0;

        $invoice = Invoice::create(array_merge([
            'user_id'         => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'amount'          => $amount,
            'tax_amount'      => $tax,
            'total_amount'    => $amount + $tax,
            'status'          => 'paid',
            'due_date'        => now()->toDateString(),
            'paid_at'         => now(),
        ], $overrides));

        $subscription->update(['invoice_id' => $invoice->id]);

        return $invoice;
    }

    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['user', 'subscription.plan']);

        $pdf  = Pdf::loadView('pdf.invoice', compact('invoice'));
        $path = 'invoices/' . $invoice->invoice_number . '.pdf';

        Storage::put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    public function sendByEmail(Invoice $invoice): void
    {
        if (!$invoice->pdf_path) {
            $this->generatePdf($invoice);
        }

        $invoice->load('user');

        Mail::to($invoice->user->email)->queue(new InvoiceMail($invoice));

        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
        }
    }
}
