<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgoraController extends Controller
{
    public function generateToken(Request $request)
    {
        $validated = $request->validate([
            'channelName' => 'required|string',
            'uid' => 'nullable|integer',
        ]);

        $appId = env('EXPO_PUBLIC_AGORA_APP_ID', '7f7547aa4508451bb0dcd38612ba5c35');
        
        return response()->json([
            'status' => 'success',
            'appId' => $appId,
            'channelName' => $validated['channelName'],
            'rtcToken' => 'mock_rtc_token_' . Str::random(24),
            'uid' => $validated['uid'] ?? rand(1000, 9999),
        ]);
    }
}
