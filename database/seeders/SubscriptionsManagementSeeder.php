<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionsManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (optional - comment out if you don't want to clear)
        $this->truncateTables();

        // Get fresh IDs from auto-increment
        $ids = $this->getIds();

        // Seed users first
        $this->seedUsers($ids);

        // Seed features
        $this->seedFeatures($ids);

        // Seed payment gateways
        $this->seedPaymentGateways($ids);

        // Seed plans
        $this->seedPlans($ids);

        // Seed plan features
        $this->seedPlanFeatures($ids);

        // Seed plan prices
        $this->seedPlanPrices($ids);

        // Seed subscriptions
        $this->seedSubscriptions($ids);

        // Seed subscription items
        $this->seedSubscriptionItems($ids);

        // Seed invoices
        $this->seedInvoices($ids);

        // Seed payments
        $this->seedPayments($ids);

        // Seed payment masters and related tables
        $this->seedPaymentMasters($ids);

        // Seed usage records
        $this->seedUsageRecords($ids);

        // Seed discounts
        $this->seedDiscounts($ids);

        // Seed subscription events
        $this->seedSubscriptionEvents($ids);

        // Seed payment methods
        $this->seedPaymentMethods($ids);

        // Seed subscription orders
        $this->seedSubscriptionOrders($ids);

        // Seed metered usage aggregates
        $this->seedMeteredUsageAggregates($ids);

        // Seed rate limits
        $this->seedRateLimits($ids);

        // Seed refunds
        $this->seedRefunds($ids);

        // Seed payment webhook logs
        $this->seedPaymentWebhookLogs($ids);
    }

    /**
     * Truncate all tables
     */
    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'users',
            'features',
            'plans',
            'plan_features',
            'plan_prices',
            'subscriptions',
            'subscription_items',
            'invoices',
            'payments',
            'usage_records',
            'discounts',
            'subscription_events',
            'payment_methods',
            'payment_gateways',
            'subscription_orders',
            'subscription_order_items',
            'metered_usage_aggregates',
            'payment_masters',
            'payment_children',
            'payment_transactions',
            'payment_allocations',
            'refunds',
            'rate_limits',
            'payment_webhook_logs',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Get reference IDs (will be populated after inserts)
     */
    private function getIds(): array
    {
        return [
            // These will be populated after inserts
            'user_admin' => null,
            'user1' => null,
            'user2' => null,
            'user3' => null,
            'user4' => null,
            'user5' => null,

            // Features
            'feature_api_requests' => null,
            'feature_storage' => null,
            'feature_users' => null,
            'feature_priority_support' => null,
            'feature_custom_domain' => null,
            'feature_webhooks' => null,
            'feature_export' => null,
            'feature_api_rate_limit' => null,

            // Plans
            'plan_free' => null,
            'plan_starter' => null,
            'plan_professional' => null,
            'plan_enterprise' => null,
            'plan_payg' => null,
            'plan_starter_yearly' => null,
            'plan_professional_yearly' => null,

            // Payment Gateways
            'gateway_stripe' => null,
            'gateway_paypal' => null,
            'gateway_sslcommerz' => null,
            'gateway_bkash' => null,
            'gateway_nagad' => null,
            'gateway_rocket' => null,
            'gateway_bank' => null,
            'gateway_cash' => null,

            // Other IDs will be fetched from DB as needed
        ];
    }

    /**
     * Seed users table
     */
    private function seedUsers(array &$ids): void
    {
        $now = Carbon::now();
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '+1234567890',
                'email_verified_at' => $now,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'billing_type' => 'personal',
                'tax_id' => null,
                'is_tax_exempt' => false,
                'tax_certificate' => null,
                'billing_address' => json_encode([
                    'line1' => '123 Main St',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'US',
                ]),
                'shipping_address' => json_encode([
                    'line1' => '123 Main St',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'US',
                ]),
                'preferred_currency' => 'USD',
                'preferred_payment_method' => 'card',
                'auto_renew' => true,
                'trial_ends_at' => null,
                'has_used_trial' => false,
                'account_status' => 'active',
                'account_status_reason' => null,
                'metadata' => json_encode(['source' => 'website']),
                'preferences' => json_encode(['newsletter' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567891',
                'email_verified_at' => $now,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
                'billing_type' => 'business',
                'tax_id' => '123456789',
                'is_tax_exempt' => false,
                'tax_certificate' => json_encode(['number' => 'CERT123', 'expires' => '2026-12-31']),
                'billing_address' => json_encode([
                    'line1' => '456 Business Ave',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'postal_code' => '94105',
                    'country' => 'US',
                ]),
                'shipping_address' => json_encode([
                    'line1' => '456 Business Ave',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'postal_code' => '94105',
                    'country' => 'US',
                ]),
                'preferred_currency' => 'USD',
                'preferred_payment_method' => 'card',
                'auto_renew' => true,
                'trial_ends_at' => null,
                'has_used_trial' => true,
                'account_status' => 'active',
                'account_status_reason' => null,
                'metadata' => json_encode(['company' => 'Acme Inc', 'source' => 'referral']),
                'preferences' => json_encode(['newsletter' => true, 'dark_mode' => false]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(6),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1234567892',
                'email_verified_at' => $now,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
                'billing_type' => 'personal',
                'tax_id' => null,
                'is_tax_exempt' => true,
                'tax_certificate' => json_encode(['number' => 'TAXEXEMPT123']),
                'billing_address' => json_encode([
                    'line1' => '789 Oak St',
                    'city' => 'Austin',
                    'state' => 'TX',
                    'postal_code' => '78701',
                    'country' => 'US',
                ]),
                'shipping_address' => json_encode([
                    'line1' => '789 Oak St',
                    'city' => 'Austin',
                    'state' => 'TX',
                    'postal_code' => '78701',
                    'country' => 'US',
                ]),
                'preferred_currency' => 'USD',
                'preferred_payment_method' => 'paypal',
                'auto_renew' => true,
                'trial_ends_at' => null,
                'has_used_trial' => true,
                'account_status' => 'active',
                'account_status_reason' => null,
                'metadata' => json_encode(['source' => 'google_ads']),
                'preferences' => json_encode(['newsletter' => false, 'dark_mode' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
                'phone' => '+1234567893',
                'email_verified_at' => $now,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
                'billing_type' => 'enterprise',
                'tax_id' => '987654321',
                'is_tax_exempt' => false,
                'tax_certificate' => null,
                'billing_address' => json_encode([
                    'line1' => '321 Corporate Blvd',
                    'city' => 'Chicago',
                    'state' => 'IL',
                    'postal_code' => '60601',
                    'country' => 'US',
                ]),
                'shipping_address' => json_encode([
                    'line1' => '321 Corporate Blvd',
                    'city' => 'Chicago',
                    'state' => 'IL',
                    'postal_code' => '60601',
                    'country' => 'US',
                ]),
                'preferred_currency' => 'USD',
                'preferred_payment_method' => 'bank_transfer',
                'auto_renew' => true,
                'trial_ends_at' => null,
                'has_used_trial' => false,
                'account_status' => 'active',
                'account_status_reason' => null,
                'metadata' => json_encode(['company' => 'Johnson Corp', 'employees' => 500, 'source' => 'sales_team']),
                'preferences' => json_encode(['newsletter' => true, 'dark_mode' => true, 'api_access' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subYear(),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah.williams@example.com',
                'phone' => '+1234567894',
                'email_verified_at' => $now,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
                'billing_type' => 'business',
                'tax_id' => '456789123',
                'is_tax_exempt' => false,
                'tax_certificate' => null,
                'billing_address' => json_encode([
                    'line1' => '555 Tech Park',
                    'city' => 'Seattle',
                    'state' => 'WA',
                    'postal_code' => '98101',
                    'country' => 'US',
                ]),
                'shipping_address' => json_encode([
                    'line1' => '555 Tech Park',
                    'city' => 'Seattle',
                    'state' => 'WA',
                    'postal_code' => '98101',
                    'country' => 'US',
                ]),
                'preferred_currency' => 'USD',
                'preferred_payment_method' => 'card',
                'auto_renew' => true,
                'trial_ends_at' => null,
                'has_used_trial' => true,
                'account_status' => 'active',
                'account_status_reason' => null,
                'metadata' => json_encode(['company' => 'TechStart', 'source' => 'twitter']),
                'preferences' => json_encode(['newsletter' => true, 'dark_mode' => false, 'beta_features' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Mike Brown',
                'email' => 'mike.brown@example.com',
                'phone' => '+1234567895',
                'email_verified_at' => $now,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
                'billing_type' => 'personal',
                'tax_id' => null,
                'is_tax_exempt' => false,
                'tax_certificate' => null,
                'billing_address' => json_encode([
                    'line1' => '777 Beach Ave',
                    'city' => 'Miami',
                    'state' => 'FL',
                    'postal_code' => '33101',
                    'country' => 'US',
                ]),
                'shipping_address' => json_encode([
                    'line1' => '777 Beach Ave',
                    'city' => 'Miami',
                    'state' => 'FL',
                    'postal_code' => '33101',
                    'country' => 'US',
                ]),
                'preferred_currency' => 'USD',
                'preferred_payment_method' => 'card',
                'auto_renew' => false,
                'trial_ends_at' => null,
                'has_used_trial' => true,
                'account_status' => 'suspended',
                'account_status_reason' => 'payment_failed_multiple_times',
                'metadata' => json_encode(['source' => 'facebook']),
                'preferences' => json_encode(['newsletter' => false, 'dark_mode' => false]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(8),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        foreach ($users as $user) {
            $id = DB::table('users')->insertGetId($user);

            // Map email to ID for reference
            if ($user['email'] === 'admin@example.com') {
                $ids['user_admin'] = $id;
            } elseif ($user['email'] === 'john.doe@example.com') {
                $ids['user1'] = $id;
            } elseif ($user['email'] === 'jane.smith@example.com') {
                $ids['user2'] = $id;
            } elseif ($user['email'] === 'bob.johnson@example.com') {
                $ids['user3'] = $id;
            } elseif ($user['email'] === 'sarah.williams@example.com') {
                $ids['user4'] = $id;
            } elseif ($user['email'] === 'mike.brown@example.com') {
                $ids['user5'] = $id;
            }
        }
    }

    /**
     * Seed features table
     */
    private function seedFeatures(array &$ids): void
    {
        $now = Carbon::now();
        $features = [
            [
                'name' => 'API Requests',
                'code' => 'api_requests',
                'description' => 'Number of API requests per month',
                'type' => 'limit',
                'scope' => 'global',
                'is_resettable' => true,
                'reset_period' => 'monthly',
                'metadata' => json_encode(['unit' => 'requests', 'display_order' => 1]),
                'validations' => json_encode(['min' => 0, 'max' => 10000000]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Storage',
                'code' => 'storage_gb',
                'description' => 'Storage space in gigabytes',
                'type' => 'limit',
                'scope' => 'global',
                'is_resettable' => true,
                'reset_period' => 'monthly',
                'metadata' => json_encode(['unit' => 'GB', 'display_order' => 2]),
                'validations' => json_encode(['min' => 0, 'max' => 10000]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Users',
                'code' => 'users',
                'description' => 'Number of team members',
                'type' => 'limit',
                'scope' => 'per_seat',
                'is_resettable' => true,
                'reset_period' => 'monthly',
                'metadata' => json_encode(['unit' => 'users', 'display_order' => 3]),
                'validations' => json_encode(['min' => 1, 'max' => 1000]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Priority Support',
                'code' => 'priority_support',
                'description' => 'Access to priority customer support',
                'type' => 'boolean',
                'scope' => 'global',
                'is_resettable' => false,
                'reset_period' => 'never',
                'metadata' => json_encode(['display_order' => 4]),
                'validations' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Custom Domain',
                'code' => 'custom_domain',
                'description' => 'Use your own domain name',
                'type' => 'boolean',
                'scope' => 'global',
                'is_resettable' => false,
                'reset_period' => 'never',
                'metadata' => json_encode(['display_order' => 5]),
                'validations' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Webhooks',
                'code' => 'webhooks',
                'description' => 'Number of webhook endpoints',
                'type' => 'limit',
                'scope' => 'global',
                'is_resettable' => true,
                'reset_period' => 'never',
                'metadata' => json_encode(['unit' => 'endpoints', 'display_order' => 6]),
                'validations' => json_encode(['min' => 0, 'max' => 100]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Export',
                'code' => 'export',
                'description' => 'Data export functionality',
                'type' => 'boolean',
                'scope' => 'global',
                'is_resettable' => false,
                'reset_period' => 'never',
                'metadata' => json_encode(['display_order' => 7]),
                'validations' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'API Rate Limit',
                'code' => 'api_rate_limit',
                'description' => 'API requests per second',
                'type' => 'limit',
                'scope' => 'global',
                'is_resettable' => true,
                'reset_period' => 'monthly',
                'metadata' => json_encode(['unit' => 'rps', 'display_order' => 8]),
                'validations' => json_encode(['min' => 1, 'max' => 10000]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        foreach ($features as $feature) {
            $id = DB::table('features')->insertGetId($feature);

            // Map by code
            $ids['feature_'.$feature['code']] = $id;
        }
    }

    /**
     * Seed payment_gateways table
     */
    private function seedPaymentGateways(array &$ids): void
    {
        $now = Carbon::now();
        $gateways = [
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'type' => 'card',
                'is_active' => 1,
                'is_test_mode' => 1,
                'supports_recurring' => 1,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'api_key' => env('STRIPE_KEY', 'pk_test_your_key'),
                'api_secret' => env('STRIPE_SECRET', 'sk_test_your_secret'),
                'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', 'whsec_your_secret'),
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'callback_url' => null,
                'webhook_url' => 'http://127.0.0.1:8000/api/v1/webhooks/stripe',
                'base_url' => 'https://api.stripe.com',
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
                'supported_countries' => json_encode(['US', 'GB', 'CA', 'AU', 'DE', 'FR']),
                'excluded_countries' => null,
                'percentage_fee' => 2.90,
                'fixed_fee' => 0.30,
                'fee_currency' => 'USD',
                'fee_structure' => json_encode([
                    'domestic' => ['percentage' => 2.9, 'fixed' => 0.30],
                    'international' => ['percentage' => 3.9, 'fixed' => 0.30],
                ]),
                'config' => json_encode([
                    'webhook_secret' => 'whsec_'.Str::random(24),
                    'api_version' => '2023-10-16',
                ]),
                'metadata' => null,
                'settlement_days' => 2,
                'refund_days' => 5,
                'min_amount' => 0.50,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'type' => 'wallet',
                'is_active' => 1,
                'is_test_mode' => 1,
                'supports_recurring' => 1,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'webhook_secret' => null,
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'callback_url' => null,

                'api_key' => env('PAYPAL_CLIENT_ID', 'your_client_id'),
                'api_secret' => env('PAYPAL_CLIENT_SECRET', 'your_client_secret'),
                'base_url' => env('PAYPAL_MODE') === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com',
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'JPY']),
                'supported_countries' => json_encode(['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'JP']),
                'percentage_fee' => 3.40,
                'fixed_fee' => 0.30,
                'webhook_url' => 'http://127.0.0.1:8000/api/v1/webhooks/paypal',
                'fee_currency' => 'USD',
                'fee_structure' => json_encode([
                    'domestic' => ['percentage' => 2.99, 'fixed' => 0.49],
                    'international' => ['percentage' => 4.99, 'fixed' => 0.49],
                ]),
                'config' => json_encode([
                    'client_id' => 'AYjZ_'.Str::random(32),
                    'webhook_id' => 'WH_'.Str::random(12),
                ]),
                'metadata' => null,
                'settlement_days' => 2,
                'refund_days' => 5,
                'min_amount' => 1.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'SSLCommerz',
                'code' => 'sslcommerz',
                'type' => 'aggregator',
                'is_active' => 1,
                'is_test_mode' => 1,
                'supports_recurring' => 0,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'api_key' => null,
                'api_secret' => null,
                'webhook_secret' => null,
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'base_url' => null,
                'callback_url' => null,
                'webhook_url' => 'http://127.0.0.1:8000/api/v1/webhooks/sslcommerz',
                'supported_currencies' => json_encode(['BDT', 'USD']),
                'supported_countries' => json_encode(['BD']),
                'excluded_countries' => null,
                'percentage_fee' => 2.00,
                'fixed_fee' => 0.00,
                'fee_currency' => 'BDT',
                'fee_structure' => null,
                'config' => null,
                'metadata' => null,
                'settlement_days' => 2,
                'refund_days' => 5,
                'min_amount' => 10.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'bKash',
                'code' => 'bkash',
                'type' => 'wallet',
                'is_active' => 1,
                'is_test_mode' => 1,
                'supports_recurring' => 0,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'webhook_secret' => null,
                'store_id' => null,
                'store_password' => null,
                'api_key' => env('BKASH_APP_KEY', 'your_app_key'),
                'api_secret' => env('BKASH_APP_SECRET', 'your_app_secret'),
                'merchant_id' => env('BKASH_MERCHANT_ID', 'your_merchant_id'),
                'base_url' => env('BKASH_BASE_URL', 'https://tokenized.pay.bka.sh/v1.2.0-beta'),
                'callback_url' => env('APP_URL').'/payment/bkash/callback',
                'webhook_url' => 'http://127.0.0.1:8000/api/v1/webhooks/bkash',
                'supported_currencies' => json_encode(['BDT']),
                'supported_countries' => json_encode(['BD']),
                'excluded_countries' => null,
                'percentage_fee' => 1.50,
                'fixed_fee' => 5.00,
                'fee_currency' => 'BDT',
                'fee_structure' => null,
                'config' => null,
                'metadata' => null,
                'settlement_days' => 1,
                'refund_days' => 3,
                'min_amount' => 10.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Nagad',
                'code' => 'nagad',
                'type' => 'wallet',
                'is_active' => 1,
                'is_test_mode' => 1,
                'supports_recurring' => 0,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'webhook_secret' => null,
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'api_key' => env('NAGAD_MERCHANT_ID', 'your_merchant_id'),
                'api_secret' => env('NAGAD_MERCHANT_KEY', 'your_merchant_key'),
                'base_url' => env('NAGAD_BASE_URL', 'https://sandbox.mynagad.com'),
                'callback_url' => env('APP_URL').'/payment/nagad/callback',
                'webhook_url' => 'http://127.0.0.1:8000/api/v1/webhooks/nagad',
                'supported_currencies' => json_encode(['BDT']),
                'supported_countries' => json_encode(['BD']),
                'excluded_countries' => null,
                'percentage_fee' => 1.25,
                'fixed_fee' => 5.00,
                'fee_currency' => 'BDT',
                'fee_structure' => null,
                'config' => null,
                'metadata' => null,
                'settlement_days' => 1,
                'refund_days' => 3,
                'min_amount' => 10.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Rocket',
                'code' => 'rocket',
                'type' => 'wallet',
                'is_active' => 1,
                'is_test_mode' => 1,
                'supports_recurring' => 0,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'webhook_secret' => null,
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'api_key' => env('ROCKET_MERCHANT_ID', 'your_merchant_id'),
                'api_secret' => env('ROCKET_MERCHANT_KEY', 'your_merchant_key'),
                'base_url' => env('ROCKET_BASE_URL', 'https://api.rocket.com.bd'),
                'callback_url' => env('APP_URL').'/payment/rocket/callback',
                'webhook_url' => 'http://127.0.0.1:8000/api/v1/webhooks/rocket',
                'supported_currencies' => json_encode(['BDT']),
                'supported_countries' => json_encode(['BD']),
                'excluded_countries' => null,
                'percentage_fee' => 1.25,
                'fixed_fee' => 5.00,
                'fee_currency' => 'BDT',
                'fee_structure' => null,
                'config' => null,
                'metadata' => null,
                'settlement_days' => 1,
                'refund_days' => 3,
                'min_amount' => 10.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'type' => 'bank',
                'is_active' => 1,
                'is_test_mode' => 0,
                'supports_recurring' => 0,
                'supports_refunds' => 1,
                'supports_installments' => 0,
                'api_key' => null,
                'api_secret' => null,
                'webhook_secret' => null,
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'base_url' => null,
                'callback_url' => null,
                'webhook_url' => null,
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP']),
                'supported_countries' => json_encode(['US', 'GB', 'DE', 'FR']),
                'excluded_countries' => null,
                'percentage_fee' => 0.00,
                'fixed_fee' => 0.00,
                'fee_currency' => 'USD',
                'fee_structure' => null,
                'config' => json_encode([
                    'bank_name' => 'Chase Bank',
                    'account_name' => 'Subscription Management Inc',
                    'account_number' => '343434343431234',
                    'routing_number' => '021000021',
                    'swift_code' => 'CHASUS33',
                ]),
                'metadata' => null,
                'settlement_days' => 3,
                'refund_days' => 7,
                'min_amount' => 10.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Cash',
                'code' => 'cash',
                'type' => 'cash',
                'is_active' => 1,
                'is_test_mode' => 0,
                'supports_recurring' => 0,
                'supports_refunds' => 0,
                'supports_installments' => 0,
                'api_key' => null,
                'api_secret' => null,
                'webhook_secret' => null,
                'merchant_id' => null,
                'store_id' => null,
                'store_password' => null,
                'base_url' => null,
                'callback_url' => null,
                'webhook_url' => null,
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'BDT']),
                'supported_countries' => json_encode(['US', 'GB', 'BD']),
                'excluded_countries' => null,
                'percentage_fee' => 0.00,
                'fixed_fee' => 0.00,
                'fee_currency' => 'USD',
                'fee_structure' => null,
                'config' => null,
                'metadata' => null,
                'settlement_days' => 0,
                'refund_days' => 0,
                'min_amount' => 1.00,
                'max_amount' => 999999.00,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'SurjoPay',
                'code' => 'surjopay',
                'type' => 'aggregator',
                'is_active' => true,
                'is_test_mode' => true,
                'supports_recurring' => false,
                'supports_refunds' => true,
                'supports_installments' => false,
                'api_key' => env('SURJOPAY_MERCHANT_KEY', 'your_merchant_key'),
                'api_secret' => env('SURJOPAY_API_SECRET', 'your_api_secret'),
                'merchant_id' => env('SURJOPAY_MERCHANT_ID', 'your_merchant_id'),
                'merchant_password' => env('SURJOPAY_MERCHANT_PASSWORD', 'your_merchant_password'),
                'base_url' => env('SURJOPAY_BASE_URL', 'https://engine.surjopay.com'),
                'callback_url' => env('APP_URL').'/payment/surjopay/callback',
                'webhook_url' => env('APP_URL').'/payment/surjopay/ipn',
                'supported_currencies' => json_encode(['BDT', 'USD']),
                'supported_countries' => json_encode(['BD']),
                'percentage_fee' => 2.00,
                'fixed_fee' => 0.00,
                'fee_currency' => 'BDT',
                'settlement_days' => 2,
                'refund_days' => 5,
                'min_amount' => 10.00,
                'max_amount' => 999999.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($gateways as $gateway) {
            $id = DB::table('payment_gateways')->insertGetId($gateway);
            $ids['gateway_'.$gateway['code']] = $id;
        }
    }

    /**
     * Seed plans table
     */
    private function seedPlans(array &$ids): void
    {
        $now = Carbon::now();
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'code' => 'FREE',
                'description' => 'Basic plan with limited features, perfect for getting started',
                'type' => 'recurring',
                'billing_period' => 'monthly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 1,
                'is_featured' => false,
                'metadata' => json_encode(['popular' => false, 'highlight_color' => '#gray', 'tagline' => 'Start for free']),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'code' => 'STARTER',
                'description' => 'Perfect for small businesses and startups',
                'type' => 'recurring',
                'billing_period' => 'monthly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 2,
                'is_featured' => true,
                'metadata' => json_encode(['popular' => true, 'highlight_color' => '#blue', 'tagline' => 'Most popular']),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'code' => 'PRO',
                'description' => 'For growing businesses with advanced needs',
                'type' => 'recurring',
                'billing_period' => 'monthly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 3,
                'is_featured' => false,
                'metadata' => json_encode(['popular' => false, 'highlight_color' => '#purple', 'tagline' => 'Advanced features']),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'code' => 'ENTERPRISE',
                'description' => 'Advanced features for large organizations',
                'type' => 'recurring',
                'billing_period' => 'yearly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 4,
                'is_featured' => false,
                'metadata' => json_encode(['popular' => false, 'highlight_color' => '#gold', 'tagline' => 'For large teams']),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Pay As You Go',
                'slug' => 'payg',
                'code' => 'PAYG',
                'description' => 'Usage-based pricing, pay only for what you use',
                'type' => 'usage',
                'billing_period' => 'monthly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 5,
                'is_featured' => false,
                'metadata' => json_encode(['popular' => false, 'highlight_color' => '#green', 'tagline' => 'Flexible pricing']),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Starter Yearly',
                'slug' => 'starter-yearly',
                'code' => 'STARTER_YEARLY',
                'description' => 'Starter plan with yearly billing (save 20%)',
                'type' => 'recurring',
                'billing_period' => 'yearly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 6,
                'is_featured' => false,
                'metadata' => json_encode(['popular' => false, 'highlight_color' => '#blue', 'tagline' => 'Best value', 'discount_percent' => 20]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Professional Yearly',
                'slug' => 'professional-yearly',
                'code' => 'PRO_YEARLY',
                'description' => 'Professional plan with yearly billing (save 20%)',
                'type' => 'recurring',
                'billing_period' => 'yearly',
                'billing_interval' => 1,
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 7,
                'is_featured' => false,
                'metadata' => json_encode(['popular' => false, 'highlight_color' => '#purple', 'tagline' => 'Best value', 'discount_percent' => 20]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        foreach ($plans as $plan) {
            $id = DB::table('plans')->insertGetId($plan);
            $ids['plan_'.strtolower(str_replace('-', '_', $plan['slug']))] = $id;
        }
    }

    /**
     * Seed plan_features table
     */
    /**
     * Seed plan_features table
     */
    private function seedPlanFeatures(array $ids): void
    {
        // WARNING: This will delete all existing plan_features data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('plan_features')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = Carbon::now();

        // Get all plan IDs
        $plans = DB::table('plans')->get();

        // Get all features
        $features = DB::table('features')->get();

        $planFeatures = [];
        $sortOrder = 1;

        foreach ($plans as $plan) {
            foreach ($features as $feature) {
                $value = $this->getFeatureValueForPlan($plan->code, $feature->code);

                if ($value === null) {
                    continue;
                }

                $planFeatures[] = [
                    'plan_id' => $plan->id,
                    'feature_id' => $feature->id,
                    'value' => $value,
                    'config' => json_encode(['enabled' => true, 'rollover' => false]),
                    'sort_order' => $sortOrder++,
                    'is_inherited' => false,
                    'parent_feature_id' => null,
                    'effective_from' => $now,
                    'effective_to' => null,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];
            }
        }

        // Add historical record
        $starterPlan = DB::table('plans')->where('code', 'STARTER')->first();
        $prioritySupport = DB::table('features')->where('code', 'priority_support')->first();

        if ($starterPlan && $prioritySupport) {
            $planFeatures[] = [
                'plan_id' => $starterPlan->id,
                'feature_id' => $prioritySupport->id,
                'value' => 'true',
                'config' => json_encode(['enabled' => true]),
                'sort_order' => 99,
                'is_inherited' => false,
                'parent_feature_id' => null,
                'effective_from' => $now->copy()->subMonths(6),
                'effective_to' => $now->copy()->subMonths(3),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(6),
                'updated_at' => $now->copy()->subMonths(3),
                'deleted_at' => null,
            ];
        }

        // Insert in chunks
        foreach (array_chunk($planFeatures, 50) as $chunk) {
            DB::table('plan_features')->insert($chunk);
        }
    }

    /**
     * Get feature value based on plan and feature
     */
    private function getFeatureValueForPlan($planCode, $featureCode): ?string
    {
        $values = [
            'FREE' => [
                'api_requests' => '1000',
                'storage_gb' => '1',
                'users' => '1',
                'priority_support' => 'false',
                'custom_domain' => 'false',
                'webhooks' => '0',
                'export' => 'false',
                'api_rate_limit' => '10',
            ],
            'STARTER' => [
                'api_requests' => '10000',
                'storage_gb' => '10',
                'users' => '5',
                'priority_support' => 'false',
                'custom_domain' => 'true',
                'webhooks' => '3',
                'export' => 'true',
                'api_rate_limit' => '50',
            ],
            'STARTER_YEARLY' => [
                'api_requests' => '10000',
                'storage_gb' => '10',
                'users' => '5',
                'priority_support' => 'false',
                'custom_domain' => 'true',
                'webhooks' => '3',
                'export' => 'true',
                'api_rate_limit' => '50',
            ],
            'PRO' => [
                'api_requests' => '100000',
                'storage_gb' => '100',
                'users' => '20',
                'priority_support' => 'true',
                'custom_domain' => 'true',
                'webhooks' => '10',
                'export' => 'true',
                'api_rate_limit' => '200',
            ],
            'PRO_YEARLY' => [
                'api_requests' => '100000',
                'storage_gb' => '100',
                'users' => '20',
                'priority_support' => 'true',
                'custom_domain' => 'true',
                'webhooks' => '10',
                'export' => 'true',
                'api_rate_limit' => '200',
            ],
            'ENTERPRISE' => [
                'api_requests' => 'unlimited',
                'storage_gb' => '1000',
                'users' => 'unlimited',
                'priority_support' => 'true',
                'custom_domain' => 'true',
                'webhooks' => 'unlimited',
                'export' => 'true',
                'api_rate_limit' => '1000',
            ],
            'PAYG' => [
                'api_requests' => 'unlimited',
                'storage_gb' => 'unlimited',
                'users' => 'unlimited',
                'priority_support' => 'false',
                'custom_domain' => 'true',
                'webhooks' => 'unlimited',
                'export' => 'true',
                'api_rate_limit' => '100',
            ],
        ];

        return $values[$planCode][$featureCode] ?? null;
    }

    /**
     * Seed plan_prices table
     */
    private function seedPlanPrices(array $ids): void
    {
        $now = Carbon::now();

        $prices = [];

        // Get all plans
        $plans = DB::table('plans')->get();

        foreach ($plans as $plan) {
            $amount = $this->getPlanAmount($plan->code);

            $prices[] = [
                'plan_id' => $plan->id,
                'currency' => 'USD',
                'amount' => $amount,
                'interval' => $this->getPlanInterval($plan->code),
                'interval_count' => 1,
                'usage_type' => $plan->code === 'PAYG' ? 'metered' : 'licensed',
                'tiers' => $plan->code === 'PAYG' ? json_encode([
                    ['unit' => 'api_request', 'price' => 0.0001, 'first' => 0, 'last' => 1000000],
                    ['unit' => 'api_request', 'price' => 0.00005, 'first' => 1000001, 'last' => null],
                    ['unit' => 'storage_gb', 'price' => 0.10, 'first' => 0, 'last' => null],
                ]) : null,
                'transformations' => $plan->code === 'PAYG' ? json_encode(['round' => 'up', 'multiply' => 1]) : null,
                'stripe_price_id' => 'price_'.strtolower($plan->code).'_'.strtolower($this->getPlanInterval($plan->code)),
                'active_from' => $now,
                'active_to' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        DB::table('plan_prices')->insert($prices);
    }

    /**
     * Get plan amount based on code
     */
    private function getPlanAmount($planCode): string
    {
        $amounts = [
            'FREE' => '0.00',
            'STARTER' => '29.99',
            'STARTER_YEARLY' => '287.90', // 29.99 * 12 * 0.8
            'PRO' => '99.99',
            'PRO_YEARLY' => '959.90', // 99.99 * 12 * 0.8
            'ENTERPRISE' => '299.99',
            'PAYG' => '0.00',
        ];

        return $amounts[$planCode] ?? '0.00';
    }

    /**
     * Get plan interval based on code
     */
    private function getPlanInterval($planCode): string
    {
        if (strpos($planCode, 'YEARLY') !== false || $planCode === 'ENTERPRISE') {
            return 'year';
        }

        return 'month';
    }

    /**
     * Seed subscriptions table
     */
    private function seedSubscriptions(array $ids): void
    {
        $now = Carbon::now();

        // Get plan and price IDs
        $starterPlan = DB::table('plans')->where('code', 'STARTER')->first();
        $starterPrice = DB::table('plan_prices')
            ->where('plan_id', $starterPlan->id)
            ->where('interval', 'month')
            ->first();

        $proPlan = DB::table('plans')->where('code', 'PRO')->first();
        $proPrice = DB::table('plan_prices')
            ->where('plan_id', $proPlan->id)
            ->where('interval', 'month')
            ->first();

        $enterprisePlan = DB::table('plans')->where('code', 'ENTERPRISE')->first();
        $enterprisePrice = DB::table('plan_prices')
            ->where('plan_id', $enterprisePlan->id)
            ->where('interval', 'year')
            ->first();

        $paygPlan = DB::table('plans')->where('code', 'PAYG')->first();
        $paygPrice = DB::table('plan_prices')
            ->where('plan_id', $paygPlan->id)
            ->first();

        $freePlan = DB::table('plans')->where('code', 'FREE')->first();
        $freePrice = DB::table('plan_prices')
            ->where('plan_id', $freePlan->id)
            ->first();

        $subscriptions = [
            [
                'user_id' => $ids['user1'],
                'plan_id' => $starterPlan->id,
                'plan_price_id' => $starterPrice->id,
                'parent_subscription_id' => null,
                'status' => 'active',
                'billing_cycle_anchor' => 'creation',
                'quantity' => 1,
                'unit_price' => 29.99,
                'amount' => 29.99,
                'currency' => 'USD',
                'trial_starts_at' => null,
                'trial_ends_at' => null,
                'trial_converted' => false,
                'current_period_starts_at' => $now->copy()->subDays(15),
                'current_period_ends_at' => $now->copy()->addDays(15),
                'billing_cycle_anchor_date' => $now->copy()->subDays(15),
                'canceled_at' => null,
                'cancellation_reason' => null,
                'prorate' => true,
                'proration_amount' => null,
                'proration_date' => null,
                'gateway' => 'stripe',
                'gateway_subscription_id' => 'sub_'.Str::random(14),
                'gateway_customer_id' => 'cus_'.Str::random(14),
                'gateway_metadata' => json_encode(['payment_method' => 'pm_card_visa']),
                'metadata' => json_encode(['source' => 'web', 'auto_renew' => true]),
                'history' => json_encode([
                    ['event' => 'created', 'date' => $now->copy()->subDays(45)->toIso8601String()],
                    ['event' => 'payment_succeeded', 'date' => $now->copy()->subDays(15)->toIso8601String()],
                ]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(45),
                'updated_at' => $now,
                'deleted_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $ids['user2'],
                'plan_id' => $proPlan->id,
                'plan_price_id' => $proPrice->id,
                'parent_subscription_id' => null,
                'status' => 'active',
                'billing_cycle_anchor' => 'creation',
                'quantity' => 5, // 5 seats
                'unit_price' => 99.99,
                'amount' => 499.95,
                'currency' => 'USD',
                'trial_starts_at' => $now->copy()->subDays(90),
                'trial_ends_at' => $now->copy()->subDays(83),
                'trial_converted' => true,
                'current_period_starts_at' => $now->copy()->subDays(20),
                'current_period_ends_at' => $now->copy()->addDays(10),
                'billing_cycle_anchor_date' => $now->copy()->subDays(83),
                'canceled_at' => null,
                'cancellation_reason' => null,
                'prorate' => true,
                'proration_amount' => null,
                'proration_date' => null,
                'gateway' => 'paypal',
                'gateway_subscription_id' => 'I-'.strtoupper(Str::random(10)),
                'gateway_customer_id' => 'paypal_cus_'.Str::random(10),
                'gateway_metadata' => json_encode(['payer_id' => 'PAYER_'.Str::random(10)]),
                'metadata' => json_encode(['source' => 'referral', 'referral_code' => 'FRIEND10']),
                'history' => json_encode([
                    ['event' => 'created', 'date' => $now->copy()->subDays(90)->toIso8601String()],
                    ['event' => 'trial_started', 'date' => $now->copy()->subDays(90)->toIso8601String()],
                    ['event' => 'converted', 'date' => $now->copy()->subDays(83)->toIso8601String()],
                    ['event' => 'payment_succeeded', 'date' => $now->copy()->subDays(20)->toIso8601String()],
                ]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(90),
                'updated_at' => $now,
                'deleted_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $ids['user3'],
                'plan_id' => $enterprisePlan->id,
                'plan_price_id' => $enterprisePrice->id,
                'parent_subscription_id' => null,
                'status' => 'active',
                'billing_cycle_anchor' => 'creation',
                'quantity' => 1,
                'unit_price' => 299.99,
                'amount' => 299.99,
                'currency' => 'USD',
                'trial_starts_at' => null,
                'trial_ends_at' => null,
                'trial_converted' => false,
                'current_period_starts_at' => $now->copy()->subMonths(8),
                'current_period_ends_at' => $now->copy()->addMonths(4),
                'billing_cycle_anchor_date' => $now->copy()->subMonths(8),
                'canceled_at' => null,
                'cancellation_reason' => null,
                'prorate' => true,
                'proration_amount' => null,
                'proration_date' => null,
                'gateway' => 'bank_transfer',
                'gateway_subscription_id' => null,
                'gateway_customer_id' => null,
                'gateway_metadata' => null,
                'metadata' => json_encode(['source' => 'sales', 'account_manager' => 'sarah@company.com']),
                'history' => json_encode([
                    ['event' => 'created', 'date' => $now->copy()->subMonths(8)->toIso8601String()],
                    ['event' => 'payment_succeeded', 'date' => $now->copy()->subMonths(8)->toIso8601String()],
                ]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(8),
                'updated_at' => $now,
                'deleted_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $ids['user4'],
                'plan_id' => $paygPlan->id,
                'plan_price_id' => $paygPrice->id,
                'parent_subscription_id' => null,
                'status' => 'active',
                'billing_cycle_anchor' => 'creation',
                'quantity' => 1,
                'unit_price' => 0.00,
                'amount' => 0.00,
                'currency' => 'USD',
                'trial_starts_at' => $now->copy()->subDays(30),
                'trial_ends_at' => $now->copy()->subDays(23),
                'trial_converted' => true,
                'current_period_starts_at' => $now->copy()->subDays(23),
                'current_period_ends_at' => $now->copy()->addDays(7),
                'billing_cycle_anchor_date' => $now->copy()->subDays(23),
                'canceled_at' => null,
                'cancellation_reason' => null,
                'prorate' => true,
                'proration_amount' => null,
                'proration_date' => null,
                'gateway' => 'stripe',
                'gateway_subscription_id' => 'sub_'.Str::random(14),
                'gateway_customer_id' => 'cus_'.Str::random(14),
                'gateway_metadata' => json_encode(['payment_method' => 'pm_card_visa']),
                'metadata' => json_encode(['source' => 'web']),
                'history' => json_encode([
                    ['event' => 'created', 'date' => $now->copy()->subDays(30)->toIso8601String()],
                    ['event' => 'trial_started', 'date' => $now->copy()->subDays(30)->toIso8601String()],
                    ['event' => 'converted', 'date' => $now->copy()->subDays(23)->toIso8601String()],
                ]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now,
                'deleted_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $ids['user5'],
                'plan_id' => $freePlan->id,
                'plan_price_id' => $freePrice->id,
                'parent_subscription_id' => null,
                'status' => 'canceled',
                'billing_cycle_anchor' => 'creation',
                'quantity' => 1,
                'unit_price' => 0.00,
                'amount' => 0.00,
                'currency' => 'USD',
                'trial_starts_at' => null,
                'trial_ends_at' => null,
                'trial_converted' => false,
                'current_period_starts_at' => $now->copy()->subMonths(6),
                'current_period_ends_at' => $now->copy()->subMonths(5),
                'billing_cycle_anchor_date' => $now->copy()->subMonths(6),
                'canceled_at' => $now->copy()->subMonths(5),
                'cancellation_reason' => 'customer',
                'prorate' => true,
                'proration_amount' => null,
                'proration_date' => null,
                'gateway' => 'stripe',
                'gateway_subscription_id' => 'sub_'.Str::random(14),
                'gateway_customer_id' => 'cus_'.Str::random(14),
                'gateway_metadata' => json_encode(['payment_method' => 'pm_card_visa']),
                'metadata' => json_encode(['source' => 'web', 'cancellation_reason' => 'too_expensive']),
                'history' => json_encode([
                    ['event' => 'created', 'date' => $now->copy()->subMonths(6)->toIso8601String()],
                    ['event' => 'canceled', 'date' => $now->copy()->subMonths(5)->toIso8601String()],
                ]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(6),
                'updated_at' => $now->copy()->subMonths(5),
                'deleted_at' => null,
                'is_active' => false,
            ],
            [
                'user_id' => $ids['user_admin'],
                'plan_id' => $freePlan->id,
                'plan_price_id' => $freePrice->id,
                'parent_subscription_id' => null,
                'status' => 'trialing',
                'billing_cycle_anchor' => 'creation',
                'quantity' => 1,
                'unit_price' => 0.00,
                'amount' => 0.00,
                'currency' => 'USD',
                'trial_starts_at' => $now->copy()->subDays(5),
                'trial_ends_at' => $now->copy()->addDays(9),
                'trial_converted' => false,
                'current_period_starts_at' => $now->copy()->subDays(5),
                'current_period_ends_at' => $now->copy()->addDays(9),
                'billing_cycle_anchor_date' => $now->copy()->subDays(5),
                'canceled_at' => null,
                'cancellation_reason' => null,
                'prorate' => true,
                'proration_amount' => null,
                'proration_date' => null,
                'gateway' => 'stripe',
                'gateway_subscription_id' => 'sub_'.Str::random(14),
                'gateway_customer_id' => 'cus_'.Str::random(14),
                'gateway_metadata' => json_encode(['payment_method' => 'pm_card_visa']),
                'metadata' => json_encode(['source' => 'web']),
                'history' => json_encode([
                    ['event' => 'created', 'date' => $now->copy()->subDays(5)->toIso8601String()],
                    ['event' => 'trial_started', 'date' => $now->copy()->subDays(5)->toIso8601String()],
                ]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now,
                'deleted_at' => null,
                'is_active' => true,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->insertGetId($subscription);
        }
    }

    /**
     * Seed subscription_items table
     */
    private function seedSubscriptionItems(array $ids): void
    {
        $now = Carbon::now();

        $subscriptions = DB::table('subscriptions')->get();

        $items = [];

        foreach ($subscriptions as $subscription) {
            // Get features for this plan
            $planFeatures = DB::table('plan_features')
                ->where('plan_id', $subscription->plan_id)
                ->where('effective_from', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->whereNull('effective_to')
                        ->orWhere('effective_to', '>', $now);
                })
                ->get();

            foreach ($planFeatures as $index => $planFeature) {
                $items[] = [
                    'subscription_id' => $subscription->id,
                    'plan_price_id' => $subscription->plan_price_id,
                    'feature_id' => $planFeature->feature_id,
                    'quantity' => $subscription->quantity,
                    'unit_price' => $subscription->unit_price / max(1, $planFeatures->count()),
                    'amount' => $subscription->amount / max(1, $planFeatures->count()),
                    'metadata' => json_encode(['included' => true]),
                    'tiers' => null,
                    'effective_from' => $subscription->current_period_starts_at,
                    'effective_to' => $subscription->status === 'canceled' ? $subscription->canceled_at : null,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $subscription->created_at,
                    'updated_at' => $subscription->updated_at,
                    'deleted_at' => null,
                ];
            }
        }

        if (! empty($items)) {
            DB::table('subscription_items')->insert($items);
        }
    }

    /**
     * Seed invoices table
     */
    private function seedInvoices(array $ids): void
    {
        $now = Carbon::now();

        // Get subscriptions
        $subscriptions = DB::table('subscriptions')->where('status', 'active')->get();

        $invoices = [];

        foreach ($subscriptions as $index => $subscription) {
            if ($subscription->amount <= 0) {
                continue;
            }

            $issueDate = $subscription->current_period_starts_at;
            $dueDate = Carbon::parse($subscription->current_period_starts_at)->addDays(5);
            $paidAt = $subscription->created_at < $now->copy()->subDays(10) ? $issueDate : null;

            $tax = $subscription->user_id == $ids['user2'] ? $subscription->amount * 0.0825 : 0;
            $total = $subscription->amount + $tax;

            $invoices[] = [
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'number' => 'INV-'.date('Ymd', strtotime($issueDate)).'-'.str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'external_id' => 'in_'.Str::random(14),
                'type' => 'subscription',
                'status' => $paidAt ? 'paid' : 'open',
                'subtotal' => $subscription->amount,
                'tax' => $tax,
                'total' => $total,
                'amount_due' => $total,
                'amount_paid' => $paidAt ? $total : 0,
                'currency' => 'USD',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'paid_at' => $paidAt,
                'finalized_at' => $issueDate,
                'line_items' => json_encode([
                    ['description' => 'Subscription - '.date('M Y', strtotime($issueDate)), 'amount' => $subscription->amount, 'quantity' => 1],
                ]),
                'tax_rates' => $tax > 0 ? json_encode([['name' => 'Sales Tax', 'rate' => 8.25, 'amount' => $tax]]) : null,
                'discounts' => null,
                'metadata' => json_encode(['source' => 'subscription']),
                'history' => json_encode([
                    ['status' => 'open', 'date' => $issueDate],
                    $paidAt ? ['status' => 'paid', 'date' => $paidAt] : null,
                ]),
                'pdf_url' => $paidAt ? 'https://example.com/invoices/'.Str::random(10).'.pdf' : null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $issueDate,
                'updated_at' => $paidAt ?: $issueDate,
                'deleted_at' => null,
            ];
        }

        if (! empty($invoices)) {
            DB::table('invoices')->insert($invoices);
        }
    }

    /**
     * Seed payments table
     */
    private function seedPayments(array $ids): void
    {
        $now = Carbon::now();

        $invoices = DB::table('invoices')->where('status', 'paid')->get();

        $payments = [];

        foreach ($invoices as $invoice) {
            $fee = $invoice->total * 0.029 + 0.30; // 2.9% + $0.30
            $gateway = $invoice->user_id == $ids['user2'] ? 'paypal' : 'stripe';

            $payments[] = [
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'external_id' => $gateway == 'stripe' ? 'ch_'.Str::random(14) : 'pay_'.Str::random(17),
                'type' => $gateway == 'stripe' ? 'card' : 'wallet',
                'status' => 'completed',
                'amount' => $invoice->total,
                'fee' => $fee,
                'currency' => 'USD',
                'gateway' => $gateway,
                'gateway_response' => json_encode(['id' => $gateway == 'stripe' ? 'ch_'.Str::random(14) : 'pay_'.Str::random(17)]),
                'payment_method' => json_encode($gateway == 'stripe' ?
                    ['brand' => 'visa', 'last4' => '4242'] :
                    ['type' => 'paypal_account', 'email' => 'user@example.com']
                ),
                'processed_at' => $invoice->paid_at,
                'refunded_at' => null,
                'metadata' => json_encode(['invoice_number' => $invoice->number]),
                'fraud_indicators' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $invoice->paid_at,
                'updated_at' => $invoice->paid_at,
                'deleted_at' => null,
            ];
        }

        if (! empty($payments)) {
            DB::table('payments')->insert($payments);
        }
    }

    /**
     * Seed payment_masters and related tables
     */
    private function seedPaymentMasters(array $ids): void
    {
        $now = Carbon::now();

        $payments = DB::table('payments')->get();

        foreach ($payments as $payment) {
            // Create payment master
            $paymentMasterId = DB::table('payment_masters')->insertGetId([
                'user_id' => $payment->user_id,
                'payment_number' => 'PMT-'.date('Ymd', strtotime($payment->processed_at)).'-'.rand(10000, 99999),
                'type' => 'subscription',
                'status' => 'paid',
                'total_amount' => $payment->amount,
                'subtotal' => $payment->amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'fee_amount' => $payment->fee,
                'net_amount' => $payment->amount - $payment->fee,
                'paid_amount' => $payment->amount,
                'currency' => 'USD',
                'exchange_rate' => 1.000000,
                'base_currency' => 'USD',
                'payment_method' => $payment->type == 'card' ? 'stripe' : 'paypal',
                'payment_method_details' => $payment->payment_method,
                'payment_gateway' => $payment->gateway,
                'is_installment' => false,
                'installment_count' => null,
                'installment_frequency' => null,
                'payment_date' => $payment->processed_at,
                'due_date' => $payment->processed_at,
                'paid_at' => $payment->processed_at,
                'cancelled_at' => null,
                'expires_at' => null,
                'customer_reference' => null,
                'bank_reference' => null,
                'gateway_reference' => $payment->external_id,
                'metadata' => json_encode(['invoice_id' => $payment->invoice_id]),
                'custom_fields' => null,
                'notes' => null,
                'failure_reason' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $payment->processed_at,
                'updated_at' => $payment->processed_at,
                'deleted_at' => null,
            ]);

            // Get invoice and subscription details
            $invoice = DB::table('invoices')->find($payment->invoice_id);
            $subscription = DB::table('subscriptions')->find($invoice->subscription_id);
            $plan = DB::table('plans')->find($subscription->plan_id);

            // Create payment child
            $paymentChildId = DB::table('payment_children')->insertGetId([
                'payment_master_id' => $paymentMasterId,
                'item_type' => 'invoice',
                'item_id' => $invoice->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'invoice_id' => $invoice->id,
                'description' => $plan->name.' - Monthly subscription',
                'item_code' => $plan->code,
                'unit_price' => $subscription->unit_price,
                'quantity' => $subscription->quantity,
                'amount' => $subscription->amount,
                'tax_amount' => $invoice->tax,
                'discount_amount' => 0,
                'total_amount' => $invoice->total,
                'period_start' => date('Y-m-d', strtotime($subscription->current_period_starts_at)),
                'period_end' => date('Y-m-d', strtotime($subscription->current_period_ends_at)),
                'billing_cycle' => 'monthly',
                'status' => 'paid',
                'paid_at' => $payment->processed_at,
                'allocated_amount' => $payment->amount,
                'metadata' => null,
                'tax_breakdown' => $invoice->tax > 0 ? json_encode([['name' => 'Sales Tax', 'rate' => 8.25, 'amount' => $invoice->tax]]) : null,
                'discount_breakdown' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $payment->processed_at,
                'updated_at' => $payment->processed_at,
                'deleted_at' => null,
            ]);

            // Create payment transaction
            $paymentTransactionId = DB::table('payment_transactions')->insertGetId([
                'payment_master_id' => $paymentMasterId,
                'payment_child_id' => $paymentChildId,
                'transaction_id' => $payment->gateway == 'stripe' ? 'txn_'.Str::random(14) : 'pay_'.Str::random(17),
                'reference_id' => $payment->external_id,
                'type' => 'payment',
                'payment_method' => $payment->gateway == 'stripe' ? 'stripe' : 'paypal',
                'payment_gateway' => $payment->gateway,
                'gateway_response' => json_encode(['id' => $payment->external_id, 'status' => 'succeeded']),
                'payment_method_details' => $payment->payment_method,
                'amount' => $payment->amount,
                'fee' => $payment->fee,
                'tax' => 0,
                'currency' => 'USD',
                'exchange_rate' => 1.000000,
                'status' => 'completed',
                'card_last4' => $payment->type == 'card' ? '4242' : null,
                'card_brand' => $payment->type == 'card' ? 'visa' : null,
                'card_country' => $payment->type == 'card' ? 'US' : null,
                'card_exp_month' => $payment->type == 'card' ? 12 : null,
                'card_exp_year' => $payment->type == 'card' ? 2026 : null,
                'bank_name' => null,
                'bank_account_last4' => null,
                'bank_routing_number' => null,
                'wallet_type' => $payment->type == 'wallet' ? 'paypal' : null,
                'wallet_number' => $payment->type == 'wallet' ? 'user@example.com' : null,
                'wallet_transaction_id' => $payment->type == 'wallet' ? 'PAY-'.strtoupper(Str::random(10)) : null,
                'installment_number' => null,
                'total_installments' => null,
                'initiated_at' => $payment->processed_at,
                'authorized_at' => $payment->processed_at,
                'captured_at' => $payment->processed_at,
                'completed_at' => $payment->processed_at,
                'failed_at' => null,
                'refunded_at' => null,
                'fraud_indicators' => null,
                'risk_score' => 5.00,
                'requires_review' => false,
                'metadata' => null,
                'custom_fields' => null,
                'notes' => null,
                'failure_reason' => null,
                'ip_address' => '192.168.1.'.rand(1, 255),
                'user_agent' => 'Mozilla/5.0',
                'location_data' => json_encode(['country' => 'US', 'city' => 'New York']),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $payment->processed_at,
                'updated_at' => $payment->processed_at,
                'deleted_at' => null,
            ]);

            // Create payment allocation
            DB::table('payment_allocations')->insert([
                'payment_master_id' => $paymentMasterId,
                'payment_child_id' => $paymentChildId,
                'payment_transaction_id' => $paymentTransactionId,
                'allocatable_type' => 'invoice',
                'allocatable_id' => $invoice->id,
                'amount' => $payment->amount,
                'exchange_rate' => 1.000000,
                'currency' => 'USD',
                'allocation_reference' => null,
                'allocation_type' => 'payment',
                'is_reversed' => false,
                'reversed_at' => null,
                'reversal_id' => null,
                'metadata' => null,
                'notes' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $payment->processed_at,
                'updated_at' => $payment->processed_at,
                'deleted_at' => null,
            ]);
        }
    }

    /**
     * Seed usage_records table
     */
    private function seedUsageRecords(array $ids): void
    {
        $now = Carbon::now();

        $usageRecords = [];

        // Get PAYG subscription
        $paygPlan = DB::table('plans')->where('code', 'PAYG')->first();
        $paygSubscription = DB::table('subscriptions')
            ->where('plan_id', $paygPlan->id)
            ->where('status', 'active')
            ->first();

        if ($paygSubscription) {
            // Get features for this subscription
            $features = DB::table('features')
                ->whereIn('code', ['api_requests', 'storage_gb'])
                ->get();

            // Get subscription items
            $subscriptionItems = DB::table('subscription_items')
                ->where('subscription_id', $paygSubscription->id)
                ->get();

            $startDate = Carbon::parse($paygSubscription->current_period_starts_at);
            $endDate = Carbon::parse($paygSubscription->current_period_ends_at);

            // Generate daily usage records
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate && $currentDate <= $now) {
                foreach ($features as $feature) {
                    $subscriptionItem = $subscriptionItems->where('feature_id', $feature->id)->first();

                    if (! $subscriptionItem) {
                        continue;
                    }

                    $quantity = $feature->code === 'api_requests'
                        ? rand(500, 5000)
                        : rand(1, 50) / 10; // Storage in GB (0.1 to 5 GB)

                    $usageRecords[] = [
                        'subscription_id' => $paygSubscription->id,
                        'subscription_item_id' => $subscriptionItem->id,
                        'feature_id' => $feature->id,
                        'quantity' => $quantity,
                        'tier_quantity' => null,
                        'amount' => $feature->code === 'api_requests' ? $quantity * 0.0001 : $quantity * 0.10,
                        'unit' => $feature->code === 'api_requests' ? 'request' : 'gb',
                        'status' => $currentDate->lt($now) ? 'billed' : 'pending',
                        'recorded_at' => $currentDate,
                        'billing_date' => $currentDate->format('Y-m-d'),
                        'metadata' => json_encode(['source' => 'api']),
                        'dimensions' => null,
                        'created_by' => null,
                        'updated_by' => null,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                        'deleted_at' => null,
                    ];
                }
                $currentDate->addDay();
            }
        }

        // Add usage for other subscriptions
        $otherSubscriptions = DB::table('subscriptions')
            ->where('plan_id', '!=', $paygPlan->id)
            ->where('status', 'active')
            ->get();

        foreach ($otherSubscriptions as $subscription) {
            // Add some random usage records
            $apiFeature = DB::table('features')->where('code', 'api_requests')->first();
            $subscriptionItems = DB::table('subscription_items')
                ->where('subscription_id', $subscription->id)
                ->where('feature_id', $apiFeature->id)
                ->first();

            if ($apiFeature && $subscriptionItems) {
                for ($i = 0; $i < 5; $i++) {
                    $date = $now->copy()->subDays(rand(1, 30));
                    $usageRecords[] = [
                        'subscription_id' => $subscription->id,
                        'subscription_item_id' => $subscriptionItems->id,
                        'feature_id' => $apiFeature->id,
                        'quantity' => rand(100, 1000),
                        'tier_quantity' => null,
                        'amount' => null, // Not metered
                        'unit' => 'request',
                        'status' => 'billed',
                        'recorded_at' => $date,
                        'billing_date' => $date->format('Y-m-d'),
                        'metadata' => json_encode(['source' => 'api']),
                        'dimensions' => null,
                        'created_by' => null,
                        'updated_by' => null,
                        'created_at' => $date,
                        'updated_at' => $date,
                        'deleted_at' => null,
                    ];
                }
            }
        }

        if (! empty($usageRecords)) {
            DB::table('usage_records')->insert($usageRecords);
        }
    }

    /**
     * Seed discounts table
     */
    private function seedDiscounts(array $ids): void
    {
        $now = Carbon::now();

        $discounts = [
            [
                'code' => 'WELCOME20',
                'name' => 'Welcome Discount 20%',
                'type' => 'percentage',
                'amount' => 20.00,
                'currency' => null,
                'applies_to' => 'all',
                'applies_to_ids' => null,
                'max_redemptions' => 1000,
                'times_redeemed' => 145,
                'is_active' => true,
                'starts_at' => $now->copy()->subMonths(3),
                'expires_at' => $now->copy()->addMonths(3),
                'duration' => 'repeating',
                'duration_in_months' => 3,
                'metadata' => json_encode(['campaign' => 'welcome', 'source' => 'email']),
                'restrictions' => json_encode(['min_amount' => 10, 'new_customers_only' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'code' => 'SAVE10',
                'name' => 'Save $10',
                'type' => 'fixed',
                'amount' => 10.00,
                'currency' => 'USD',
                'applies_to' => 'plans',
                'applies_to_ids' => null, // Will be populated later
                'max_redemptions' => 500,
                'times_redeemed' => 78,
                'is_active' => true,
                'starts_at' => $now->copy()->subMonths(1),
                'expires_at' => $now->copy()->addMonths(2),
                'duration' => 'once',
                'duration_in_months' => null,
                'metadata' => json_encode(['campaign' => 'spring_sale']),
                'restrictions' => json_encode(['first_payment_only' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(1),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'code' => 'FREETRIAL',
                'name' => 'Free Trial',
                'type' => 'trial',
                'amount' => 14.00, // 14 days
                'currency' => null,
                'applies_to' => 'plans',
                'applies_to_ids' => null, // Will be populated later
                'max_redemptions' => null,
                'times_redeemed' => 230,
                'is_active' => true,
                'starts_at' => $now->copy()->subMonths(6),
                'expires_at' => null,
                'duration' => 'once',
                'duration_in_months' => null,
                'metadata' => json_encode(['campaign' => 'trial']),
                'restrictions' => json_encode(['new_customers_only' => true]),
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(6),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        // Get plan IDs for SAVE10 discount
        $starterPlan = DB::table('plans')->where('code', 'STARTER')->first();
        $proPlan = DB::table('plans')->where('code', 'PRO')->first();

        $discounts[1]['applies_to_ids'] = json_encode([$starterPlan->id, $proPlan->id]);

        // Get plan IDs for FREETRIAL discount
        $discounts[2]['applies_to_ids'] = json_encode([$proPlan->id]);

        DB::table('discounts')->insert($discounts);
    }

    /**
     * Seed subscription_events table
     */
    private function seedSubscriptionEvents(array $ids): void
    {
        $now = Carbon::now();

        $subscriptions = DB::table('subscriptions')->get();

        $events = [];

        foreach ($subscriptions as $subscription) {
            $events[] = [
                'subscription_id' => $subscription->id,
                'type' => 'created',
                'data' => json_encode(['plan' => $subscription->plan_id, 'amount' => $subscription->amount]),
                'changes' => json_encode(['status' => ['null', $subscription->status]]),
                'causer_id' => $subscription->user_id,
                'causer_type' => 'user',
                'ip_address' => '192.168.1.'.rand(1, 255),
                'user_agent' => 'Mozilla/5.0',
                'metadata' => json_encode(['source' => 'web']),
                'occurred_at' => $subscription->created_at,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $subscription->created_at,
                'updated_at' => $subscription->created_at,
                'deleted_at' => null,
            ];

            if ($subscription->trial_starts_at) {
                $events[] = [
                    'subscription_id' => $subscription->id,
                    'type' => 'trial_started',
                    'data' => json_encode(['trial_end' => $subscription->trial_ends_at]),
                    'changes' => null,
                    'causer_id' => $subscription->user_id,
                    'causer_type' => 'user',
                    'ip_address' => '192.168.1.'.rand(1, 255),
                    'user_agent' => 'Mozilla/5.0',
                    'metadata' => null,
                    'occurred_at' => $subscription->trial_starts_at,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $subscription->trial_starts_at,
                    'updated_at' => $subscription->trial_starts_at,
                    'deleted_at' => null,
                ];
            }

            if ($subscription->trial_converted) {
                $events[] = [
                    'subscription_id' => $subscription->id,
                    'type' => 'trial_ended',
                    'data' => json_encode(['converted' => true]),
                    'changes' => json_encode(['status' => ['trialing', 'active']]),
                    'causer_id' => null,
                    'causer_type' => 'system',
                    'ip_address' => null,
                    'user_agent' => null,
                    'metadata' => null,
                    'occurred_at' => $subscription->trial_ends_at,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $subscription->trial_ends_at,
                    'updated_at' => $subscription->trial_ends_at,
                    'deleted_at' => null,
                ];
            }

            if ($subscription->status === 'canceled') {
                $events[] = [
                    'subscription_id' => $subscription->id,
                    'type' => 'canceled',
                    'data' => json_encode(['reason' => $subscription->cancellation_reason]),
                    'changes' => json_encode(['status' => ['active', 'canceled']]),
                    'causer_id' => $subscription->user_id,
                    'causer_type' => 'user',
                    'ip_address' => '192.168.1.'.rand(1, 255),
                    'user_agent' => 'Mozilla/5.0',
                    'metadata' => null,
                    'occurred_at' => $subscription->canceled_at,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $subscription->canceled_at,
                    'updated_at' => $subscription->canceled_at,
                    'deleted_at' => null,
                ];
            }
        }

        // Add invoice events
        $invoices = DB::table('invoices')->where('status', 'paid')->get();
        foreach ($invoices as $invoice) {
            $events[] = [
                'subscription_id' => $invoice->subscription_id,
                'type' => 'invoice_created',
                'data' => json_encode(['invoice_id' => $invoice->id, 'amount' => $invoice->total]),
                'changes' => null,
                'causer_id' => null,
                'causer_type' => 'system',
                'ip_address' => null,
                'user_agent' => null,
                'metadata' => null,
                'occurred_at' => $invoice->issue_date,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $invoice->issue_date,
                'updated_at' => $invoice->issue_date,
                'deleted_at' => null,
            ];

            $events[] = [
                'subscription_id' => $invoice->subscription_id,
                'type' => 'payment_succeeded',
                'data' => json_encode(['invoice_id' => $invoice->id, 'amount' => $invoice->total]),
                'changes' => null,
                'causer_id' => null,
                'causer_type' => 'system',
                'ip_address' => null,
                'user_agent' => null,
                'metadata' => null,
                'occurred_at' => $invoice->paid_at,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $invoice->paid_at,
                'updated_at' => $invoice->paid_at,
                'deleted_at' => null,
            ];
        }

        DB::table('subscription_events')->insert($events);
    }

    /**
     * Seed payment_methods table
     */
    private function seedPaymentMethods(array $ids): void
    {
        $now = Carbon::now();

        $paymentMethods = [
            [
                'user_id' => $ids['user1'],
                'type' => 'card',
                'gateway' => 'stripe',
                'gateway_customer_id' => 'cus_'.Str::random(14),
                'gateway_payment_method_id' => 'pm_'.Str::random(14),
                'nickname' => 'Work Visa',
                'is_default' => true,
                'is_verified' => true,
                'card_last4' => '4242',
                'card_brand' => 'visa',
                'card_exp_month' => 12,
                'card_exp_year' => 2026,
                'card_country' => 'US',
                'bank_name' => null,
                'bank_account_last4' => null,
                'bank_account_type' => null,
                'bank_routing_number' => null,
                'wallet_type' => null,
                'wallet_number' => null,
                'crypto_currency' => null,
                'crypto_address' => null,
                'encrypted_data' => null,
                'fingerprint' => 'fp_'.Str::random(16),
                'is_compromised' => false,
                'metadata' => json_encode(['used_count' => 15]),
                'gateway_metadata' => json_encode(['cvc_check' => 'pass']),
                'verified_at' => $now->copy()->subDays(45),
                'verified_by' => null,
                'last_used_at' => $now->copy()->subDays(15),
                'usage_count' => 15,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(45),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'user_id' => $ids['user2'],
                'type' => 'digital_wallet',
                'gateway' => 'paypal',
                'gateway_customer_id' => 'paypal_cus_'.Str::random(10),
                'gateway_payment_method_id' => null,
                'nickname' => 'PayPal',
                'is_default' => true,
                'is_verified' => true,
                'card_last4' => null,
                'card_brand' => null,
                'card_exp_month' => null,
                'card_exp_year' => null,
                'card_country' => null,
                'bank_name' => null,
                'bank_account_last4' => null,
                'bank_account_type' => null,
                'bank_routing_number' => null,
                'wallet_type' => 'paypal',
                'wallet_number' => 'jane@example.com',
                'crypto_currency' => null,
                'crypto_address' => null,
                'encrypted_data' => null,
                'fingerprint' => 'fp_paypal_'.Str::random(16),
                'is_compromised' => false,
                'metadata' => json_encode(['used_count' => 8]),
                'gateway_metadata' => json_encode(['payer_status' => 'verified']),
                'verified_at' => $now->copy()->subDays(90),
                'verified_by' => null,
                'last_used_at' => $now->copy()->subDays(20),
                'usage_count' => 8,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(90),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'user_id' => $ids['user3'],
                'type' => 'bank_account',
                'gateway' => 'bank_transfer',
                'gateway_customer_id' => null,
                'gateway_payment_method_id' => null,
                'nickname' => 'Company Account',
                'is_default' => true,
                'is_verified' => false,
                'card_last4' => null,
                'card_brand' => null,
                'card_exp_month' => null,
                'card_exp_year' => null,
                'card_country' => null,
                'bank_name' => 'Chase',
                'bank_account_last4' => '1234',
                'bank_account_type' => 'checking',
                'bank_routing_number' => '021000021',
                'wallet_type' => null,
                'wallet_number' => null,
                'crypto_currency' => null,
                'crypto_address' => null,
                'encrypted_data' => null,
                'fingerprint' => 'fp_bank_'.Str::random(16),
                'is_compromised' => false,
                'metadata' => json_encode(['used_count' => 1]),
                'gateway_metadata' => null,
                'verified_at' => null,
                'verified_by' => null,
                'last_used_at' => $now->copy()->subMonths(8),
                'usage_count' => 1,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subMonths(8),
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        DB::table('payment_methods')->insert($paymentMethods);
    }

    /**
     * Seed subscription_orders table
     */
    private function seedSubscriptionOrders(array $ids): void
    {
        $now = Carbon::now();

        $subscriptions = DB::table('subscriptions')->whereIn('user_id', [$ids['user1'], $ids['user2']])->get();

        foreach ($subscriptions as $index => $subscription) {
            $user = DB::table('users')->find($subscription->user_id);
            $plan = DB::table('plans')->find($subscription->plan_id);

            $orderId = DB::table('subscription_orders')->insertGetId([
                'user_id' => $subscription->user_id,
                'payment_master_id' => null,
                'order_number' => 'ORD-'.date('Ymd', strtotime($subscription->created_at)).'-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'type' => 'new',
                'subtotal' => $subscription->amount,
                'tax_amount' => $subscription->user_id == $ids['user2'] ? $subscription->amount * 0.0825 : 0,
                'discount_amount' => $subscription->user_id == $ids['user2'] ? 99.99 : 0,
                'total_amount' => $subscription->user_id == $ids['user2'] ? $subscription->amount * 1.0825 - 99.99 : $subscription->amount,
                'currency' => 'USD',
                'customer_info' => json_encode([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]),
                'billing_address' => $user->billing_address,
                'ordered_at' => $subscription->created_at,
                'processed_at' => $subscription->created_at,
                'cancelled_at' => null,
                'coupon_code' => $subscription->user_id == $ids['user2'] ? 'WELCOME20' : null,
                'applied_discounts' => $subscription->user_id == $ids['user2'] ?
                    json_encode([['code' => 'WELCOME20', 'type' => 'percentage', 'amount' => 20, 'discount' => 99.99]]) : null,
                'metadata' => json_encode(['source' => 'web']),
                'notes' => $subscription->user_id == $ids['user2'] ? 'Customer used referral link' : null,
                'failure_reason' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $subscription->created_at,
                'updated_at' => $subscription->created_at,
                'deleted_at' => null,
            ]);

            // Add order item
            DB::table('subscription_order_items')->insert([
                'subscription_order_id' => $orderId,
                'plan_id' => $subscription->plan_id,
                'user_id' => $subscription->user_id,
                'plan_name' => $plan->name,
                'billing_cycle' => $plan->billing_period,
                'quantity' => $subscription->quantity,
                'recipient_user_id' => null,
                'recipient_info' => null,
                'unit_price' => $subscription->unit_price,
                'amount' => $subscription->amount,
                'tax_amount' => $subscription->user_id == $ids['user2'] ? $subscription->amount * 0.0825 : 0,
                'discount_amount' => $subscription->user_id == $ids['user2'] ? 99.99 : 0,
                'total_amount' => $subscription->user_id == $ids['user2'] ? $subscription->amount * 1.0825 - 99.99 : $subscription->amount,
                'start_date' => date('Y-m-d', strtotime($subscription->current_period_starts_at)),
                'end_date' => date('Y-m-d', strtotime($subscription->current_period_ends_at)),
                'subscription_id' => $subscription->id,
                'subscription_status' => 'created',
                'processing_error' => null,
                'processed_at' => $subscription->created_at,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $subscription->created_at,
                'updated_at' => $subscription->created_at,
                'deleted_at' => null,
            ]);
        }
    }

    /**
     * Seed metered_usage_aggregates table
     */
    private function seedMeteredUsageAggregates(array $ids): void
    {
        $now = Carbon::now();

        $aggregates = [];

        // Get PAYG subscription
        $paygPlan = DB::table('plans')->where('code', 'PAYG')->first();
        $paygSubscription = DB::table('subscriptions')
            ->where('plan_id', $paygPlan->id)
            ->where('status', 'active')
            ->first();

        if ($paygSubscription) {
            $apiFeature = DB::table('features')->where('code', 'api_requests')->first();
            $storageFeature = DB::table('features')->where('code', 'storage_gb')->first();

            // Daily aggregates for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = $now->copy()->subDays($i);

                // API requests aggregate
                $aggregates[] = [
                    'subscription_id' => $paygSubscription->id,
                    'feature_id' => $apiFeature->id,
                    'aggregate_date' => $date->format('Y-m-d'),
                    'aggregate_period' => 'daily',
                    'total_quantity' => rand(2000, 8000),
                    'tier1_quantity' => rand(1000, 5000),
                    'tier2_quantity' => rand(1000, 3000),
                    'tier3_quantity' => 0,
                    'total_amount' => rand(20, 80) / 100,
                    'record_count' => rand(10, 50),
                    'last_calculated_at' => $date,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $date,
                    'updated_at' => $date,
                    'deleted_at' => null,
                ];

                // Storage aggregate
                $aggregates[] = [
                    'subscription_id' => $paygSubscription->id,
                    'feature_id' => $storageFeature->id,
                    'aggregate_date' => $date->format('Y-m-d'),
                    'aggregate_period' => 'daily',
                    'total_quantity' => rand(10, 50) / 10,
                    'tier1_quantity' => rand(5, 30) / 10,
                    'tier2_quantity' => rand(5, 20) / 10,
                    'tier3_quantity' => 0,
                    'total_amount' => rand(10, 50) / 100,
                    'record_count' => rand(1, 5),
                    'last_calculated_at' => $date,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $date,
                    'updated_at' => $date,
                    'deleted_at' => null,
                ];
            }

            // Monthly aggregates
            $aggregates[] = [
                'subscription_id' => $paygSubscription->id,
                'feature_id' => $apiFeature->id,
                'aggregate_date' => $now->format('Y-m').'-01',
                'aggregate_period' => 'monthly',
                'total_quantity' => 125000,
                'tier1_quantity' => 100000,
                'tier2_quantity' => 25000,
                'tier3_quantity' => 0,
                'total_amount' => 125.00,
                'record_count' => 850,
                'last_calculated_at' => $now,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            $aggregates[] = [
                'subscription_id' => $paygSubscription->id,
                'feature_id' => $storageFeature->id,
                'aggregate_date' => $now->format('Y-m').'-01',
                'aggregate_period' => 'monthly',
                'total_quantity' => 8.5,
                'tier1_quantity' => 8.5,
                'tier2_quantity' => 0,
                'tier3_quantity' => 0,
                'total_amount' => 0.85,
                'record_count' => 30,
                'last_calculated_at' => $now,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        if (! empty($aggregates)) {
            DB::table('metered_usage_aggregates')->insert($aggregates);
        }
    }

    /**
     * Seed rate_limits table
     */
    private function seedRateLimits(array $ids): void
    {
        $now = Carbon::now();

        $subscriptions = DB::table('subscriptions')->where('status', 'active')->get();

        $rateLimits = [];

        foreach ($subscriptions as $subscription) {
            $apiRateLimit = DB::table('features')->where('code', 'api_rate_limit')->first();
            if ($apiRateLimit) {
                $rateLimits[] = [
                    'subscription_id' => $subscription->id,
                    'feature_id' => $apiRateLimit->id,
                    'key' => 'api_requests:'.$subscription->id,
                    'max_attempts' => 100,
                    'decay_seconds' => 60,
                    'remaining' => rand(0, 100),
                    'resets_at' => Carbon::now()->addMinutes(rand(1, 60)),
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];
            }
        }

        if (! empty($rateLimits)) {
            DB::table('rate_limits')->insert($rateLimits);
        }
    }

    /**
     * Seed refunds table
     */
    private function seedRefunds(array $ids): void
    {
        $now = Carbon::now();

        // Get first payment for refund example
        $payment = DB::table('payments')->first();
        $paymentMaster = DB::table('payment_masters')
            ->where('gateway_reference', $payment->external_id)
            ->first();
        $paymentTransaction = DB::table('payment_transactions')
            ->where('payment_master_id', $paymentMaster->id)
            ->first();

        if ($paymentMaster && $paymentTransaction) {
            DB::table('refunds')->insert([
                'payment_master_id' => $paymentMaster->id,
                'payment_transaction_id' => $paymentTransaction->id,
                'user_id' => $payment->user_id,
                'refund_number' => 'REF-'.date('Ymd').'-'.rand(10000, 99999),
                'type' => 'partial',
                'status' => 'completed',
                'initiated_by' => 'customer',
                'amount' => 10.00,
                'fee' => 0.00,
                'currency' => 'USD',
                'exchange_rate' => 1.000000,
                'reason' => 'requested_by_customer',
                'reason_details' => 'Customer requested partial refund due to service issue',
                'customer_comments' => 'Had some downtime last week',
                'requested_at' => $now->copy()->subDays(10),
                'approved_at' => $now->copy()->subDays(9),
                'approved_by' => null,
                'processed_at' => $now->copy()->subDays(8),
                'completed_at' => $now->copy()->subDays(8),
                'failed_at' => null,
                'gateway_refund_id' => 're_'.Str::random(14),
                'gateway_response' => json_encode(['id' => 're_'.Str::random(14), 'status' => 'succeeded']),
                'metadata' => json_encode(['reason_code' => 'service_issue']),
                'documents' => null,
                'processed_by' => null,
                'rejection_reason' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(8),
                'deleted_at' => null,
            ]);
        }
    }

    /**
     * Seed payment_webhook_logs table
     */
    private function seedPaymentWebhookLogs(array $ids): void
    {
        $now = Carbon::now();

        $payments = DB::table('payments')->take(2)->get();

        $webhookLogs = [];

        foreach ($payments as $index => $payment) {
            $gateway = DB::table('payment_gateways')->where('code', $payment->gateway)->first();
            $paymentTransaction = DB::table('payment_transactions')
                ->where('transaction_id', $payment->gateway == 'stripe' ? 'txn_'.substr($payment->external_id, 3) : $payment->external_id)
                ->first();

            $webhookLogs[] = [
                'payment_gateway_id' => $gateway->id,
                'gateway' => $payment->gateway,
                'event_type' => $payment->gateway == 'stripe' ? 'invoice.payment_succeeded' : 'PAYMENT.SALE.COMPLETED',
                'webhook_id' => $payment->gateway == 'stripe' ? 'evt_'.Str::random(14) : 'WH-'.Str::random(20),
                'reference_id' => $payment->external_id,
                'payment_transaction_id' => $paymentTransaction ? $paymentTransaction->id : null,
                'payload' => json_encode(['id' => 'evt_'.Str::random(14), 'type' => 'invoice.payment_succeeded']),
                'headers' => json_encode($payment->gateway == 'stripe' ?
                    ['stripe-signature' => 't='.time().',v1='.Str::random(40)] :
                    ['paypal-auth-algo' => 'SHA256withRSA']
                ),
                'response_code' => 200,
                'response_body' => 'Webhook processed successfully',
                'status' => 'processed',
                'processing_error' => null,
                'retry_count' => 0,
                'next_retry_at' => null,
                'received_at' => $payment->processed_at,
                'processed_at' => $payment->processed_at,
                'ip_address' => $payment->gateway == 'stripe' ? '54.187.174.169' : '66.211.168.91',
                'is_verified' => true,
                'verification_error' => null,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $payment->processed_at,
                'updated_at' => $payment->processed_at,
                'deleted_at' => null,
            ];
        }

        if (! empty($webhookLogs)) {
            DB::table('payment_webhook_logs')->insert($webhookLogs);
        }
    }
}
