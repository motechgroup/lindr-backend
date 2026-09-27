<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LiveKitTokenService;
use Illuminate\Http\Request;

class LiveKitController extends Controller
{
    private LiveKitTokenService $tokenService;

    public function __construct(LiveKitTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function generateToken(Request $request)
    {
        $roomName = (string) ($request->input('roomName') ?? $request->input('channelName') ?? 'lindr_room_live');
        $identity = (string) ($request->input('identity') ?? $request->input('uid') ?? rand(1000, 9999));
        $name = (string) ($request->input('name') ?? ('User_' . $identity));

        $tokenData = $this->tokenService->generateToken($roomName, $identity, $name);

        return response()->json([
            'status' => 'success',
            'token' => $tokenData['token'],
            'url' => $tokenData['url'],
            'room' => $tokenData['room'],
            'identity' => $tokenData['identity'],
        ]);
    }
}
