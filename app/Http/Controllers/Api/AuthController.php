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
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string',
            'avatar' => 'nullable|string',
            'googleId' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['email'])
            ->orWhere(function($query) use ($validated) {
                if (!empty($validated['googleId'])) {
                    $query->where('google_id', $validated['googleId']);
                }
            })
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $validated['name'] ?? 'Google User',
                'email' => $validated['email'],
                'google_id' => $validated['googleId'] ?? 'g_' . Str::random(10),
                'avatar' => $validated['avatar'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                'tokens' => 350,
                'credits' => 0,
                'gender' => 'male',
                'is_verified' => false,
            ]);
        } else {
            if (!empty($validated['avatar'])) $user->avatar = $validated['avatar'];
            if (!empty($validated['name'])) $user->name = $validated['name'];
            if (!empty($validated['googleId'])) $user->google_id = $validated['googleId'];
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'token' => 'auth_token_' . $user->id . '_' . Str::random(16),
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'birthdate' => $user->birthdate,
                'avatar' => $user->avatar,
                'isVerified' => $user->is_verified,
                'tokens' => $user->tokens,
                'credits' => $user->credits,
                'totalTopUpTokens' => $user->total_topup_tokens,
                'totalCreditsEarned' => $user->total_credits_earned,
                'expPoints' => $user->exp_points,
                'level' => $user->level,
                'isLoggedIn' => true,
                'hasCompletedOnboarding' => !empty($user->gender) && !empty($user->birthdate),
            ]
        ]);
    }

    public function emailLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|string',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => explode('@', $validated['email'])[0],
                'tokens' => 350,
                'credits' => 0,
                'gender' => 'male',
                'is_verified' => false,
            ]
        );

        return response()->json([
            'status' => 'success',
            'token' => 'auth_token_' . $user->id . '_' . Str::random(16),
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'birthdate' => $user->birthdate,
                'avatar' => $user->avatar,
                'isVerified' => $user->is_verified,
                'tokens' => $user->tokens,
                'credits' => $user->credits,
                'totalTopUpTokens' => $user->total_topup_tokens,
                'totalCreditsEarned' => $user->total_credits_earned,
                'expPoints' => $user->exp_points,
                'level' => $user->level,
                'isLoggedIn' => true,
                'hasCompletedOnboarding' => !empty($user->gender) && !empty($user->birthdate),
            ]
        ]);
    }
}
