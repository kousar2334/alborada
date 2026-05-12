<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $chats = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'lastMessage'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('frontend.pages.messages.index', compact('chats'));
    }

    public function show($uid)
    {
        $userId = Auth::id();

        $chat = Chat::where('uid', $uid)
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver', 'messages.sender'])
            ->firstOrFail();

        // Mark messages from the other user as read
        $chat->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('frontend.pages.messages.show', compact('chat'));
    }

    public function sendMessage(Request $request, $uid)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();

        $chat = Chat::where('uid', $uid)
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })->firstOrFail();

        ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $userId,
            'message'   => $request->message,
        ]);

        $chat->touch();

        return redirect()->route('member.messages.show', $uid)
            ->with('success', 'Message sent.');
    }
}
