<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PlanViewController extends Controller
{
    /**
     * Display a listing of all visible plans.
     */
    public function index()
    {
        return view('website.plans.index');
    }

    /**
     * Display the specified plan.
     */
    public function show($slug)
    {
        return view('website.plans.show', compact('slug'));
    }

    public function findBySlug($slug): JsonResponse
    {
        try {
            $plan = $this->planService->getPlanBySlug($slug);

            if (! $plan) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PlanResource($plan));

        } catch (Exception $e) {
            info('Plans data showing failed!', [$e]);

            return error('Plans retrieval failed!');
        }
    }

    public function getPlanBySlug(string $slug): ?Plan
    {
        return Plan::with(['planFeatures.feature', 'prices', 'discounts'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_visible', true)
            ->first();
    }
}
