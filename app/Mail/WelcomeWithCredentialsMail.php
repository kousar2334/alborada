<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeWithCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User             $user,
        public UserSubscription $subscription,
        public array            $credentials,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to ' . get_setting('site_name', 'Moissanite Radiance') . ' — Your IPTV Credentials');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome-credentials');
    }
}
