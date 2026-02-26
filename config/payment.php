<?php

return [
    'gateways' => [
        'sslcommerz' => [
            'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        ],
        'surjopay' => [
            'sandbox' => env('SURJOPAY_SANDBOX', true),
            'merchant_id' => env('SURJOPAY_MERCHANT_ID'),
            'merchant_key' => env('SURJOPAY_MERCHANT_KEY'),
            'callback_url' => env('SURJOPAY_CALLBACK_URL', '/payment/surjopay/callback'),
        ],
        'paypal' => [
            'sandbox' => env('PAYPAL_SANDBOX', true),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'callback_url' => env('PAYPAL_CALLBACK_URL', '/payment/paypal/callback'),
        ],
        'stripe' => [
            'sandbox' => env('STRIPE_SANDBOX', true),
            'test_public_key' => env('STRIPE_TEST_PUBLIC_KEY'),
            'test_secret_key' => env('STRIPE_TEST_SECRET_KEY'),
            'live_public_key' => env('STRIPE_LIVE_PUBLIC_KEY'),
            'live_secret_key' => env('STRIPE_LIVE_SECRET_KEY'),
            'callback_url' => env('STRIPE_CALLBACK_URL', '/payment/stripe/callback'),
        ],
        'nagad' => [
            'sandbox' => env('NAGAD_SANDBOX', true),
            'merchant_id' => env('NAGAD_MERCHANT_ID'),
            'merchant_key' => env('NAGAD_MERCHANT_KEY'),
            'callback_url' => env('NAGAD_CALLBACK_URL', '/payment/nagad/callback'),
        ],
        'bkash' => [
            'sandbox' => env('BKASH_SANDBOX', true),
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'callback_url' => env('BKASH_CALLBACK_URL', '/payment/bkash/callback'),
        ],
        'rocket' => [
            'sandbox' => env('ROCKET_SANDBOX', true),
            'merchant_id' => env('ROCKET_MERCHANT_ID'),
            'merchant_key' => env('ROCKET_MERCHANT_KEY'),
            'callback_url' => env('ROCKET_CALLBACK_URL', '/payment/rocket/callback'),
            'ipn_url' => env('ROCKET_IPN_URL', '/payment/rocket/ipn'),
        ],

    ],
];
