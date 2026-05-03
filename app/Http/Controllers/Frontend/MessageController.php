<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $chats = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['ad', 'sender', 'receiver', 'lastMessage'])
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
            ->with(['ad', 'sender', 'receiver', 'messages.sender'])
            ->firstOrFail();

        // Mark messages from the other user as read
        $chat->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('frontend.pages.messages.show', compact('chat'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'ad_id'   => 'required|exists:ads,id',
            'message' => 'required|string|max:2000',
        ]);

        $ad = Ad::findOrFail($request->ad_id);
        $senderId   = Auth::id();
        $receiverId = $ad->user_id;

        if ($senderId === $receiverId) {
            return redirect()->back()->with('error', 'You cannot message yourself.');
        }

        // Find existing chat for this ad between these two users
        $chat = Chat::where('ad_id', $ad->id)
            ->where(function ($q) use ($senderId, $receiverId) {
                $q->where(function ($q2) use ($senderId, $receiverId) {
                    $q2->where('sender_id', $senderId)->where('receiver_id', $receiverId);
                })->orWhere(function ($q2) use ($senderId, $receiverId) {
                    $q2->where('sender_id', $receiverId)->where('receiver_id', $senderId);
                });
            })->first();

        if (!$chat) {
            $chat = Chat::create([
                'uid'         => Str::uuid(),
                'ad_id'       => $ad->id,
                'sender_id'   => $senderId,
                'receiver_id' => $receiverId,
            ]);
        }

        ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $senderId,
            'message'   => $request->message,
        ]);

        $chat->touch();

        return redirect()->route('member.messages.show', $chat->uid)
            ->with('success', 'Message sent successfully.');
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
