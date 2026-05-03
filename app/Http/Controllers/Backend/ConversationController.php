<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Chat;

class ConversationController extends Controller
{
    public function index()
    {
        $chats = Chat::with(['ad', 'sender', 'receiver', 'lastMessage'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('backend.modules.messages.index', compact('chats'));
    }

    public function show($uid)
    {
        $chat = Chat::where('uid', $uid)
            ->with(['ad', 'sender', 'receiver', 'messages.sender'])
            ->firstOrFail();

        return view('backend.modules.messages.show', compact('chat'));
    }
}
