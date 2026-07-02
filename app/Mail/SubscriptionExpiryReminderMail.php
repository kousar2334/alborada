<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User             $user,
        public UserSubscription $subscription,
    ) {}

    public function envelope(): Envelope
    {
        $daysLeft = now()->diffInDays($this->subscription->expires_at);
        return new Envelope(subject: 'Your subscription expires in ' . $daysLeft . ' days — ' . get_setting('site_name', 'Moissanite Radiance'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-expiry-reminder');
    }
}
