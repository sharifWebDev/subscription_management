<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrudGeneratorController extends Controller
{
    public function __construct(protected UsageService $usageService)
    {
    }

    /**
     * Show CRUD generator form
     */
    public function create(Request $request)
    {

        $usageData = $this->usageService->getCrudGenerationSummary($this->userId());

        // check ajax request
        if ($request->wantsJson()) {
            return response()->json([
            'success' => true,
            'data' => $usageData
        ]);
        }

        return view('crud-generator.create', compact('usageData'));
    }

    /**
     * Generate CRUD (with usage check)
     */
    public function generate(Request $request)
    {

        // Validate request
        $request->validate([
            'table_name' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'fields' => 'required|string|max:5000',
            ]);


        try {
            // Record the usage
            $usageResult = $this->usageService->recordCrudGeneration(
                $this->userId(),
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

        $stats = $this->usageService->getUsageStatistics($this->userId());

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

        $forecast = $this->usageService->getUsageForecast($this->userId());

        return response()->json([
            'success' => true,
            'data' => $forecast,
        ]);
    }

    protected function userId()
    {
        return Auth::user()->id;
    }
}
