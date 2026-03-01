<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrudGeneratorController extends Controller
{
    protected $usageService;

    public function __construct(UsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    /**
     * Show CRUD generator form
     */
    public function create()
    {
        $user = Auth::user();

        // Get usage summary for CRUD generation
        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        $usageSummary = null;
        if ($subscription) {
            $usageSummary = $this->usageService->getCrudGenerationSummary($subscription->id);
        }

        return view('crud-generator.create', compact('usageSummary'));
    }

    /**
     * Generate CRUD (with usage check)
     */
    public function generate(Request $request)
    {
        // The usage middleware will automatically check if user can generate CRUD
        // If not allowed, it will return error before reaching here

        $user = Auth::user();

        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if (! $subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 403);
        }

        // Validate request
        $request->validate([
            'table_name' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'fields' => 'required|json',
        ]);

        try {
            // Record the usage
            $usageResult = $this->usageService->recordCrudGeneration(
                $subscription->id,
                [
                    'table_name' => $request->table_name,
                    'model_name' => $request->model_name,
                    'ip' => $request->ip(),
                ]
            );

            if (! $usageResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $usageResult['message'],
                ], 400);
            }

            // Here you would actually generate the CRUD
            // Artisan::call("make:crud {$request->table_name} ...");

            return response()->json([
                'success' => true,
                'message' => 'CRUD generated successfully',
                'data' => [
                    'usage' => $usageResult['data'],
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('CRUD generation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate CRUD: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get usage statistics
     */
    public function usageStats()
    {
        $user = Auth::user();

        $stats = $this->usageService->getUsageStatistics($user->id);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get usage forecast
     */
    public function usageForecast()
    {
        $user = Auth::user();

        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if (! $subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 404);
        }

        $forecast = $this->usageService->getUsageForecast($subscription->id);

        return response()->json([
            'success' => true,
            'data' => $forecast,
        ]);
    }
}
