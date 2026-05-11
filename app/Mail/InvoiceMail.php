<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Invoice ' . $this->invoice->invoice_number . ' — ' . get_setting('site_name', 'Alborada Box'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.invoice');
    }

    public function attachments(): array
    {
        if ($this->invoice->pdf_path && \Illuminate\Support\Facades\Storage::exists($this->invoice->pdf_path)) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromStorage($this->invoice->pdf_path)
                    ->as('invoice-' . $this->invoice->invoice_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
