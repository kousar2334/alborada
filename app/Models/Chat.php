<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['uid', 'ad_id', 'sender_id', 'receiver_id'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function unreadCountFor($userId)
    {
        return $this->messages()->where('sender_id', '!=', $userId)->where('is_read', false)->count();
    }

    public function otherParticipant($userId)
    {
        return $this->sender_id === $userId ? $this->receiver : $this->sender;
    }
}
