<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewTicketReplyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public TicketReply   $reply,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $from = $this->reply->is_staff_reply ? 'Support Team' : $this->reply->user->name;

        return (new MailMessage)
            ->subject('New reply on ticket ' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($from . ' has replied to your ticket **' . $this->ticket->subject . '**.')
            ->line('> ' . \Illuminate\Support\Str::limit($this->reply->message, 200))
            ->action('View Ticket', route('member.tickets.show', $this->ticket->ticket_number))
            ->line('Reply to continue the conversation.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New reply on ticket ' . $this->ticket->ticket_number . ': ' . $this->ticket->subject,
            'link'    => route('member.tickets.show', $this->ticket->ticket_number),
        ];
    }
}
