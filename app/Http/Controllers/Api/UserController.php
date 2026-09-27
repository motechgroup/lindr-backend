<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
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

    public function profile(Request $request)
    {
        $userId = $request->header('X-User-Id') ?? $request->query('userId');
        $email = $request->query('email');

        $user = null;
        if (!empty($userId)) {
            $user = User::find($userId);
        }
        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $this->touchHeartbeat($user);

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
                'isVerified' => (bool) $user->is_verified,
                'tokens' => (int) $user->tokens,
                'credits' => (int) $user->credits,
                'totalTopUpTokens' => (int) $user->total_topup_tokens,
                'totalCreditsEarned' => (int) $user->total_credits_earned,
                'expPoints' => (int) $user->exp_points,
                'level' => (int) ($user->level ?? 1),
                'isLoggedIn' => true,
                'hasCompletedOnboarding' => true,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $userId = $request->header('X-User-Id') ?? $request->input('userId');
        $email = $request->input('email');

        $user = null;
        if (!empty($userId)) {
            $user = User::find($userId);
        }
        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        if ($request->has('name') && !empty($request->input('name'))) {
            $user->name = $request->input('name');
        }
        if ($request->has('avatar') && !empty($request->input('avatar'))) {
            $user->avatar = $request->input('avatar');
        }
        if ($request->has('gender') && !empty($request->input('gender'))) {
            $user->gender = $request->input('gender');
        }
        if ($request->has('birthdate') && !empty($request->input('birthdate'))) {
            $user->birthdate = $request->input('birthdate');
        }

        $this->touchHeartbeat($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
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
            'name' => 'nullable|string',
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
                'tokens' => 0,
                'credits' => 0,
                'is_verified' => false,
            ]);
        } else {
            $user->gender = $validated['gender'];
            $user->birthdate = $validated['birthdate'];
            if (!empty($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (!empty($validated['avatar'])) {
                $isCurrentCustom = !empty($user->avatar) && !str_contains($user->avatar, 'unsplash.com');
                $isNewUnsplash = str_contains($validated['avatar'], 'unsplash.com');
                if (!$isCurrentCustom || !$isNewUnsplash) {
                    $user->avatar = $validated['avatar'];
                }
            }
            if (!empty($validated['countryCode'])) {
                $user->country_code = $validated['countryCode'];
                $user->country_name = $validated['countryName'] ?? ($validated['countryCode'] === 'KE' ? 'Kenya' : 'International');
            }
            $user->save();
        }

        $this->touchHeartbeat($user);

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

    public function heartbeat(Request $request)
    {
        $userId = $request->header('X-User-Id') ?? $request->query('userId') ?? $request->input('userId');
        if (!empty($userId)) {
            $user = User::find($userId);
            if ($user) {
                $this->touchHeartbeat($user);
                return response()->json(['status' => 'success', 'timestamp' => now()->timestamp]);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
    }

    public function oppositeGender(Request $request)
    {
        $gender = strtolower($request->query('gender', 'male'));
        $currentUserId = $request->header('X-User-Id') ?? $request->query('userId');

        if (!empty($currentUserId)) {
            $currentUser = User::find($currentUserId);
            if ($currentUser) {
                $this->touchHeartbeat($currentUser);
                if (!empty($currentUser->gender) && in_array(strtolower($currentUser->gender), ['male', 'female'])) {
                    $gender = strtolower($currentUser->gender);
                }
            }
        }

        // Target gender is strictly female if current user is male, and male if female
        $targetGender = $gender === 'female' ? 'male' : 'female';

        // Strict online threshold: User MUST have sent an active app heartbeat within the last 300 seconds (5 minutes)
        $onlineCutoff = now()->subSeconds(300);

        $query = User::where('is_admin', false)
            ->whereRaw('LOWER(gender) = ?', [strtolower($targetGender)]);

        $countryCodeFilter = $request->query('countryCode') ?? $request->query('country');
        if (!empty($countryCodeFilter) && strtoupper($countryCodeFilter) !== 'ALL' && strtoupper($countryCodeFilter) !== 'GLOBAL') {
            $query->where(function($q) use ($countryCodeFilter) {
                $q->whereRaw('LOWER(country_code) = ?', [strtolower($countryCodeFilter)])
                  ->orWhereRaw('LOWER(country_name) = ?', [strtolower($countryCodeFilter)]);
            });
        }

        if (!empty($currentUserId)) {
            $query->where('id', '!=', $currentUserId);
        }

        $users = $query->latest('updated_at')->get()
            ->map(function($u) use ($onlineCutoff) {
                $country = $u->country_name ?? 'Kenya';
                $countryCode = $u->country_code ?? 'KE';
                $flag = $this->getCountryFlag($countryCode, $country);
                $hbTime = $u->last_heartbeat_at ?? $u->updated_at;
                $lastHb = $hbTime ? \Carbon\Carbon::parse($hbTime) : null;
                $isOnline = $lastHb ? $lastHb->gte($onlineCutoff) : false;

                return [
                    'id' => (string) $u->id,
                    'name' => $u->name,
                    'gender' => $u->gender,
                    'age' => $u->birthdate ? date_diff(date_create($u->birthdate), date_create('today'))->y : 22,
                    'avatar' => $u->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                    'country' => $country,
                    'countryCode' => $countryCode,
                    'flag' => $flag,
                    'isOnline' => $isOnline,
                    'status' => $isOnline ? 'online' : 'offline',
                    'isVerified' => (bool) $u->is_verified,
                    'level' => $u->level ?? 1,
                    'bio' => 'Ready to connect and video call on Lindr ✨',
                    'followingCount' => 0,
                    'fansCount' => 0,
                    'friendsCount' => 0,
                    'photos' => [],
                ];
            })
            ->sortByDesc('isOnline')
            ->values();

        return response()->json([
            'status' => 'success',
            'users' => $users
        ]);
    }

    private function getCountryFlag($code, $name = '')
    {
        $c = strtoupper(trim($code ?? ''));
        if ($c === 'KE' || strtolower($name) === 'kenya') return '🇰🇪';
        if ($c === 'US' || strtolower($name) === 'united states' || strtolower($name) === 'usa') return '🇺🇸';
        if ($c === 'GB' || strtolower($name) === 'united kingdom' || strtolower($name) === 'uk') return '🇬🇧';
        if ($c === 'NG' || strtolower($name) === 'nigeria') return '🇳🇬';
        if ($c === 'ZA' || strtolower($name) === 'south africa') return '🇿🇦';
        if ($c === 'TZ' || strtolower($name) === 'tanzania') return '🇹🇿';
        if ($c === 'UG' || strtolower($name) === 'uganda') return '🇺🇬';
        if ($c === 'IN' || strtolower($name) === 'india') return '🇮🇳';
        if ($c === 'CA' || strtolower($name) === 'canada') return '🇨🇦';
        if ($c === 'AU' || strtolower($name) === 'australia') return '🇦🇺';
        if ($c === 'BR' || strtolower($name) === 'brazil') return '🇧🇷';
        if ($c === 'ES' || strtolower($name) === 'spain') return '🇪🇸';
        if ($c === 'KR' || strtolower($name) === 'south korea') return '🇰🇷';
        if ($c === 'DE' || strtolower($name) === 'germany') return '🇩🇪';
        if ($c === 'FR' || strtolower($name) === 'france') return '🇫🇷';

        if (strlen($c) === 2 && ctype_alpha($c)) {
            $char1 = mb_chr(ord($c[0]) + 127397, 'UTF-8');
            $char2 = mb_chr(ord($c[1]) + 127397, 'UTF-8');
            return $char1 . $char2;
        }
        return '🌐';
    }

    public function availableCountries(Request $request)
    {
        $feeSetting = \App\Models\SystemSetting::where('key', 'country_filter_token_fee')->first();
        $tokenFee = $feeSetting ? (int)$feeSetting->value : 5;

        $rawCountries = User::where('is_admin', false)
            ->whereNotNull('country_code')
            ->select('country_code', 'country_name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as user_count'))
            ->groupBy('country_code', 'country_name')
            ->orderBy('user_count', 'desc')
            ->get();

        $countriesList = [
            [
                'code' => 'ALL',
                'name' => 'All',
                'flag' => '🌐',
                'userCount' => User::where('is_admin', false)->count(),
                'tokenFee' => 0,
            ]
        ];

        foreach ($rawCountries as $c) {
            $code = strtoupper(trim($c->country_code ?? 'KE'));
            $name = $c->country_name ?? ($code === 'KE' ? 'Kenya' : $code);
            $flag = $this->getCountryFlag($code, $name);

            $countriesList[] = [
                'code' => $code,
                'name' => $name,
                'flag' => $flag,
                'userCount' => (int)$c->user_count,
                'tokenFee' => $tokenFee,
            ];
        }

        return response()->json([
            'status' => 'success',
            'countryFilterTokenFee' => $tokenFee,
            'countries' => $countriesList
        ]);
    }

    public function filterCountry(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'nullable|string',
            'countryCode' => 'required|string',
        ]);

        $userId = $validated['userId'] ?? $request->header('X-User-Id');
        if (empty($userId)) {
            return response()->json(['status' => 'error', 'message' => 'User ID is required'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $countryCode = strtoupper(trim($validated['countryCode']));

        if ($countryCode === 'ALL' || $countryCode === 'GLOBAL') {
            return response()->json([
                'status' => 'success',
                'message' => 'Global country filter active (Free)',
                'countryCode' => 'ALL',
                'tokensDeducted' => 0,
                'remainingTokens' => (int) $user->tokens,
            ]);
        }

        $feeSetting = \App\Models\SystemSetting::where('key', 'country_filter_token_fee')->first();
        $fee = $feeSetting ? (int)$feeSetting->value : 5;

        if ($fee > 0) {
            if ($user->tokens < $fee) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient tokens. Filtering users by country requires {$fee} tokens.",
                    'requiredTokens' => $fee,
                    'currentTokens' => (int) $user->tokens,
                ], 400);
            }

            $user->tokens -= $fee;
            $user->save();

            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'type' => 'country_filter',
                'amount_tokens' => -$fee,
                'amount_credits' => 0,
                'amount_usd' => 0,
                'payment_provider' => 'system',
                'reference' => "country_filter_{$countryCode}_" . \Illuminate\Support\Str::random(8),
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Country filter unlocked for {$countryCode}",
            'countryCode' => $countryCode,
            'tokensDeducted' => $fee,
            'remainingTokens' => (int) $user->tokens,
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
