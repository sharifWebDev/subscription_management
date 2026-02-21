<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\PlanPrice;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanService
{
    /**
     * Get all plans with optional filtering
     */
    public function getAllPlans(Request $request): LengthAwarePaginator
    {

        $length = $request->input('length', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sortColumnIndex = $request->input('order.0.column');
        $sortDirection = $request->input('order.0.dir', 'desc');

        $sortColumn = $sortColumnIndex === null ? 'id' : (new Plan)->getFillable()[$sortColumnIndex];

        $query = Plan::with(['planFeatures.feature', 'prices', 'discounts']);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('billing_period')) {
            $query->where('billing_period', $request->billing_period);
        }

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            });

        $query->orderBy($sortColumn, $sortDirection);

        return $length === -1
            ? $query->paginate($query->get()->count())
            : $query->paginate($length);
    }

    /**
     * Get plan by ID
     */
    public function getPlanById(int $id): ?Plan
    {
        return Plan::with(['planFeatures.feature', 'prices', 'discounts'])->find($id);
    }

    /**
     * Store a new plan
     */
    public function storePlan(array $data): Plan
    {
        try {
            DB::beginTransaction();

            // Create the plan
            $plan = Plan::create([
                'name' => $data['name'],
                'code' => $data['code'] ?? strtoupper(\Str::slug($data['name'], '_')),
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? 'recurring',
                'billing_period' => $data['billing_period'] ?? 'monthly',
                'billing_interval' => $data['billing_interval'] ?? 1,
                'is_active' => $data['is_active'] ?? true,
                'is_visible' => $data['is_visible'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_featured' => $data['is_featured'] ?? false,
                'metadata' => $data['metadata'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Save features if provided
            if (! empty($data['features'])) {
                $this->syncFeatures($plan->id, $data['features']);
            }

            // Save prices if provided
            if (! empty($data['prices'])) {
                $this->syncPrices($plan->id, $data['prices']);
            }

            // Save discounts if provided
            if (! empty($data['discounts'])) {
                $plan->discounts()->sync($data['discounts']);
            }

            DB::commit();

            // Load relationships
            return $plan->load(['planFeatures.feature', 'prices', 'discounts']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create plan: '.$e->getMessage());
            throw new Exception('Failed to create plan: '.$e->getMessage());
        }
    }

    /**
     * Update an existing plan
     */
    public function updatePlan(int $id, array $data): Plan
    {
        try {
            DB::beginTransaction();

            $plan = Plan::findOrFail($id);

            // Update plan details
            $plan->update([
                'name' => $data['name'] ?? $plan->name,
                'code' => $data['code'] ?? $plan->code,
                'description' => $data['description'] ?? $plan->description,
                'type' => $data['type'] ?? $plan->type,
                'billing_period' => $data['billing_period'] ?? $plan->billing_period,
                'billing_interval' => $data['billing_interval'] ?? $plan->billing_interval,
                'is_active' => $data['is_active'] ?? $plan->is_active,
                'is_visible' => $data['is_visible'] ?? $plan->is_visible,
                'sort_order' => $data['sort_order'] ?? $plan->sort_order,
                'is_featured' => $data['is_featured'] ?? $plan->is_featured,
                'metadata' => $data['metadata'] ?? $plan->metadata,
                'updated_by' => auth()->id(),
            ]);

            // Update features if provided
            if (isset($data['features'])) {
                $this->syncFeatures($plan->id, $data['features']);
            }

            // Update prices if provided
            if (isset($data['prices'])) {
                $this->syncPrices($plan->id, $data['prices']);
            }

            // Update discounts if provided
            if (isset($data['discounts'])) {
                $plan->discounts()->sync($data['discounts']);
            }

            DB::commit();

            // Load relationships
            return $plan->load(['planFeatures.feature', 'prices', 'discounts']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update plan: '.$e->getMessage());
            throw new Exception('Failed to update plan: '.$e->getMessage());
        }
    }

    /**
     * Delete a plan
     */
    public function deletePlan(int $id): bool
    {
        try {
            DB::beginTransaction();

            $plan = Plan::findOrFail($id);

            // Check if plan has active subscriptions
            if ($plan->subscriptions()->whereIn('status', ['active', 'trialing'])->exists()) {
                throw new Exception('Cannot delete plan with active subscriptions');
            }

            // Soft delete related records
            $plan->planFeatures()->delete();
            $plan->prices()->delete();
            $plan->discounts()->detach();

            // Soft delete the plan
            $plan->delete();

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete plan: '.$e->getMessage());
            throw new Exception('Failed to delete plan: '.$e->getMessage());
        }
    }

    /**
     * Sync plan features
     */
    protected function syncFeatures(int $planId, array $features): void
    {
        $existingFeatures = PlanFeature::where('plan_id', $planId)->get();
        $processedIds = [];

        foreach ($features as $featureData) {
            // Check if feature should be deleted
            if (! empty($featureData['_deleted'])) {
                if (! empty($featureData['id'])) {
                    PlanFeature::where('id', $featureData['id'])->delete();
                }

                continue;
            }

            $feature = [
                'plan_id' => $planId,
                'feature_id' => $featureData['feature_id'],
                'value' => $featureData['value'],
                'config' => $featureData['config'] ?? null,
                'sort_order' => $featureData['sort_order'] ?? 0,
                'is_inherited' => $featureData['is_inherited'] ?? false,
                'parent_feature_id' => $featureData['parent_feature_id'] ?? null,
                'effective_from' => $featureData['effective_from'] ?? now(),
                'effective_to' => $featureData['effective_to'] ?? null,
                'updated_by' => auth()->id(),
            ];

            if (! empty($featureData['id'])) {
                // Update existing feature
                $planFeature = PlanFeature::find($featureData['id']);
                if ($planFeature) {
                    $planFeature->update($feature);
                    $processedIds[] = $planFeature->id;
                }
            } else {
                // Create new feature
                $feature['created_by'] = auth()->id();
                $planFeature = PlanFeature::create($feature);
                $processedIds[] = $planFeature->id;
            }
        }

        // Delete features not in the processed list (but only if we're doing a full sync)
        // Comment this out if you want to keep features not included in the update
        // PlanFeature::where('plan_id', $planId)
        //     ->whereNotIn('id', $processedIds)
        //     ->delete();
    }

    /**
     * Sync plan prices
     */
    protected function syncPrices(int $planId, array $prices): void
    {
        $existingPrices = PlanPrice::where('plan_id', $planId)->get();
        $processedIds = [];

        foreach ($prices as $priceData) {
            // Check if price should be deleted
            if (! empty($priceData['_deleted'])) {
                if (! empty($priceData['id'])) {
                    PlanPrice::where('id', $priceData['id'])->delete();
                }

                continue;
            }

            $price = [
                'plan_id' => $planId,
                'currency' => $priceData['currency'],
                'amount' => $priceData['amount'],
                'interval' => $priceData['interval'],
                'interval_count' => $priceData['interval_count'] ?? 1,
                'usage_type' => $priceData['usage_type'] ?? 'licensed',
                'tiers' => $priceData['tiers'] ?? null,
                'transformations' => $priceData['transformations'] ?? null,
                'stripe_price_id' => $priceData['stripe_price_id'] ?? null,
                'active_from' => $priceData['active_from'] ?? now(),
                'active_to' => $priceData['active_to'] ?? null,
                'updated_by' => auth()->id(),
            ];

            if (! empty($priceData['id'])) {
                // Update existing price
                $planPrice = PlanPrice::find($priceData['id']);
                if ($planPrice) {
                    $planPrice->update($price);
                    $processedIds[] = $planPrice->id;
                }
            } else {
                // Create new price
                $price['created_by'] = auth()->id();
                $planPrice = PlanPrice::create($price);
                $processedIds[] = $planPrice->id;
            }
        }

        // Delete prices not in the processed list (but only if we're doing a full sync)
        // PlanPrice::where('plan_id', $planId)
        //     ->whereNotIn('id', $processedIds)
        //     ->delete();
    }

    public function findBySlug($slug): JsonResponse
    {
        try {
            $plan = $this->planService->getPlanBySlug($slug);

            if (! $plan) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PlanResource($plan));

        } catch (\Exception $e) {
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
