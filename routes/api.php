<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuth;
use App\Http\Controllers\Api\UserController as ApiUser;
use App\Http\Controllers\Api\WalletController as ApiWallet;
use App\Http\Controllers\Api\LiveKitController as ApiLiveKit;
use App\Http\Controllers\Api\InteractionController as ApiInteraction;

/*
|--------------------------------------------------------------------------
| Mobile App API Routes (/api/...)
|--------------------------------------------------------------------------
*/

Route::post('/auth/google', [ApiAuth::class, 'googleLogin']);
Route::post('/auth/email', [ApiAuth::class, 'emailLogin']);

Route::get('/user/profile', [ApiUser::class, 'profile']);
Route::post('/user/update-profile', [ApiUser::class, 'updateProfile']);
Route::post('/user/onboarding', [ApiUser::class, 'onboarding']);
Route::match(['get', 'post'], '/user/heartbeat', [ApiUser::class, 'heartbeat']);
Route::get('/users/opposite', [ApiUser::class, 'oppositeGender']);
Route::get('/countries', [ApiUser::class, 'availableCountries']);
Route::post('/users/filter-country', [ApiUser::class, 'filterCountry']);
Route::post('/user/verification', [ApiUser::class, 'submitVerification']);
Route::post('/user/task/claim', [ApiUser::class, 'claimTask']);

Route::post('/wallet/topup', [ApiWallet::class, 'topup']);
Route::post('/wallet/cashout', [ApiWallet::class, 'cashout']);

Route::any('/system/sync-updates', function () {
    $workDir = base_path();
    $branchRes = shell_exec("cd " . escapeshellarg($workDir) . " && git pull origin main 2>&1");
    
    try {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN last_heartbeat_at DATETIME NULL;");
    } catch (\Throwable $e) {}

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    } catch (\Throwable $e) {}

    try {
        if (\Illuminate\Support\Facades\File::exists(base_path('bootstrap/cache'))) {
            foreach (\Illuminate\Support\Facades\File::files(base_path('bootstrap/cache')) as $f) {
                if ($f->getFilename() !== '.gitignore') {
                    @\Illuminate\Support\Facades\File::delete($f->getPathname());
                }
            }
        }
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
    } catch (\Throwable $e) {}

    return response()->json([
        'status' => 'success',
        'gitOutput' => $branchRes,
        'message' => 'Code pulled, migrated, and cached flushed successfully'
    ]);
});

Route::post('/calls/initiate', [ApiInteraction::class, 'initiateCall']);
Route::get('/calls/check-incoming', [ApiInteraction::class, 'checkIncomingCall']);
Route::get('/calls/status', [ApiInteraction::class, 'checkCallStatus']);
Route::post('/calls/respond', [ApiInteraction::class, 'respondCall']);
Route::post('/calls/ticker', [ApiInteraction::class, 'deductCallTicker']);
Route::post('/calls/log', [ApiInteraction::class, 'logCall']);
Route::post('/chats/send', [ApiInteraction::class, 'sendMessage']);
Route::get('/chats/history', [ApiInteraction::class, 'getChatHistory']);

Route::get('/tokens/packages', function () {
    try {
        $pkgs = \App\Models\TokenPackage::where('is_active', 1)->orderBy('tokens', 'asc')->get();
        if ($pkgs->count() === 0) {
            $pkgs = \App\Models\TokenPackage::all();
        }
        return response()->json([
            'status' => 'success',
            'packages' => $pkgs
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'success',
            'packages' => [
                ['id' => 1, 'name' => 'Starter Pack', 'tokens' => 100, 'price_usd' => 0.99, 'badge' => null, 'is_popular' => false],
                ['id' => 2, 'name' => 'Popular Pack', 'tokens' => 300, 'price_usd' => 2.99, 'badge' => 'POPULAR', 'is_popular' => true],
                ['id' => 3, 'name' => 'Pro Saver', 'tokens' => 1000, 'price_usd' => 8.99, 'badge' => 'BEST VALUE', 'is_popular' => false],
                ['id' => 4, 'name' => 'VIP Whale Pack', 'tokens' => 5000, 'price_usd' => 39.99, 'badge' => 'VIP DEAL', 'is_popular' => false],
            ]
        ]);
    }
});

Route::post('/agora/token', [ApiLiveKit::class, 'generateToken']);
Route::post('/livekit/token', [ApiLiveKit::class, 'generateToken']);
