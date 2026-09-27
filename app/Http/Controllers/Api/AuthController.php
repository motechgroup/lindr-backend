<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private function touchHeartbeat($user)
    {
        if (!$user) return;
        try {
            $nowStr = now()->toDateTimeString();
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'last_heartbeat_at' => $nowStr,
                    'updated_at' => $nowStr,
                ]);
        } catch (\Throwable $e) {}
    }

    private function getCountryNameFromCode($code)
    {
        $c = strtoupper(trim($code ?? ''));
        $map = [
            'KE' => 'Kenya',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'NG' => 'Nigeria',
            'ZA' => 'South Africa',
            'TZ' => 'Tanzania',
            'UG' => 'Uganda',
            'IN' => 'India',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'BR' => 'Brazil',
            'ES' => 'Spain',
            'KR' => 'South Korea',
            'DE' => 'Germany',
            'FR' => 'France',
        ];
        return $map[$c] ?? ($c === 'KE' ? 'Kenya' : 'International');
    }

    private function detectCountryFromRequest(Request $request)
    {
        $code = strtoupper(trim($request->input('countryCode', '')));
        $name = trim($request->input('countryName', ''));

        if (!empty($code) && strlen($code) === 2 && $code !== 'KE' && !empty($name) && $name !== 'Kenya') {
            return ['code' => $code, 'name' => $name];
        }

        $cfCountry = strtoupper(trim($request->header('CF-IPCOUNTRY') ?: $request->server('HTTP_CF_IPCOUNTRY') ?: ''));
        if (!empty($cfCountry) && strlen($cfCountry) === 2 && $cfCountry !== 'XX') {
            return ['code' => $cfCountry, 'name' => $this->getCountryNameFromCode($cfCountry)];
        }

        $ip = $request->ip();
        if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 1.5]]);
                $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode", false, $ctx);
                if (!empty($json)) {
                    $data = json_decode($json, true);
                    if (($data['status'] ?? '') === 'success' && !empty($data['countryCode'])) {
                        return [
                            'code' => strtoupper($data['countryCode']),
                            'name' => $data['country'] ?? $this->getCountryNameFromCode($data['countryCode'])
                        ];
                    }
                }
            } catch (\Throwable $e) {}
        }

        return [
            'code' => !empty($code) ? $code : 'KE',
            'name' => !empty($name) ? $name : 'Kenya'
        ];
    }

    public function googleLogin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'name' => 'nullable|string',
                'avatar' => 'nullable|string',
                'googleId' => 'nullable|string',
            ]);

            $email = strtolower(trim($validated['email']));
            $googleId = $validated['googleId'] ?? 'g_' . Str::random(10);

            $user = User::where('email', $email)
                ->orWhere(function($query) use ($googleId) {
                    if (!empty($googleId)) {
                        $query->where('google_id', $googleId);
                    }
                })
                ->first();

            if (!$user) {
                $cData = $this->detectCountryFromRequest($request);

                $user = User::create([
                    'name' => !empty($validated['name']) ? $validated['name'] : 'Google User',
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => !empty($validated['avatar']) ? $validated['avatar'] : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                    'country_code' => $cData['code'],
                    'country_name' => $cData['name'],
                    'tokens' => 50,
                    'credits' => 0,
                    'gender' => 'pending',
                    'birthdate' => null,
                    'is_verified' => false,
                ]);
            } else {
                if (!empty($validated['avatar'])) $user->avatar = $validated['avatar'];
                if (!empty($validated['name']) && (empty($user->name) || $user->name === 'Google User')) $user->name = $validated['name'];
                if (!empty($googleId)) $user->google_id = $googleId;
                if (empty($user->country_code)) {
                    $cData = $this->detectCountryFromRequest($request);
                    $user->country_code = $cData['code'];
                    $user->country_name = $cData['name'];
                }
                $user->save();
            }

            $this->touchHeartbeat($user);

            return response()->json([
                'status' => 'success',
                'token' => 'auth_token_' . $user->id . '_' . Str::random(16),
                'user' => [
                    'id' => (string) $user->id,
                    'name' => $user->name ?? 'Google User',
                    'email' => $user->email,
                    'gender' => $user->gender ?? 'pending',
                    'birthdate' => $user->birthdate ?? null,
                    'avatar' => $user->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                    'countryCode' => $user->country_code ?? 'KE',
                    'countryName' => $user->country_name ?? 'Kenya',
                    'isVerified' => (bool) ($user->is_verified ?? false),
                    'tokens' => (int) ($user->tokens ?? 50),
                    'credits' => (int) ($user->credits ?? 0),
                    'totalTopUpTokens' => (int) ($user->total_topup_tokens ?? 0),
                    'totalCreditsEarned' => (int) ($user->total_credits_earned ?? 0),
                    'expPoints' => (int) ($user->exp_points ?? 0),
                    'level' => (int) ($user->level ?? 1),
                    'isLoggedIn' => true,
                    'hasCompletedOnboarding' => in_array($user->gender, ['male', 'female']) && !empty($user->birthdate),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function emailLogin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'nullable|string',
                'name' => 'nullable|string',
                'countryCode' => 'nullable|string',
                'countryName' => 'nullable|string',
            ]);

            $email = strtolower(trim($validated['email']));
            $cData = $this->detectCountryFromRequest($request);

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'name' => !empty($validated['name']) ? $validated['name'] : explode('@', $email)[0],
                    'password' => !empty($validated['password']) ? \Illuminate\Support\Facades\Hash::make($validated['password']) : null,
                    'country_code' => $cData['code'],
                    'country_name' => $cData['name'],
                    'tokens' => 0,
                    'credits' => 0,
                    'gender' => 'pending',
                    'birthdate' => null,
                    'is_verified' => false,
                ]);
            } else {
                if (!empty($validated['name']) && empty($user->name)) {
                    $user->name = $validated['name'];
                }
                if (!empty($validated['password']) && empty($user->password)) {
                    $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
                }
                if (empty($user->country_code)) {
                    $user->country_code = $cData['code'];
                    $user->country_name = $cData['name'];
                }
                $user->save();
            }

            $this->touchHeartbeat($user);

            return response()->json([
                'status' => 'success',
                'token' => 'auth_token_' . $user->id . '_' . Str::random(16),
                'user' => [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $user->gender ?? 'pending',
                    'birthdate' => $user->birthdate ?? null,
                    'avatar' => $user->avatar ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                    'countryCode' => $user->country_code ?? 'KE',
                    'countryName' => $user->country_name ?? 'Kenya',
                    'isVerified' => (bool) ($user->is_verified ?? false),
                    'tokens' => (int) ($user->tokens ?? 0),
                    'credits' => (int) ($user->credits ?? 0),
                    'totalTopUpTokens' => (int) ($user->total_topup_tokens ?? 0),
                    'totalCreditsEarned' => (int) ($user->total_credits_earned ?? 0),
                    'expPoints' => (int) ($user->exp_points ?? 0),
                    'level' => (int) ($user->level ?? 1),
                    'isLoggedIn' => true,
                    'hasCompletedOnboarding' => in_array($user->gender, ['male', 'female']) && !empty($user->birthdate),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
