<?php

namespace App\Notifications;

use App\Models\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAdPosted extends Notification
{
    use Queueable;

    public function __construct(public Ad $ad) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'A new ad "' . $this->ad->title . '" has been posted.',
            'link' => route('ad.details.page', ['slug' => $this->ad->uid]),
        ];
    }
}
