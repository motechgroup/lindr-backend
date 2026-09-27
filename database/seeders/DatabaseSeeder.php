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
        // 2. 20 Diverse Mock Users (Male & Female) from Various Countries
        $mockUsers = [
            // Females
            [
                'email' => 'jane@gmail.com',
                'name' => 'Jane Wanjiku',
                'gender' => 'female',
                'birthdate' => '2001-05-14',
                'country_code' => 'KE',
                'country_name' => 'Kenya',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 450,
                'tokens' => 120,
            ],
            [
                'email' => 'chloe@gmail.com',
                'name' => 'Chloe Miller',
                'gender' => 'female',
                'birthdate' => '2002-08-20',
                'country_code' => 'US',
                'country_name' => 'United States',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80',
                'level' => 3,
                'is_verified' => true,
                'credits' => 900,
                'tokens' => 300,
            ],
            [
                'email' => 'sophia@gmail.com',
                'name' => 'Sophia Taylor',
                'gender' => 'female',
                'birthdate' => '2000-11-12',
                'country_code' => 'GB',
                'country_name' => 'United Kingdom',
                'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80',
                'level' => 1,
                'is_verified' => false,
                'credits' => 150,
                'tokens' => 50,
            ],
            [
                'email' => 'aisha@gmail.com',
                'name' => 'Aisha Bello',
                'gender' => 'female',
                'birthdate' => '1999-03-25',
                'country_code' => 'NG',
                'country_name' => 'Nigeria',
                'avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 600,
                'tokens' => 180,
            ],
            [
                'email' => 'zuri@gmail.com',
                'name' => 'Zuri Nkosi',
                'gender' => 'female',
                'birthdate' => '2003-01-10',
                'country_code' => 'ZA',
                'country_name' => 'South Africa',
                'avatar' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=600&q=80',
                'level' => 1,
                'is_verified' => false,
                'credits' => 200,
                'tokens' => 80,
            ],
            [
                'email' => 'meiling@gmail.com',
                'name' => 'Mei Ling',
                'gender' => 'female',
                'birthdate' => '2001-09-18',
                'country_code' => 'KR',
                'country_name' => 'South Korea',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=600&q=80',
                'level' => 4,
                'is_verified' => true,
                'credits' => 1250,
                'tokens' => 400,
            ],
            [
                'email' => 'priya@gmail.com',
                'name' => 'Priya Sharma',
                'gender' => 'female',
                'birthdate' => '1998-07-04',
                'country_code' => 'IN',
                'country_name' => 'India',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 500,
                'tokens' => 150,
            ],
            [
                'email' => 'elena@gmail.com',
                'name' => 'Elena Rossi',
                'gender' => 'female',
                'birthdate' => '2000-02-14',
                'country_code' => 'ES',
                'country_name' => 'Spain',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80',
                'level' => 3,
                'is_verified' => true,
                'credits' => 850,
                'tokens' => 250,
            ],
            [
                'email' => 'camila@gmail.com',
                'name' => 'Camila Silva',
                'gender' => 'female',
                'birthdate' => '2002-12-30',
                'country_code' => 'BR',
                'country_name' => 'Brazil',
                'avatar' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=600&q=80',
                'level' => 1,
                'is_verified' => false,
                'credits' => 100,
                'tokens' => 40,
            ],
            [
                'email' => 'amara@gmail.com',
                'name' => 'Amara Mwangi',
                'gender' => 'female',
                'birthdate' => '1999-06-22',
                'country_code' => 'TZ',
                'country_name' => 'Tanzania',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 400,
                'tokens' => 110,
            ],

            // Males
            [
                'email' => 'dan@gmail.com',
                'name' => 'Dan Otieno',
                'gender' => 'male',
                'birthdate' => '1999-10-15',
                'country_code' => 'KE',
                'country_name' => 'Kenya',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 650,
            ],
            [
                'email' => 'david@gmail.com',
                'name' => 'David Smith',
                'gender' => 'male',
                'birthdate' => '1998-04-12',
                'country_code' => 'US',
                'country_name' => 'United States',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                'level' => 3,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 1200,
            ],
            [
                'email' => 'liam@gmail.com',
                'name' => 'Liam Wilson',
                'gender' => 'male',
                'birthdate' => '2000-09-08',
                'country_code' => 'GB',
                'country_name' => 'United Kingdom',
                'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=600&q=80',
                'level' => 1,
                'is_verified' => false,
                'credits' => 0,
                'tokens' => 200,
            ],
            [
                'email' => 'kwame@gmail.com',
                'name' => 'Kwame Chukwu',
                'gender' => 'male',
                'birthdate' => '1997-12-01',
                'country_code' => 'NG',
                'country_name' => 'Nigeria',
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 500,
            ],
            [
                'email' => 'tariq@gmail.com',
                'name' => 'Tariq Hassan',
                'gender' => 'male',
                'birthdate' => '2001-03-30',
                'country_code' => 'ZA',
                'country_name' => 'South Africa',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80',
                'level' => 1,
                'is_verified' => false,
                'credits' => 0,
                'tokens' => 150,
            ],
            [
                'email' => 'kenji@gmail.com',
                'name' => 'Kenji Sato',
                'gender' => 'male',
                'birthdate' => '1999-07-21',
                'country_code' => 'KR',
                'country_name' => 'South Korea',
                'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=600&q=80',
                'level' => 3,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 800,
            ],
            [
                'email' => 'rohan@gmail.com',
                'name' => 'Rohan Gupta',
                'gender' => 'male',
                'birthdate' => '1998-11-05',
                'country_code' => 'IN',
                'country_name' => 'India',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 450,
            ],
            [
                'email' => 'mateo@gmail.com',
                'name' => 'Mateo Garcia',
                'gender' => 'male',
                'birthdate' => '2000-05-19',
                'country_code' => 'ES',
                'country_name' => 'Spain',
                'avatar' => 'https://images.unsplash.com/photo-1480429370139-e0132c086e2a?auto=format&fit=crop&w=600&q=80',
                'level' => 1,
                'is_verified' => false,
                'credits' => 0,
                'tokens' => 180,
            ],
            [
                'email' => 'lucas@gmail.com',
                'name' => 'Lucas Santos',
                'gender' => 'male',
                'birthdate' => '2002-01-14',
                'country_code' => 'BR',
                'country_name' => 'Brazil',
                'avatar' => 'https://images.unsplash.com/photo-1513956589380-bad6acb9b9d4?auto=format&fit=crop&w=600&q=80',
                'level' => 2,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 350,
            ],
            [
                'email' => 'benjamin@gmail.com',
                'name' => 'Benjamin Dubois',
                'gender' => 'male',
                'birthdate' => '1996-08-27',
                'country_code' => 'FR',
                'country_name' => 'France',
                'avatar' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?auto=format&fit=crop&w=600&q=80',
                'level' => 3,
                'is_verified' => true,
                'credits' => 0,
                'tokens' => 950,
            ],
        ];

        $nowStr = now()->toDateTimeString();

        foreach ($mockUsers as $m) {
            User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => Hash::make('Password123!'),
                    'gender' => $m['gender'],
                    'birthdate' => $m['birthdate'],
                    'country_code' => $m['country_code'],
                    'country_name' => $m['country_name'],
                    'avatar' => $m['avatar'],
                    'level' => $m['level'],
                    'is_verified' => $m['is_verified'],
                    'credits' => $m['credits'],
                    'tokens' => $m['tokens'],
                    'is_admin' => false,
                    'last_heartbeat_at' => $nowStr,
                    'updated_at' => $nowStr,
                ]
            );
        }

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

            // ePay Payout Gateway Credentials
            ['key' => 'epay_merchant_id', 'value' => env('EPAY_MERCHANT_ID', 'EPAY_MCH_884920'), 'category' => 'gateways', 'description' => 'ePay Merchant Account ID'],
            ['key' => 'epay_api_key', 'value' => env('EPAY_API_KEY', 'epay_secret_api_key_sandbox'), 'category' => 'gateways', 'description' => 'ePay Secret API Key'],
            ['key' => 'epay_environment', 'value' => env('EPAY_ENVIRONMENT', 'sandbox'), 'category' => 'gateways', 'description' => 'ePay Environment (sandbox/production)'],

            // Google OAuth Credentials
            ['key' => 'google_web_client_id', 'value' => env('GOOGLE_WEB_CLIENT_ID', 'your-google-web-client-id'), 'category' => 'google', 'description' => 'Google Web OAuth Client ID'],
            ['key' => 'google_web_client_secret', 'value' => env('GOOGLE_WEB_CLIENT_SECRET', 'your-google-web-client-secret'), 'category' => 'google', 'description' => 'Google Web OAuth Client Secret'],
            ['key' => 'google_ios_client_id', 'value' => env('GOOGLE_IOS_CLIENT_ID', 'your-google-ios-client-id'), 'category' => 'google', 'description' => 'Google iOS Client ID'],
            ['key' => 'google_android_client_id', 'value' => env('GOOGLE_ANDROID_CLIENT_ID', 'your-google-android-client-id'), 'category' => 'google', 'description' => 'Google Android Client ID'],

            // Video Call Cost Rates (Tokens / Min for Male Levels)
            ['key' => 'male_call_rate_level1', 'value' => '60', 'category' => 'call_rates', 'description' => 'Level 1 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level2', 'value' => '50', 'category' => 'call_rates', 'description' => 'Level 2 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level3', 'value' => '40', 'category' => 'call_rates', 'description' => 'Level 3 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level4', 'value' => '30', 'category' => 'call_rates', 'description' => 'Level 4 Male Video Call Cost (Tokens/min)'],
            ['key' => 'male_call_rate_level5', 'value' => '20', 'category' => 'call_rates', 'description' => 'Level 5 Male Video Call Cost (Tokens/min)'],

            // Chat & Messaging Costs
            ['key' => 'chat_text_message_cost', 'value' => '3', 'category' => 'chat_rates', 'description' => 'Text Message Cost (Tokens per message)'],
            ['key' => 'chat_media_message_cost', 'value' => '5', 'category' => 'chat_rates', 'description' => 'Image / Voice Note Message Cost (Tokens)'],

            // Female Creator Payout & Split Rates
            ['key' => 'creator_payout_split_percentage', 'value' => '70', 'category' => 'payouts', 'description' => 'Female Creator Payout Split (% of call revenue)'],
            ['key' => 'chat_commission_percentage', 'value' => '70', 'category' => 'payouts', 'description' => 'Female Creator Chat Commission Split (% of chat revenue)'],
            ['key' => 'gift_commission_percentage', 'value' => '70', 'category' => 'payouts', 'description' => 'Female Creator Gift Commission Split (% of gift revenue)'],
            ['key' => 'female_pay_rate_level1', 'value' => '20', 'category' => 'payouts', 'description' => 'Level 1 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'female_pay_rate_level2', 'value' => '25', 'category' => 'payouts', 'description' => 'Level 2 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'female_pay_rate_level3', 'value' => '30', 'category' => 'payouts', 'description' => 'Level 3 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'female_pay_rate_level4', 'value' => '40', 'category' => 'payouts', 'description' => 'Level 4 Female Creator Pay Rate (Credits/min)'],
            ['key' => 'minimum_cashout_credits', 'value' => '500', 'category' => 'payouts', 'description' => 'Minimum Credits Balance Required for Cashout'],
            ['key' => 'credits_per_usd', 'value' => '100', 'category' => 'payouts', 'description' => 'Credit-to-USD Exchange Rate (100 Credits = $1.00 USD)'],

            // Video Call Engine & LiveKit Config
            ['key' => 'livekit_url', 'value' => env('LIVEKIT_URL', 'wss://demo.livekit.cloud'), 'category' => 'livekit', 'description' => 'LiveKit WebRTC Server URL'],
            ['key' => 'livekit_api_key', 'value' => env('LIVEKIT_API_KEY', 'devkey'), 'category' => 'livekit', 'description' => 'LiveKit API Key'],
            ['key' => 'max_call_duration_minutes', 'value' => '60', 'category' => 'livekit', 'description' => 'Maximum Continuous Video Call Duration (Mins)'],

            // App Rules & Bonus Settings
            ['key' => 'new_user_welcome_tokens', 'value' => '0', 'category' => 'app_rules', 'description' => 'Welcome Gift Tokens for New Male Users'],
            ['key' => 'biometric_verification_required', 'value' => '1', 'category' => 'app_rules', 'description' => 'Require 4-Step Verification Before Video Calling (1=Yes, 0=No)'],
            ['key' => 'daily_login_bonus_exp', 'value' => '50', 'category' => 'app_rules', 'description' => 'Daily Login Bonus EXP Points'],
            ['key' => 'country_filter_token_fee', 'value' => '5', 'category' => 'app_rules', 'description' => 'Token Fee to Filter Users by Country'],
        ];

        foreach ($settings as $s) {
            SystemSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
