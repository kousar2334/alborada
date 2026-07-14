<?php

namespace App\Mail;

use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User        $user,
        public string      $paymentUrl,
        public PricingPlan $plan,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your payment link for ' . $this->plan->title . ' — ' . get_setting('site_name', 'Moissanite Visions'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-link');
    }
}
