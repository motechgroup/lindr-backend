<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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
                $countryCode = $request->input('countryCode', 'KE');
                $countryName = $request->input('countryName', $countryCode === 'KE' ? 'Kenya' : 'International');

                $user = User::create([
                    'name' => !empty($validated['name']) ? $validated['name'] : 'Google User',
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => !empty($validated['avatar']) ? $validated['avatar'] : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                    'country_code' => $countryCode,
                    'country_name' => $countryName,
                    'tokens' => 50,
                    'credits' => 0,
                    'gender' => 'pending',
                    'birthdate' => null,
                    'is_verified' => false,
                    'last_heartbeat_at' => now(),
                ]);
            } else {
                if (!empty($validated['avatar'])) $user->avatar = $validated['avatar'];
                if (!empty($validated['name']) && (empty($user->name) || $user->name === 'Google User')) $user->name = $validated['name'];
                if (!empty($googleId)) $user->google_id = $googleId;
                $user->last_heartbeat_at = now();
                $user->touch();
                $user->save();
            }

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
            $countryCode = $request->input('countryCode', 'KE');
            $countryName = $request->input('countryName', $countryCode === 'KE' ? 'Kenya' : 'International');

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'name' => !empty($validated['name']) ? $validated['name'] : explode('@', $email)[0],
                    'password' => !empty($validated['password']) ? \Illuminate\Support\Facades\Hash::make($validated['password']) : null,
                    'country_code' => $countryCode,
                    'country_name' => $countryName,
                    'tokens' => 0,
                    'credits' => 0,
                    'gender' => 'pending',
                    'birthdate' => null,
                    'is_verified' => false,
                    'last_heartbeat_at' => now(),
                ]);
            } else {
                if (!empty($validated['name']) && empty($user->name)) {
                    $user->name = $validated['name'];
                }
                if (!empty($validated['password']) && empty($user->password)) {
                    $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
                }
                $user->last_heartbeat_at = now();
                $user->touch();
                $user->save();
            }

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
