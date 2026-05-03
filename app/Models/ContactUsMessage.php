<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUsMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'reply_message',
        'replied_at',
    ];

    protected $casts = [
        'is_read'     => 'boolean',
        'replied_at'  => 'datetime',
    ];
}
