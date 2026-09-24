<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $userId = $request->header('X-User-Id');
        $user = User::find($userId) ?? User::first();

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'birthdate' => $user->birthdate,
                'avatar' => $user->avatar,
                'countryCode' => $user->country_code ?? 'KE',
                'countryName' => $user->country_name ?? 'Kenya',
                'isVerified' => $user->is_verified,
                'tokens' => $user->tokens,
                'credits' => $user->credits,
                'totalTopUpTokens' => $user->total_topup_tokens,
                'totalCreditsEarned' => $user->total_credits_earned,
                'expPoints' => $user->exp_points,
                'level' => $user->level,
                'isLoggedIn' => true,
                'hasCompletedOnboarding' => true,
            ]
        ]);
    }

    public function onboarding(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|string',
            'name' => 'required|string',
            'avatar' => 'nullable|string',
            'countryCode' => 'nullable|string',
            'countryName' => 'nullable|string',
        ]);

        $userId = $validated['userId'] ?? $request->header('X-User-Id');
        $email = $request->input('email');

        $user = null;
        if (!empty($userId)) {
            $user = User::find($userId);
        }
        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
        }
        if (!$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $email ?? ('user_' . \Illuminate\Support\Str::random(6) . '@lindr.app'),
                'gender' => $validated['gender'],
                'birthdate' => $validated['birthdate'],
                'avatar' => $validated['avatar'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                'country_code' => $validated['countryCode'] ?? 'KE',
                'country_name' => $validated['countryName'] ?? 'Kenya',
                'tokens' => 350,
                'credits' => 0,
                'is_verified' => false,
            ]);
        } else {
            $user->gender = $validated['gender'];
            $user->birthdate = $validated['birthdate'];
            $user->name = $validated['name'];
            if (!empty($validated['avatar'])) {
                $user->avatar = $validated['avatar'];
            }
            if (!empty($validated['countryCode'])) {
                $user->country_code = $validated['countryCode'];
                $user->country_name = $validated['countryName'] ?? ($validated['countryCode'] === 'KE' ? 'Kenya' : 'International');
            }
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Onboarding completed successfully',
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'birthdate' => $user->birthdate,
                'avatar' => $user->avatar,
                'countryCode' => $user->country_code ?? 'KE',
                'countryName' => $user->country_name ?? 'Kenya',
                'isVerified' => $user->is_verified,
                'tokens' => $user->tokens,
                'credits' => $user->credits,
                'totalTopUpTokens' => $user->total_topup_tokens,
                'totalCreditsEarned' => $user->total_credits_earned,
                'expPoints' => $user->exp_points,
                'level' => $user->level,
                'isLoggedIn' => true,
                'hasCompletedOnboarding' => true,
            ]
        ]);
    }

    public function oppositeGender(Request $request)
    {
        $gender = $request->query('gender', 'male');
        $targetGender = $gender === 'male' ? 'female' : 'male';

        $users = User::where('gender', $targetGender)
            ->where('is_admin', false)
            ->get()
            ->map(function($u) {
                return [
                    'id' => (string) $u->id,
                    'name' => $u->name,
                    'gender' => $u->gender,
                    'age' => $u->birthdate ? date_diff(date_create($u->birthdate), date_create('today'))->y : 22,
                    'avatar' => $u->avatar,
                    'isOnline' => true,
                    'isVerified' => $u->is_verified,
                    'level' => $u->level,
                    'bio' => 'Ready to connect and video call on Lindr ✨',
                ];
            });

        return response()->json([
            'status' => 'success',
            'users' => $users
        ]);
    }

    public function submitVerification(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'stepsCompleted' => 'required|array',
        ]);

        $user = User::findOrFail($validated['userId']);
        $user->is_verified = true;
        $user->exp_points += 300;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Biometric verification approved',
            'isVerified' => true,
            'expPoints' => $user->exp_points
        ]);
    }

    public function claimTask(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'taskId' => 'required|string',
            'rewardExp' => 'required|integer',
            'rewardCredits' => 'required|integer',
        ]);

        $user = User::findOrFail($validated['userId']);
        $user->exp_points += $validated['rewardExp'];
        $user->credits += $validated['rewardCredits'];
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Task reward claimed successfully',
            'credits' => $user->credits,
            'expPoints' => $user->exp_points
        ]);
    }
}
