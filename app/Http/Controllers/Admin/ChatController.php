<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $messages = ChatMessage::with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $giftsSentCount = ChatMessage::whereNotNull('gift_id')->count();

        return view('admin.chats.index', compact('messages', 'giftsSentCount'));
    }
}
