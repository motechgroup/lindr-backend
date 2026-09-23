-- Lindr Platform MySQL Database Export
-- Database: `vrmthdfl_lindrdbmysql`

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(255) NOT NULL DEFAULT 'male',
  `birthdate` date DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `country_code` varchar(255) NOT NULL DEFAULT 'KE',
  `country_name` varchar(255) NOT NULL DEFAULT 'Kenya',
  `tokens` int(11) NOT NULL DEFAULT 350,
  `credits` int(11) NOT NULL DEFAULT 0,
  `total_topup_tokens` int(11) NOT NULL DEFAULT 0,
  `total_credits_earned` int(11) NOT NULL DEFAULT 0,
  `exp_points` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 1,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_google_id_index` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `google_id`, `email_verified_at`, `password`, `gender`, `birthdate`, `avatar`, `country_code`, `country_name`, `tokens`, `credits`, `total_topup_tokens`, `total_credits_earned`, `exp_points`, `level`, `is_verified`, `is_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Lindr Super Admin', 'admin@lindr.app', NULL, '2026-09-23 18:00:00', '$2y$12$R2E7QXGtxD6ArRnWG6NaDutB4HU9Td6rMqsRJj4oyAtTVSFnHx1gG', 'male', '1990-01-01', NULL, 'KE', 'Kenya', 999999, 999999, 0, 0, 0, 99, 1, 1, NULL, NOW(), NOW()),
