<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PlanDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $planService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $plans = $this->planService->getAllPlans($request);

            return success('Records retrieved successfully', PlanResource::collection($plans));

        } catch (Exception $e) {

            info('Error retrieved Plan!', [$e]);

            return error('Plans retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePlanRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $plan = $this->planService->storePlan($dto);

    //         return success('Records saved successfully', new PlanResource($plan));

    //     } catch (Exception $e) {

    //         info('Plans data insert failed!', [$e]);
    //         return error('Plans insert failed!.');
    //     }
    // }

    public function store(StorePlanRequest $request): JsonResponse
    {
        try {

            $plan = $this->planService->storePlan($request->validated());

            return success('Records saved successfully', new PlanResource($plan));

        } catch (Exception $e) {

            info('Plans data insert failed!', [$e]);

            return error('Plans insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Plan $plan) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PlanResource($plan));

    //     } catch (\Exception $e) {
    //         info('Plans data showing failed!', [$e]);
    //         return error('Plans retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $plan = $this->planService->getPlanById($id);

            if (! $plan) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PlanResource($plan));

        } catch (\Exception $e) {
            info('Plans data showing failed!', [$e]);

            return error('Plans retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    //   {
    //       try {

    //         $plan = $this->planService->updatePlan($plan->id, $request->validated());

    //         return success('Records updated successfully', new PlanResource($plan));

    //       } catch (\Exception $e) {
    //           info('Plans update failed!', [$e]);
    //           return error('Plans update failed!.');
    //       }
    //   }

    public function update(UpdatePlanRequest $request, int $id): JsonResponse
    {
        try {

            $plan = $this->planService->getPlanById($id);

            // $dto = new PlanDto($request->validated());
            // $this->planService->updatePlan($plan->id, $dto->toArray());

            $this->planService->updatePlan($plan->id, $request->validated());

            return success('Records updated successfully', new PlanResource($plan));

        } catch (\Exception $e) {
            info('Plans update failed!', [$e]);

            return error('Plans update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Plan $plan): JsonResponse
    // {
    //     try {

    //         if (! $plan) {
    //             return response()->json(['message' => 'Plans not found!'], 404);
    //         }

    //         $plan = $this->planService->deletePlan($plan->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Plans delete failed!', [$e]);
    //         return error('Plans delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $plan = $this->planService->getPlanById($id);

            if (! $plan) {
                return response()->json(['message' => 'Plans not found!'], 404);
            }

            $this->planService->deletePlan($plan->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Plans delete failed!', [$e]);

            return error('Plans delete failed!.');
        }
    }
}
