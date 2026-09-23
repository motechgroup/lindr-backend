<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\CallSession;
use App\Models\ChatMessage;
use App\Models\UserTask;
use App\Models\SystemSetting;
use App\Models\TokenPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@lindr.app'],
            [
                'name' => 'Lindr Admin',
                'password' => Hash::make('Admin@Lindr2026!'),
                'is_admin' => true,
                'gender' => 'male',
                'tokens' => 999999,
                'credits' => 999999,
                'is_verified' => true,
            ]
        );

        // 2. Male Demo User
        $alex = User::updateOrCreate(
            ['email' => 'alex.demo@lindr.app'],
            [
                'name' => 'Alex Mercer',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'gender' => 'male',
                'birthdate' => '1998-05-15',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80',
                'tokens' => 750,
                'credits' => 0,
                'total_topup_tokens' => 5500,
                'level' => 3,
                'is_verified' => true,
            ]
        );

        // 3. Female Demo Users
        $sophia = User::updateOrCreate(
            ['email' => 'sophia@lindr.app'],
            [
                'name' => 'Sophia Vance',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'gender' => 'female',
                'birthdate' => '2001-08-22',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'tokens' => 50,
                'credits' => 1250,
                'total_credits_earned' => 1250,
                'exp_points' => 1100,
                'level' => 4,
                'is_verified' => true,
            ]
        );

        $elena = User::updateOrCreate(
            ['email' => 'elena@lindr.app'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'gender' => 'female',
                'birthdate' => '1999-11-04',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80',
                'tokens' => 120,
                'credits' => 850,
                'total_credits_earned' => 850,
                'exp_points' => 800,
                'level' => 3,
                'is_verified' => true,
            ]
        );

        // 4. Seed Call Sessions
        CallSession::create([
            'caller_id' => $alex->id,
            'receiver_id' => $sophia->id,
            'channel_name' => 'channel_alex_sophia_882',
            'duration_seconds' => 480,
            'tokens_spent' => 48,
            'credits_earned' => 32,
            'status' => 'ended',
        ]);

        // 5. Seed Chat Messages & Gifts
        ChatMessage::create([
            'sender_id' => $sophia->id,
            'receiver_id' => $alex->id,
            'message_text' => 'Hey Alex! Loved your profile music! 🎵',
            'tokens_spent' => 0,
        ]);

        // 6. Seed Transactions
        Transaction::create([
            'user_id' => $alex->id,
            'type' => 'topup',
            'amount_tokens' => 5000,
            'amount_usd' => 49.99,
            'payment_provider' => 'flutterwave',
            'reference' => 'FLW_REF_' . rand(100000, 999999),
            'status' => 'completed',
        ]);

        // 7. Seed Pending Cashout
        Withdrawal::create([
            'user_id' => $sophia->id,
            'credits_amount' => 1000,
            'amount_usd' => 10.00,
            'payment_method' => 'mpesa',
            'account_details' => '+254712345678 (Sophia Vance)',
            'status' => 'pending',
        ]);

        // 8. Seed Creator Tasks
        UserTask::create([
            'user_id' => $sophia->id,
            'title' => 'Upload 5 Profile Photos',
            'reward_exp' => 150,
            'reward_credits' => 50,
            'is_completed' => true,
            'progress_text' => '5/5 Photos Uploaded',
            'icon' => 'images',
        ]);

        // 9. Seed Token Packages
        $packages = [
            ['name' => 'Starter Pack', 'tokens' => 100, 'price_usd' => 0.99, 'badge' => null, 'is_popular' => false, 'is_active' => true],
            ['name' => 'Popular Pack', 'tokens' => 300, 'price_usd' => 2.99, 'badge' => 'POPULAR', 'is_popular' => true, 'is_active' => true],
            ['name' => 'Pro Saver', 'tokens' => 1000, 'price_usd' => 8.99, 'badge' => 'BEST VALUE', 'is_popular' => false, 'is_active' => true],
            ['name' => 'VIP Whale Pack', 'tokens' => 5000, 'price_usd' => 39.99, 'badge' => 'VIP DEAL', 'is_popular' => false, 'is_active' => true],
        ];

        foreach ($packages as $pkg) {
            TokenPackage::updateOrCreate(['name' => $pkg['name']], $pkg);
        }

        // 10. Seed System Settings
        $settings = [
            // Payment Gateway Credentials
            ['key' => 'flw_public_key', 'value' => env('FLW_PUBLIC_KEY', 'your_flw_public_key'), 'category' => 'gateways', 'description' => 'Flutterwave Public API Key'],
            ['key' => 'flw_secret_key', 'value' => env('FLW_SECRET_KEY', 'your_flw_secret_key'), 'category' => 'gateways', 'description' => 'Flutterwave Secret API Key'],
            ['key' => 'flw_secret_hash', 'value' => env('FLW_SECRET_HASH', 'your_flw_secret_hash'), 'category' => 'gateways', 'description' => 'Flutterwave Webhook Secret Hash'],

            ['key' => 'mpesa_consumer_key', 'value' => env('MPESA_CONSUMER_KEY', 'your_mpesa_consumer_key'), 'category' => 'gateways', 'description' => 'M-Pesa Consumer Key'],
            ['key' => 'mpesa_consumer_secret', 'value' => env('MPESA_CONSUMER_SECRET', 'your_mpesa_consumer_secret'), 'category' => 'gateways', 'description' => 'M-Pesa Consumer Secret'],
            ['key' => 'mpesa_shortcode', 'value' => env('MPESA_SHORTCODE', '174379'), 'category' => 'gateways', 'description' => 'M-Pesa Paybill / Shortcode'],
            ['key' => 'mpesa_passkey', 'value' => env('MPESA_PASSKEY', 'your_mpesa_passkey'), 'category' => 'gateways', 'description' => 'M-Pesa Online Passkey'],
            ['key' => 'mpesa_callback_url', 'value' => env('MPESA_CALLBACK_URL', 'https://lindrapp.top/api/v1/webhooks/mpesa'), 'category' => 'gateways', 'description' => 'M-Pesa Instant Callback URL'],

            // Google OAuth Credentials
            ['key' => 'google_web_client_id', 'value' => 'your-google-web-client-id.apps.googleusercontent.com', 'category' => 'google', 'description' => 'Google Web OAuth Client ID'],
            ['key' => 'google_ios_client_id', 'value' => 'your-google-ios-client-id.apps.googleusercontent.com', 'category' => 'google', 'description' => 'Google iOS Client ID'],
            ['key' => 'google_android_client_id', 'value' => 'your-google-android-client-id.apps.googleusercontent.com', 'category' => 'google', 'description' => 'Google Android Client ID'],

            // Video Call Cost Rates (Tokens / Min for Male Levels)
            ['key' => 'male_call_rate_level1', 'value' => '60', 'category' => 'call_rates', 'description' => 'Level 1 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level2', 'value' => '50', 'category' => 'call_rates', 'description' => 'Level 2 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level3', 'value' => '40', 'category' => 'call_rates', 'description' => 'Level 3 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level4', 'value' => '30', 'category' => 'call_rates', 'description' => 'Level 4 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level5', 'value' => '20', 'category' => 'call_rates', 'description' => 'Level 5 Male Video Call Cost (Tokens/min)'],

            // Chat & Messaging Costs
            ['key' => 'chat_text_message_cost', 'value' => '2', 'category' => 'chat_rates', 'description' => 'Text Message Cost (Tokens per message)'],
            ['key' => 'chat_media_message_cost', 'value' => '5', 'category' => 'chat_rates', 'description' => 'Image / Voice Note Message Cost (Tokens)'],

            // Female Creator Payout & Split Rates
            ['key' => 'creator_payout_split_percentage', 'value' => '70', 'category' => 'payouts', 'description' => 'Female Creator Payout Split (% of call revenue)'],
            ['key' => 'female_pay_rate_level1', 'value' => '20', 'category' => 'payouts', 'description' => 'Level 1 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'female_pay_rate_level2', 'value' => '25', 'category' => 'payouts', 'description' => 'Level 2 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'female_pay_rate_level3', 'value' => '30', 'category' => 'payouts', 'description' => 'Level 3 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'female_pay_rate_level4', 'value' => '40', 'category' => 'payouts', 'description' => 'Level 4 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'minimum_cashout_credits', 'value' => '500', 'category' => 'payouts', 'description' => 'Minimum Credits Balance Required for Cashout'],
            ['key' => 'credits_per_usd', 'value' => '100', 'category' => 'payouts', 'description' => 'Credit-to-USD Exchange Rate (100 Credits = $1.00 USD)'],

            // Video Call Engine & Agora Config
            ['key' => 'agora_app_id', 'value' => '7f7547aa4508451bb0dcd38612ba5c35', 'category' => 'agora', 'description' => 'Agora RTC App ID'],
            ['key' => 'max_call_duration_minutes', 'value' => '60', 'category' => 'agora', 'description' => 'Maximum Continuous Video Call Duration (Mins)'],

            // App Rules & Bonus Settings
            ['key' => 'new_user_welcome_tokens', 'value' => '350', 'category' => 'app_rules', 'description' => 'Welcome Gift Tokens for New Male Users'],
            ['key' => 'biometric_verification_required', 'value' => '1', 'category' => 'app_rules', 'description' => 'Require 4-Step Verification Before Video Calling (1=Yes, 0=No)'],
            ['key' => 'daily_login_bonus_exp', 'value' => '50', 'category' => 'app_rules', 'description' => 'Daily Login Bonus EXP Points'],
        ];

        foreach ($settings as $s) {
            SystemSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
