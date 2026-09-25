<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function initiateCall(Request $request)
    {
        $validated = $request->validate([
            'callerId' => 'required',
            'receiverId' => 'required',
            'channelName' => 'nullable|string',
        ]);

        $channelName = $validated['channelName'] ?? ('lindr_call_' . $validated['callerId'] . '_' . $validated['receiverId']);

        // Cancel any previous pending calls for this caller
        CallSession::where('caller_id', $validated['callerId'])
            ->where('status', 'ringing')
            ->update(['status' => 'cancelled']);

        $call = CallSession::create([
            'caller_id' => $validated['callerId'],
            'receiver_id' => $validated['receiverId'],
            'channel_name' => $channelName,
            'status' => 'ringing',
        ]);

        $caller = User::find($validated['callerId']);

        return response()->json([
            'status' => 'success',
            'callId' => $call->id,
            'channelName' => $channelName,
            'callStatus' => 'ringing',
            'caller' => $caller ? [
                'id' => (string) $caller->id,
                'name' => $caller->name,
                'avatar' => $caller->avatar,
                'country' => $caller->country_name ?? 'Kenya',
                'flag' => ($caller->country_code ?? 'KE') === 'KE' ? '🇰🇪' : '🌐',
                'age' => $caller->birthdate ? date_diff(date_create($caller->birthdate), date_create('today'))->y : 22,
            ] : null,
        ]);
    }

    public function checkIncomingCall(Request $request)
    {
        $userId = $request->header('X-User-Id') ?? $request->query('userId');

        if (empty($userId)) {
            return response()->json(['status' => 'success', 'incomingCall' => null]);
        }

        // Find recent ringing call for this user created in the last 45 seconds
        $incoming = CallSession::where('receiver_id', $userId)
            ->where('status', 'ringing')
            ->where('created_at', '>=', now()->subSeconds(45))
            ->latest()
            ->first();

        if (!$incoming) {
            return response()->json(['status' => 'success', 'incomingCall' => null]);
        }

        $caller = User::find($incoming->caller_id);

        return response()->json([
            'status' => 'success',
            'incomingCall' => [
                'id' => $incoming->id,
                'callerId' => (string) $incoming->caller_id,
                'channelName' => $incoming->channel_name,
                'status' => $incoming->status,
                'caller' => $caller ? [
                    'id' => (string) $caller->id,
                    'name' => $caller->name,
                    'gender' => $caller->gender,
                    'avatar' => $caller->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                    'location' => $caller->country_name ?? 'Kenya',
                    'country' => $caller->country_name ?? 'Kenya',
                    'flag' => ($caller->country_code ?? 'KE') === 'KE' ? '🇰🇪' : '🌐',
                    'age' => $caller->birthdate ? date_diff(date_create($caller->birthdate), date_create('today'))->y : 22,
                    'callRatePerMin' => 25,
                ] : null,
            ]
        ]);
    }

    public function checkCallStatus(Request $request)
    {
        $callId = $request->query('callId');
        if (empty($callId)) {
            return response()->json(['status' => 'error', 'message' => 'callId required'], 400);
        }

        $call = CallSession::find($callId);
        if (!$call) {
            return response()->json(['status' => 'error', 'message' => 'Call not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'callStatus' => $call->status,
            'channelName' => $call->channel_name,
        ]);
    }

    public function respondCall(Request $request)
    {
        $validated = $request->validate([
            'callId' => 'required',
            'action' => 'required|in:accept,decline,cancel',
        ]);

        $call = CallSession::find($validated['callId']);
        if (!$call) {
            return response()->json(['status' => 'error', 'message' => 'Call not found'], 404);
        }

        $newStatus = match ($validated['action']) {
            'accept' => 'accepted',
            'decline' => 'declined',
            'cancel' => 'cancelled',
        };

        $call->status = $newStatus;
        $call->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Call updated to ' . $newStatus,
            'callStatus' => $newStatus,
        ]);
    }

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
            'channel_name' => 'lindr_call_' . $validated['callerId'] . '_' . $validated['receiverId'],
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

    public function getChatHistory(Request $request)
    {
        $userId = $request->header('X-User-Id') ?? $request->query('userId');
        $partnerId = $request->query('partnerId');

        if (empty($userId) || empty($partnerId)) {
            return response()->json(['status' => 'error', 'message' => 'userId and partnerId required'], 400);
        }

        $messages = ChatMessage::where(function($q) use ($userId, $partnerId) {
                $q->where('sender_id', $userId)->where('receiver_id', $partnerId);
            })->orWhere(function($q) use ($userId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($m) {
                return [
                    'id' => (string) $m->id,
                    'senderId' => (string) $m->sender_id,
                    'receiverId' => (string) $m->receiver_id,
                    'text' => $m->message,
                    'giftId' => $m->gift_id,
                    'timestamp' => $m->created_at ? $m->created_at->format('H:i') : date('H:i'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }
}
