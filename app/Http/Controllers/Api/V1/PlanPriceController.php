<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PlanPriceDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanPriceRequest;
use App\Http\Requests\UpdatePlanPriceRequest;
use App\Http\Resources\PlanPriceResource;
use App\Models\PlanPrice;
use App\Services\PlanPriceService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanPriceController extends Controller
{
    public function __construct(
        protected PlanPriceService $planPriceService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $planPrices = $this->planPriceService->getAllPlanPrices($request);

            return success('Records retrieved successfully', PlanPriceResource::collection($planPrices));

        } catch (Exception $e) {

            info('Error retrieved PlanPrice!', [$e]);

            return error('Plan Prices retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePlanPriceRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $planPrice = $this->planPriceService->storePlanPrice($dto);

    //         return success('Records saved successfully', new PlanPriceResource($planPrice));

    //     } catch (Exception $e) {

    //         info('Plan Prices data insert failed!', [$e]);
    //         return error('Plan Prices insert failed!.');
    //     }
    // }

    public function store(StorePlanPriceRequest $request): JsonResponse
    {
        try {

            $planPrice = $this->planPriceService->storePlanPrice($request->validated());

            return success('Records saved successfully', new PlanPriceResource($planPrice));

        } catch (Exception $e) {

            info('Plan Prices data insert failed!', [$e]);

            return error('Plan Prices insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PlanPrice $plan_price) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PlanPriceResource($plan_price));

    //     } catch (\Exception $e) {
    //         info('Plan Prices data showing failed!', [$e]);
    //         return error('Plan Prices retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $plan_price = $this->planPriceService->getPlanPriceById($id);

            if (! $plan_price) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PlanPriceResource($plan_price));

        } catch (\Exception $e) {
            info('Plan Prices data showing failed!', [$e]);

            return error('Plan Prices retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePlanPriceRequest $request, PlanPrice $plan_price): JsonResponse
    //   {
    //       try {

    //         $planPrice = $this->planPriceService->updatePlanPrice($plan_price->id, $request->validated());

    //         return success('Records updated successfully', new PlanPriceResource($planPrice));

    //       } catch (\Exception $e) {
    //           info('Plan Prices update failed!', [$e]);
    //           return error('Plan Prices update failed!.');
    //       }
    //   }

    public function update(UpdatePlanPriceRequest $request, int $id): JsonResponse
    {
        try {

            $planPrice = $this->planPriceService->getPlanPriceById($id);

            // $dto = new PlanPriceDto($request->validated());
            // $this->planPriceService->updatePlanPrice($planPrice->id, $dto->toArray());

            $this->planPriceService->updatePlanPrice($planPrice->id, $request->validated());

            return success('Records updated successfully', new PlanPriceResource($planPrice));

        } catch (\Exception $e) {
            info('Plan Prices update failed!', [$e]);

            return error('Plan Prices update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PlanPrice $plan_price): JsonResponse
    // {
    //     try {

    //         if (! $plan_price) {
    //             return response()->json(['message' => 'Plan Prices not found!'], 404);
    //         }

    //         $planPrice = $this->planPriceService->deletePlanPrice($plan_price->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Plan Prices delete failed!', [$e]);
    //         return error('Plan Prices delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $planPrice = $this->planPriceService->getPlanPriceById($id);

            if (! $planPrice) {
                return response()->json(['message' => 'Plan Prices not found!'], 404);
            }

            $this->planPriceService->deletePlanPrice($planPrice->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Plan Prices delete failed!', [$e]);

            return error('Plan Prices delete failed!.');
        }
    }
}