(2, 'Alex Mercer', 'alex.demo@lindr.app', NULL, NULL, '$2y$12$R2E7QXGtxD6ArRnWG6NaDutB4HU9Td6rMqsRJj4oyAtTVSFnHx1gG', 'male', '1998-05-15', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80', 'KE', 'Kenya', 750, 0, 5500, 0, 0, 3, 1, 0, NULL, NOW(), NOW()),
(3, 'Sophia Vance', 'sophia@lindr.app', NULL, NULL, '$2y$12$R2E7QXGtxD6ArRnWG6NaDutB4HU9Td6rMqsRJj4oyAtTVSFnHx1gG', 'female', '2001-08-22', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80', 'KE', 'Kenya', 50, 1250, 0, 1250, 1100, 4, 1, 0, NULL, NOW(), NOW()),
(4, 'Elena Rostova', 'elena@lindr.app', NULL, NULL, '$2y$12$R2E7QXGtxD6ArRnWG6NaDutB4HU9Td6rMqsRJj4oyAtTVSFnHx1gG', 'female', '1999-11-04', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80', 'KE', 'Kenya', 120, 850, 0, 850, 800, 3, 1, 0, NULL, NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `transactions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `amount_tokens` int(11) NOT NULL,
  `amount_usd` decimal(8,2) NOT NULL,
  `payment_provider` varchar(255) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount_tokens`, `amount_usd`, `payment_provider`, `reference`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'topup', 5000, 49.99, 'flutterwave', 'FLW_REF_984122', 'completed', NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `withdrawals`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `withdrawals`;
CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `credits_amount` int(11) NOT NULL,
  `amount_usd` decimal(8,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `account_details` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `withdrawals` (`id`, `user_id`, `credits_amount`, `amount_usd`, `payment_method`, `account_details`, `status`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 3, 500, 5.00, 'mpesa', 'M-Pesa Phone: +254712345678', 'pending', NULL, NOW(), NOW()),
(2, 4, 1000, 10.00, 'epay', 'ePay Account: elena@epay.com', 'pending', NULL, NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `call_sessions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `call_sessions`;
CREATE TABLE `call_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `caller_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `channel_name` varchar(255) NOT NULL,
  `duration_seconds` int(11) NOT NULL DEFAULT 0,
  `tokens_spent` int(11) NOT NULL DEFAULT 0,
  `credits_earned` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `chat_messages`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message_text` text DEFAULT NULL,
  `gift_id` varchar(255) DEFAULT NULL,
  `tokens_spent` int(11) NOT NULL DEFAULT 0,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `system_settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system_settings` (`key`, `value`, `category`, `description`, `created_at`, `updated_at`) VALUES
('flw_public_key', 'FLWPUBK_TEST-YOUR_PUBLIC_KEY', 'gateways', 'Flutterwave Public API Key', NOW(), NOW()),
('flw_secret_key', 'FLWSECK_TEST-YOUR_SECRET_KEY', 'gateways', 'Flutterwave Secret API Key', NOW(), NOW()),
('flw_secret_hash', 'FLWSECK_TEST_HASH', 'gateways', 'Flutterwave Webhook Secret Hash', NOW(), NOW()),
('mpesa_consumer_key', '9a7lKfNJxw0eIiCay8OqFHeBGNXSfBLQTxWRACneeKEfSBjD', 'gateways', 'M-Pesa Consumer Key', NOW(), NOW()),
('mpesa_consumer_secret', 'GdtbnHA4Wl7uzyB4bNNlDPMP1B2fmSv3AvynXqvmIkoXexMQn3azKN3SVnczPSS8', 'gateways', 'M-Pesa Consumer Secret', NOW(), NOW()),
('mpesa_shortcode', '174379', 'gateways', 'M-Pesa Paybill / Shortcode', NOW(), NOW()),
('mpesa_passkey', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919', 'gateways', 'M-Pesa Online Passkey', NOW(), NOW()),
('mpesa_callback_url', 'https://lindrapp.top/api/v1/webhooks/mpesa', 'gateways', 'M-Pesa Instant Callback URL', NOW(), NOW()),
('epay_merchant_id', 'EPAY_MCH_884920', 'gateways', 'ePay Merchant Account ID', NOW(), NOW()),
('epay_api_key', 'epay_secret_api_key', 'gateways', 'ePay Secret API Key', NOW(), NOW()),
('epay_environment', 'production', 'gateways', 'ePay Environment (sandbox/production)', NOW(), NOW()),
('google_web_client_id', 'your-google-web-client-id.apps.googleusercontent.com', 'google', 'Google Web OAuth Client ID', NOW(), NOW()),
('google_ios_client_id', 'your-google-ios-client-id.apps.googleusercontent.com', 'google', 'Google iOS Client ID', NOW(), NOW()),
('google_android_client_id', 'your-google-android-client-id.apps.googleusercontent.com', 'google', 'Google Android Client ID', NOW(), NOW()),
('male_call_rate_level1', '60', 'call_rates', 'Level 1 Male Video Call Cost (Tokens/min)', NOW(), NOW()),
('male_call_rate_level2', '50', 'call_rates', 'Level 2 Male Video Call Cost (Tokens/min)', NOW(), NOW()),
('male_call_rate_level3', '40', 'call_rates', 'Level 3 Male Video Call Cost (Tokens/min)', NOW(), NOW()),
('male_call_rate_level4', '30', 'call_rates', 'Level 4 Male Video Call Cost (Tokens/min)', NOW(), NOW()),
('male_call_rate_level5', '20', 'call_rates', 'Level 5 Male Video Call Cost (Tokens/min)', NOW(), NOW()),
('chat_text_message_cost', '2', 'chat_rates', 'Text Message Cost (Tokens per message)', NOW(), NOW()),
('chat_media_message_cost', '5', 'chat_rates', 'Image / Voice Note Message Cost (Tokens)', NOW(), NOW()),
('female_payout_percentage', '70', 'payouts', 'Female Creator Revenue Share Percentage (%)', NOW(), NOW()),
('female_credit_pay_rate_level1', '6', 'payouts', 'Level 1 Female Credit Pay Rate (Credits/min)', NOW(), NOW()),
('female_credit_pay_rate_level2', '8', 'payouts', 'Level 2 Female Credit Pay Rate (Credits/min)', NOW(), NOW()),
('female_credit_pay_rate_level3', '10', 'payouts', 'Level 3 Female Credit Pay Rate (Credits/min)', NOW(), NOW()),
('female_credit_pay_rate_level4', '15', 'payouts', 'Level 4 Female Credit Pay Rate (Credits/min)', NOW(), NOW()),
('female_credit_pay_rate_level5', '20', 'payouts', 'Level 5 Female Credit Pay Rate (Credits/min)', NOW(), NOW()),
('agora_app_id', 'agora_demo_app_id_99201123', 'agora', 'Agora RTC App ID', NOW(), NOW()),
('agora_app_certificate', 'agora_demo_certificate_sec_99120', 'agora', 'Agora RTC Primary Certificate', NOW(), NOW()),
('welcome_bonus_tokens', '350', 'app_rules', 'Default Free Welcome Tokens for New Male Users', NOW(), NOW()),
('require_biometric_for_cashout', '1', 'app_rules', 'Require 4-Step Biometric Verification for Female Cashout', NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `token_packages`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `token_packages`;
CREATE TABLE `token_packages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tokens` int(11) NOT NULL,
  `bonus_tokens` int(11) NOT NULL DEFAULT 0,
  `price_usd` decimal(8,2) NOT NULL,
  `price_kes` int(11) NOT NULL,
  `badge` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `token_packages` (`name`, `tokens`, `bonus_tokens`, `price_usd`, `price_kes`, `badge`, `is_active`, `created_at`, `updated_at`) VALUES
('Starter Pack', 500, 50, 4.99, 650, 'POPULAR', 1, NOW(), NOW()),
('Pro Pack', 1200, 200, 9.99, 1300, 'BEST VALUE', 1, NOW(), NOW()),
('Master Pack', 2500, 500, 19.99, 2600, 'SUPER DISCOUNT', 1, NOW(), NOW()),
('VIP Pack', 7000, 1500, 49.99, 6500, 'VIP LEVEL UNLOCK', 1, NOW(), NOW()),
('Ultra Whale Pack', 15000, 4000, 99.99, 13000, 'MAX SAVINGS 40%', 1, NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_09_23_000001_create_transactions_table', 1),
('2026_09_23_000002_create_withdrawals_table', 1),
('2026_09_23_000003_create_call_sessions_table', 1),
('2026_09_23_000004_create_chat_messages_table', 1),
('2026_09_23_000005_create_user_tasks_table', 1),
('2026_09_23_000006_create_system_settings_table', 1),
('2026_09_23_000007_create_token_packages_table', 1);

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
