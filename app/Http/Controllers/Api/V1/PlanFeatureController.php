<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PlanFeatureDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanFeatureRequest;
use App\Http\Requests\UpdatePlanFeatureRequest;
use App\Http\Resources\PlanFeatureResource;
use App\Models\PlanFeature;
use App\Services\PlanFeatureService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    public function __construct(
        protected PlanFeatureService $planFeatureService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $planFeatures = $this->planFeatureService->getAllPlanFeatures($request);

            return success('Records retrieved successfully', PlanFeatureResource::collection($planFeatures));

        } catch (Exception $e) {

            info('Error retrieved PlanFeature!', [$e]);

            return error('Plan Features retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePlanFeatureRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $planFeature = $this->planFeatureService->storePlanFeature($dto);

    //         return success('Records saved successfully', new PlanFeatureResource($planFeature));

    //     } catch (Exception $e) {

    //         info('Plan Features data insert failed!', [$e]);
    //         return error('Plan Features insert failed!.');
    //     }
    // }

    public function store(StorePlanFeatureRequest $request): JsonResponse
    {
        try {

            $planFeature = $this->planFeatureService->storePlanFeature($request->validated());

            return success('Records saved successfully', new PlanFeatureResource($planFeature));

        } catch (Exception $e) {

            info('Plan Features data insert failed!', [$e]);

            return error('Plan Features insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PlanFeature $plan_feature) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PlanFeatureResource($plan_feature));

    //     } catch (\Exception $e) {
    //         info('Plan Features data showing failed!', [$e]);
    //         return error('Plan Features retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $plan_feature = $this->planFeatureService->getPlanFeatureById($id);

            if (! $plan_feature) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PlanFeatureResource($plan_feature));

        } catch (\Exception $e) {
            info('Plan Features data showing failed!', [$e]);

            return error('Plan Features retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePlanFeatureRequest $request, PlanFeature $plan_feature): JsonResponse
    //   {
    //       try {

    //         $planFeature = $this->planFeatureService->updatePlanFeature($plan_feature->id, $request->validated());

    //         return success('Records updated successfully', new PlanFeatureResource($planFeature));

    //       } catch (\Exception $e) {
    //           info('Plan Features update failed!', [$e]);
    //           return error('Plan Features update failed!.');
    //       }
    //   }

    public function update(UpdatePlanFeatureRequest $request, int $id): JsonResponse
    {
        try {

            $planFeature = $this->planFeatureService->getPlanFeatureById($id);

            // $dto = new PlanFeatureDto($request->validated());
            // $this->planFeatureService->updatePlanFeature($planFeature->id, $dto->toArray());

            $this->planFeatureService->updatePlanFeature($planFeature->id, $request->validated());

            return success('Records updated successfully', new PlanFeatureResource($planFeature));

        } catch (\Exception $e) {
            info('Plan Features update failed!', [$e]);

            return error('Plan Features update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PlanFeature $plan_feature): JsonResponse
    // {
    //     try {

    //         if (! $plan_feature) {
    //             return response()->json(['message' => 'Plan Features not found!'], 404);
    //         }

    //         $planFeature = $this->planFeatureService->deletePlanFeature($plan_feature->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Plan Features delete failed!', [$e]);
    //         return error('Plan Features delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $planFeature = $this->planFeatureService->getPlanFeatureById($id);

            if (! $planFeature) {
                return response()->json(['message' => 'Plan Features not found!'], 404);
            }

            $this->planFeatureService->deletePlanFeature($planFeature->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Plan Features delete failed!', [$e]);

            return error('Plan Features delete failed!.');
        }
    }
}
