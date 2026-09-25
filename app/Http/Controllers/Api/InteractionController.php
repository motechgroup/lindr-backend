<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Transaction;
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
            'action' => 'required|in:accept,decline,cancel,end',
        ]);

        $call = CallSession::find($validated['callId']);
        if (!$call) {
            return response()->json(['status' => 'error', 'message' => 'Call not found'], 404);
        }

        $newStatus = match ($validated['action']) {
            'accept' => 'accepted',
            'decline' => 'declined',
            'cancel' => 'cancelled',
            'end' => 'ended',
        };

        $call->status = $newStatus;
        $call->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Call updated to ' . $newStatus,
            'callStatus' => $newStatus,
        ]);
    }

    public function deductCallTicker(Request $request)
    {
        $validated = $request->validate([
            'callerId' => 'required',
            'receiverId' => 'required',
            'callRatePerMin' => 'required|integer',
            'creditPayRatePerMin' => 'required|integer',
        ]);

        $caller = User::find($validated['callerId']);
        $receiver = User::find($validated['receiverId']);

        // 10-second ticker cost and payout calculations
        $costPer10Sec = max(1, (int) ceil($validated['callRatePerMin'] / 6));
        $payoutPer10Sec = max(1, (int) ceil($validated['creditPayRatePerMin'] / 6));

        if ($caller) {
            if ($caller->tokens < $costPer10Sec) {
                return response()->json([
                    'status' => 'insufficient_tokens',
                    'message' => 'Caller ran out of tokens',
                    'shouldEndCall' => true,
                    'callerTokens' => $caller->tokens,
                ]);
            }

            // Deduct tokens from caller
            $caller->tokens = max(0, $caller->tokens - $costPer10Sec);
            $caller->save();

            // Payout credits to female receiver
            if ($receiver) {
                $receiver->credits += $payoutPer10Sec;
                $receiver->total_credits_earned += $payoutPer10Sec;
                $receiver->exp_points += ($payoutPer10Sec * 2);
                $receiver->save();
            }

            return response()->json([
                'status' => 'success',
                'callerTokens' => $caller->tokens,
                'receiverCredits' => $receiver ? $receiver->credits : 0,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Caller not found'], 404);
    }

    public function logCall(Request $request)
    {
        $validated = $request->validate([
            'callerId' => 'required',
            'receiverId' => 'required',
            'durationSeconds' => 'required|integer',
            'tokensSpent' => 'required|integer',
            'creditsEarned' => 'required|integer',
            'callId' => 'nullable',
        ]);

        $caller = User::find($validated['callerId']);
        $receiver = User::find($validated['receiverId']);

        $tokensSpent = (int) $validated['tokensSpent'];
        $creditsEarned = (int) $validated['creditsEarned'];

        $call = null;
        if (!empty($validated['callId'])) {
            $call = CallSession::find($validated['callId']);
        }
        if (!$call) {
            $call = CallSession::where('caller_id', $validated['callerId'])
                ->where('receiver_id', $validated['receiverId'])
                ->latest()
                ->first();
        }

        if ($call) {
            $call->update([
                'duration_seconds' => $validated['durationSeconds'],
                'tokens_spent' => $tokensSpent,
                'credits_earned' => $creditsEarned,
                'status' => 'ended',
            ]);
        } else {
            $call = CallSession::create([
                'caller_id' => $validated['callerId'],
                'receiver_id' => $validated['receiverId'],
                'channel_name' => 'lindr_call_' . $validated['callerId'] . '_' . $validated['receiverId'],
                'duration_seconds' => $validated['durationSeconds'],
                'tokens_spent' => $tokensSpent,
                'credits_earned' => $creditsEarned,
                'status' => 'ended',
            ]);
        }

        if ($tokensSpent > 0 && $caller) {
            Transaction::create([
                'user_id' => $caller->id,
                'type' => 'call_deduction',
                'amount_tokens' => -$tokensSpent,
                'amount_credits' => 0,
                'amount_usd' => round(($tokensSpent / 100), 2),
                'payment_provider' => 'call_billing',
                'reference' => 'CALL_' . strtoupper(bin2hex(random_bytes(4))),
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Call session logged',
            'call' => $call,
            'callerTokens' => $caller ? $caller->tokens : 0,
            'receiverCredits' => $receiver ? $receiver->credits : 0,
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
            'image' => 'nullable|string', // base64 or url
        ]);

        $sender = User::find($validated['senderId']);
        $receiver = User::find($validated['receiverId']);

        $giftCost = (int) ($validated['giftCostTokens'] ?? 0);
        $giftId = $validated['giftId'] ?? null;
        $imageUrl = null;

        // Fetch dynamic system rates & commission percentages configured by Admin
        $textCost = (int) (\App\Models\SystemSetting::where('key', 'chat_text_message_cost')->value('value') ?? 3);
        $mediaCost = (int) (\App\Models\SystemSetting::where('key', 'chat_media_message_cost')->value('value') ?? 5);
        $chatCommission = (int) (\App\Models\SystemSetting::where('key', 'chat_commission_percentage')->value('value') ?? \App\Models\SystemSetting::where('key', 'creator_payout_split_percentage')->value('value') ?? 70);
        $giftCommission = (int) (\App\Models\SystemSetting::where('key', 'gift_commission_percentage')->value('value') ?? 70);

        // Handle Image Attachment with Strict Sanitization (JPEG, JPG, PNG only, max 5MB, binary verification)
        if (!empty($validated['image'])) {
            $rawImg = $validated['image'];
            $extension = 'jpg';
            $imgData = null;

            if (str_starts_with($rawImg, 'data:image/')) {
                preg_match('/data:image\/(jpeg|jpg|png);base64,(.*)/i', $rawImg, $matches);
                if (count($matches) >= 3) {
                    $ext = strtolower($matches[1]);
                    if (!in_array($ext, ['jpeg', 'jpg', 'png'])) {
                        return response()->json(['status' => 'error', 'message' => 'Invalid image format. Only JPEG, JPG and PNG images are permitted.'], 422);
                    }
                    $extension = $ext === 'jpeg' ? 'jpg' : $ext;
                    $imgData = base64_decode($matches[2]);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Invalid image encoding format.'], 422);
                }
            } else if (filter_var($rawImg, FILTER_VALIDATE_URL)) {
                $imageUrl = $rawImg;
            }

            if ($imgData !== null) {
                // Strict file size check (5MB max)
                if (strlen($imgData) > 5 * 1024 * 1024) {
                    return response()->json(['status' => 'error', 'message' => 'Image size exceeds maximum 5MB limit.'], 422);
                }

                // Strict binary magic bytes verification using gd image processing
                $gdImg = @imagecreatefromstring($imgData);
                if (!$gdImg) {
                    return response()->json(['status' => 'error', 'message' => 'Uploaded file is corrupted or not a valid image.'], 422);
                }
                imagedestroy($gdImg);

                // Save to safe storage directory with sanitized random hash name
                $fileName = 'chat_img_' . bin2hex(random_bytes(8)) . '.' . $extension;
                $storageDir = public_path('storage/chat_images');
                if (!file_exists($storageDir)) {
                    mkdir($storageDir, 0755, true);
                }
                file_put_contents($storageDir . '/' . $fileName, $imgData);
                $imageUrl = url('storage/chat_images/' . $fileName);
            }
        }

        $tokenCost = 0;

        if ($sender) {
            if (!empty($giftId) && $giftCost > 0) {
                $tokenCost = $giftCost;
                if ($sender->tokens < $giftCost) {
                    return response()->json(['status' => 'insufficient_tokens', 'message' => 'Insufficient tokens to send gift.'], 400);
                }
                $sender->tokens = max(0, $sender->tokens - $giftCost);
                $sender->save();

                // Credit payout to receiver based on gift commission %
                if ($receiver) {
                    $earnedCredits = max(1, (int) round(($giftCost * $giftCommission) / 100));
                    $receiver->credits += $earnedCredits;
                    $receiver->total_credits_earned += $earnedCredits;
                    $receiver->exp_points += ($earnedCredits * 2);
                    $receiver->save();

                    Transaction::create([
                        'user_id' => $receiver->id,
                        'type' => 'gift_payout',
                        'amount_tokens' => $giftCost,
                        'amount_credits' => $earnedCredits,
                        'amount_usd' => round(($earnedCredits / 100), 2),
                        'payment_provider' => 'gift_conversion',
                        'reference' => 'GIFT_' . strtoupper(bin2hex(random_bytes(4))),
                        'status' => 'completed',
                    ]);
                }
            } else if (strtolower($sender->gender ?? '') === 'male') {
                $tokenCost = !empty($imageUrl) ? $mediaCost : $textCost;

                if ($sender->tokens < $tokenCost) {
                    return response()->json(['status' => 'insufficient_tokens', 'message' => 'Insufficient tokens balance to send message.'], 400);
                }

                $sender->tokens = max(0, $sender->tokens - $tokenCost);
                $sender->save();

                // Male-to-Female Chat Token Cost Commission Split (Admin Configurable %)
                if ($receiver && strtolower($receiver->gender ?? '') === 'female') {
                    $earnedCredits = max(1, (int) round(($tokenCost * $chatCommission) / 100));
                    $receiver->credits += $earnedCredits;
                    $receiver->total_credits_earned += $earnedCredits;
                    $receiver->exp_points += ($earnedCredits * 2);
                    $receiver->save();

                    Transaction::create([
                        'user_id' => $sender->id,
                        'type' => 'chat_deduction',
                        'amount_tokens' => -$tokenCost,
                        'amount_credits' => $earnedCredits,
                        'amount_usd' => round(($tokenCost / 100), 2),
                        'payment_provider' => 'chat_billing',
                        'reference' => 'CHAT_' . strtoupper(bin2hex(random_bytes(4))),
                        'status' => 'completed',
                    ]);
                }
            }
        }

        $chat = ChatMessage::create([
            'sender_id' => $validated['senderId'],
            'receiver_id' => $validated['receiverId'],
            'message_text' => $validated['message'] ?? (!empty($giftId) ? 'Sent a virtual gift 🎁' : (!empty($imageUrl) ? '📷 Sent photo' : '')),
            'gift_id' => $giftId,
            'tokens_spent' => $tokenCost,
            'image_url' => $imageUrl,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Message logged successfully',
            'chat' => [
                'id' => (string) $chat->id,
                'senderId' => (string) $chat->sender_id,
                'receiverId' => (string) $chat->receiver_id,
                'text' => $chat->message_text,
                'giftId' => $chat->gift_id,
                'imageUrl' => $chat->image_url,
                'timestamp' => $chat->created_at ? $chat->created_at->format('H:i') : date('H:i'),
            ],
            'senderTokens' => $sender ? $sender->tokens : 0,
            'receiverCredits' => $receiver ? $receiver->credits : 0,
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
                    'text' => $m->message_text ?? '',
                    'message' => $m->message_text ?? '',
                    'giftId' => $m->gift_id,
                    'imageUrl' => $m->image_url ?? null,
                    'timestamp' => $m->created_at ? $m->created_at->format('H:i') : date('H:i'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }
}
