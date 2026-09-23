<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function logCall(Request $request)
    {
        $validated = $request->validate([
            'callerId' => 'required',
            'receiverId' => 'required',
            'durationSeconds' => 'required|integer',
            'tokensSpent' => 'required|integer',
            'creditsEarned' => 'required|integer',
        ]);

        $call = CallSession::create([
            'caller_id' => $validated['callerId'],
            'receiver_id' => $validated['receiverId'],
            'duration_seconds' => $validated['durationSeconds'],
            'tokens_spent' => $validated['tokensSpent'],
            'credits_earned' => $validated['creditsEarned'],
            'status' => 'completed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Call session logged',
            'call' => $call
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'senderId' => 'required',
            'receiverId' => 'required',
            'message' => 'nullable|string',
            'giftId' => 'nullable|string',
            'giftCostTokens' => 'nullable|integer',
        ]);

        $chat = ChatMessage::create([
            'sender_id' => $validated['senderId'],
            'receiver_id' => $validated['receiverId'],
            'message' => $validated['message'] ?? 'Gift sent: ' . ($validated['giftId'] ?? ''),
            'gift_id' => $validated['giftId'] ?? null,
            'gift_cost_tokens' => $validated['giftCostTokens'] ?? 0,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Message logged',
            'chat' => $chat
        ]);
    }
}
