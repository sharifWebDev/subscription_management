<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->statefulApi();

        // jodi custom add korte chao:
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Exclude payment callback routes from CSRF protection
        $middleware->validateCsrfTokens(except: [
            // SSLCommerz
            'payment/sslcommerz/success',
            'payment/sslcommerz/fail',
            'payment/sslcommerz/cancel',
            'payment/sslcommerz/ipn',
            'payment/sslcommerz/*',

            // bKash
            'payment/bkash/callback',
            'payment/bkash/ipn',
            'payment/bkash/*',

            // Rocket
            'payment/rocket/callback',
            'payment/rocket/ipn',
            'payment/rocket/*',

            // Nagad
            'payment/nagad/callback',
            'payment/nagad/ipn',
            'payment/nagad/*',

            // SurjoPay
            'payment/surjopay/callback',
            'payment/surjopay/ipn',
            'payment/surjopay/*',

            // PayPal
            'payment/paypal/callback',
            'payment/paypal/ipn',
            'payment/paypal/*',

            // Stripe
            'payment/stripe/webhook',
            'payment/stripe/callback',
            'payment/stripe/*',

            // General payment paths
            'payment/*/callback',
            'payment/*/ipn',
            'payment/*/success',
            'payment/*/fail',
            'payment/*/cancel',

            // Webhooks
            'webhooks/*',
            'api/webhooks/*',
            'api/v1/webhooks/*',
            ]);

            // Add middleware aliases if needed
            $middleware->alias([
            'throttle.payment' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'usage' => \App\Http\Middleware\CheckUsage::class,
        ]);

        // Add middleware groups
        $middleware->group('payment', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // Handle CSRF token mismatch
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            Log::warning('CSRF token mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);

            // For payment callbacks, don't throw error
            if ($request->is('payment/*')) {
                Log::info('Payment callback with CSRF issue - attempting graceful handling', [
                    'url' => $request->fullUrl(),
                ]);

                // Redirect to appropriate payment page
                if ($request->is('payment/sslcommerz/success')) {
                    return redirect()->route('payment.sslcommerz.success', $request->query());
                }

                // For other payment callbacks, redirect to plans page
                return redirect()->route('website.plans.index')
                    ->with('info', 'Payment processing. Please check your subscription status.');
            }

            // For other requests, redirect to login
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        });

        // Handle 419 HTTP exception
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                // For payment callbacks, handle gracefully
                if ($request->is('payment/*')) {
                    Log::info('419 on payment callback - handling gracefully', [
                        'url' => $request->fullUrl(),
                    ]);

                    return redirect()->route('website.plans.index')
                        ->with('info', 'Payment received. We will verify and activate your subscription shortly.');
                }

                // For API requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired',
                        'status' => 419,
                    ], 419);
                }

                // For web requests
                return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
            }
        });

        // Handle Session store not set RuntimeException
        $exceptions->render(function (\RuntimeException $e, Request $request) {
            if ($e->getMessage() === 'Session store not set on request.') {
                Log::warning('Session store not set exception - handling gracefully', [
                    'url' => $request->fullUrl(),
                ]);

                if ($request->is('payment/*')) {
                    return redirect()->route('website.plans.index', ['payment_status' => 'pending']);
                }

                return redirect()->route('login');
            }
        });

        // Handle Method Not Allowed
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, Request $request) {
            // For payment callbacks, try to handle method mismatch gracefully
            if ($request->is('payment/sslcommerz/success')) {
                Log::info('Method not allowed on SSLCommerz success - redirecting', [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                ]);

                // If POST was used but GET is expected, redirect with query params
                if ($request->isMethod('post')) {
                    return redirect()->route('payment.sslcommerz.success', $request->input());
                }
            }

            // Re-throw if not a payment callback we can handle
            throw $e;
        });

        // Log all exceptions for debugging
        $exceptions->report(function (Throwable $e) {
            Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        // Ignore certain exceptions from reporting
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Validation\ValidationException::class,
            \Illuminate\Session\TokenMismatchException::class,
        ]);
    })->create();
