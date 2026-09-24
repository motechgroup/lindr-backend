<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\GatewayController;
use App\Http\Controllers\Admin\TokenPackageController;
use App\Http\Controllers\Admin\GitController;

use App\Http\Controllers\Api\AuthController as ApiAuth;
use App\Http\Controllers\Api\UserController as ApiUser;
use App\Http\Controllers\Api\WalletController as ApiWallet;
use App\Http\Controllers\Api\AgoraController as ApiAgora;
use App\Http\Controllers\Api\InteractionController as ApiInteraction;
use App\Http\Middleware\AdminMiddleware;
use App\Models\TokenPackage;

/*
|--------------------------------------------------------------------------
| Public App Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/privacy-policy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/privacy', [LegalController::class, 'privacy']);
Route::get('/terms-of-service', [LegalController::class, 'terms'])->name('terms');
Route::get('/terms', [LegalController::class, 'terms']);

// Google OAuth Redirect Handler for Expo App Session
$googleCallbackHandler = function () {
    return response('<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting to Lindr...</title>
    <script>
        window.onload = function() {
            var hash = window.location.hash || "";
            var search = window.location.search || "";
            var target = "lindrapp://redirect" + hash + (hash ? "" : search);
            window.location.href = target;
        };
    </script>
</head>
<body style="background:#0F0C20; color:#FFF; font-family:sans-serif; text-align:center; padding-top:20%;">
    <h2 style="color:#FF2D55;">Authentication Successful</h2>
    <p style="color:#B0B0C3;">Redirecting back to Lindr App...</p>
</body>
</html>', 200, ['Content-Type' => 'text/html']);
};

Route::get('/api/v1/auth/google/callback', $googleCallbackHandler);
Route::get('/api/auth/google/callback', $googleCallbackHandler);
Route::get('/v1/auth/google/callback', $googleCallbackHandler);
Route::get('/auth/google/callback', $googleCallbackHandler);
Route::any('{any}', $googleCallbackHandler)->where('any', '.*google/callback.*');

/*
|--------------------------------------------------------------------------
| Admin Portal Sign In (/access)
|--------------------------------------------------------------------------
*/
Route::get('/access', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/access', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Dashboard & Management Routes (Protected by AdminMiddleware)
|--------------------------------------------------------------------------
*/
Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users/{id}/balance', [UserController::class, 'updateBalance'])->name('admin.users.balance');
    Route::post('/users/{id}/verify', [UserController::class, 'toggleVerify'])->name('admin.users.verify');

    // Video Calls Logs
    Route::get('/calls', [CallController::class, 'index'])->name('admin.calls');

    // Cashouts & Withdrawals
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('admin.withdrawals');
    Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');

    // Financial Transactions Ledger
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions');

    // Chat & Gift Moderation Logs
    Route::get('/chats', [ChatController::class, 'index'])->name('admin.chats');

    // Biometric Verifications Queue
    Route::get('/verifications', [VerificationController::class, 'index'])->name('admin.verifications');
    Route::post('/verifications/{id}/approve', [VerificationController::class, 'approve'])->name('admin.verifications.approve');

    // Task Rewards System
    Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks');

    // Payment Gateways & Google Auth Credentials
    Route::get('/gateways', [GatewayController::class, 'index'])->name('admin.gateways');
    Route::post('/gateways', [GatewayController::class, 'update'])->name('admin.gateways.update');

    // Token Packages Management
    Route::get('/tokens', [TokenPackageController::class, 'index'])->name('admin.tokens');
    Route::post('/tokens', [TokenPackageController::class, 'store'])->name('admin.tokens.store');
    Route::post('/tokens/{id}/update', [TokenPackageController::class, 'update'])->name('admin.tokens.update');
    Route::post('/tokens/{id}/toggle', [TokenPackageController::class, 'toggle'])->name('admin.tokens.toggle');
    Route::delete('/tokens/{id}', [TokenPackageController::class, 'destroy'])->name('admin.tokens.destroy');

    // System Settings & Rates
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // Git Deployment & Version Control Management
    Route::get('/git', [GitController::class, 'index'])->name('admin.git');
    Route::post('/git/pull', [GitController::class, 'pull'])->name('admin.git.pull');
    Route::post('/git/migrate', [GitController::class, 'migrate'])->name('admin.git.migrate');
    Route::post('/git/clear-cache', [GitController::class, 'clearCache'])->name('admin.git.clear-cache');
    Route::post('/git/deploy', [GitController::class, 'deploy'])->name('admin.git.deploy');
});

/*
|--------------------------------------------------------------------------
| Mobile App API Routes (/api/...)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/v1/auth/google/callback', function () {
        return response('<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting to Lindr...</title>
    <script>
        window.onload = function() {
            var hash = window.location.hash || "";
            var search = window.location.search || "";
            var target = "lindrapp://redirect" + hash + (hash ? "" : search);
            window.location.href = target;
        };
    </script>
</head>
<body style="background:#0F0C20; color:#FFF; font-family:sans-serif; text-align:center; padding-top:20%;">
    <h2 style="color:#FF2D55;">Authentication Successful</h2>
    <p style="color:#B0B0C3;">Redirecting back to Lindr App...</p>
</body>
</html>', 200, ['Content-Type' => 'text/html']);
    });
    Route::get('/auth/google/callback', function () {
        return redirect('/api/v1/auth/google/callback');
    });

    Route::post('/auth/google', [ApiAuth::class, 'googleLogin']);
    Route::post('/auth/email', [ApiAuth::class, 'emailLogin']);
    
    Route::get('/user/profile', [ApiUser::class, 'profile']);
    Route::post('/user/onboarding', [ApiUser::class, 'onboarding']);
    Route::get('/users/opposite', [ApiUser::class, 'oppositeGender']);
    Route::post('/user/verification', [ApiUser::class, 'submitVerification']);
    Route::post('/user/task/claim', [ApiUser::class, 'claimTask']);
    
    Route::post('/wallet/topup', [ApiWallet::class, 'topup']);
    Route::post('/wallet/cashout', [ApiWallet::class, 'cashout']);
    
    Route::post('/calls/log', [ApiInteraction::class, 'logCall']);
    Route::post('/chats/send', [ApiInteraction::class, 'sendMessage']);
    
    Route::get('/tokens/packages', function () {
        return response()->json([
            'status' => 'success',
            'packages' => TokenPackage::where('is_active', true)->orderBy('tokens', 'asc')->get()
        ]);
    });

    Route::post('/agora/token', [ApiAgora::class, 'generateToken']);
});
