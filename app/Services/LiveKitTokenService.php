<?php

namespace App\Services;

class LiveKitTokenService
{
    private string $apiKey;
    private string $apiSecret;
    private string $livekitUrl;

    public function __construct()
    {
        $this->apiKey = env('LIVEKIT_API_KEY', 'devkey');
        $this->apiSecret = env('LIVEKIT_API_SECRET', 'secret');
        $this->livekitUrl = env('LIVEKIT_URL', 'wss://demo.livekit.cloud');
    }

    /**
     * Generate a LiveKit Access Token for a participant in a room
     */
    public function generateToken(string $roomName, string $identity, ?string $name = null, int $ttlSeconds = 86400): array
    {
        $now = time();
        $exp = $now + $ttlSeconds;

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $payload = [
            'iss' => $this->apiKey,
            'sub' => $identity,
            'name' => $name ?? $identity,
            'nbf' => $now - 5,
            'exp' => $exp,
            'video' => [
                'room' => $roomName,
                'roomJoin' => true,
                'canPublish' => true,
                'canSubscribe' => true,
                'canPublishData' => true,
            ],
        ];

        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, $this->apiSecret, true);
        $encodedSignature = $this->base64UrlEncode($signature);

        $token = $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;

        return [
            'token' => $token,
            'url' => $this->livekitUrl,
            'room' => $roomName,
            'identity' => $identity,
        ];
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
