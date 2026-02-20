<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\RateLimitDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateLimitRequest;
use App\Http\Requests\UpdateRateLimitRequest;
use App\Http\Resources\RateLimitResource;
use App\Models\RateLimit;
use App\Services\RateLimitService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RateLimitController extends Controller
{
    public function __construct(
        protected RateLimitService $rateLimitService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $rateLimits = $this->rateLimitService->getAllRateLimits($request);

            return success('Records retrieved successfully', RateLimitResource::collection($rateLimits));

        } catch (Exception $e) {

            info('Error retrieved RateLimit!', [$e]);

            return error('Rate Limits retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreRateLimitRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $rateLimit = $this->rateLimitService->storeRateLimit($dto);

    //         return success('Records saved successfully', new RateLimitResource($rateLimit));

    //     } catch (Exception $e) {

    //         info('Rate Limits data insert failed!', [$e]);
    //         return error('Rate Limits insert failed!.');
    //     }
    // }

    public function store(StoreRateLimitRequest $request): JsonResponse
    {
        try {

            $rateLimit = $this->rateLimitService->storeRateLimit($request->validated());

            return success('Records saved successfully', new RateLimitResource($rateLimit));

        } catch (Exception $e) {

            info('Rate Limits data insert failed!', [$e]);

            return error('Rate Limits insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(RateLimit $rate_limit) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new RateLimitResource($rate_limit));

    //     } catch (\Exception $e) {
    //         info('Rate Limits data showing failed!', [$e]);
    //         return error('Rate Limits retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $rate_limit = $this->rateLimitService->getRateLimitById($id);

            if (! $rate_limit) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new RateLimitResource($rate_limit));

        } catch (\Exception $e) {
            info('Rate Limits data showing failed!', [$e]);

            return error('Rate Limits retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateRateLimitRequest $request, RateLimit $rate_limit): JsonResponse
    //   {
    //       try {

    //         $rateLimit = $this->rateLimitService->updateRateLimit($rate_limit->id, $request->validated());

    //         return success('Records updated successfully', new RateLimitResource($rateLimit));

    //       } catch (\Exception $e) {
    //           info('Rate Limits update failed!', [$e]);
    //           return error('Rate Limits update failed!.');
    //       }
    //   }

    public function update(UpdateRateLimitRequest $request, int $id): JsonResponse
    {
        try {

            $rateLimit = $this->rateLimitService->getRateLimitById($id);

            // $dto = new RateLimitDto($request->validated());
            // $this->rateLimitService->updateRateLimit($rateLimit->id, $dto->toArray());

            $this->rateLimitService->updateRateLimit($rateLimit->id, $request->validated());

            return success('Records updated successfully', new RateLimitResource($rateLimit));

        } catch (\Exception $e) {
            info('Rate Limits update failed!', [$e]);

            return error('Rate Limits update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(RateLimit $rate_limit): JsonResponse
    // {
    //     try {

    //         if (! $rate_limit) {
    //             return response()->json(['message' => 'Rate Limits not found!'], 404);
    //         }

    //         $rateLimit = $this->rateLimitService->deleteRateLimit($rate_limit->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Rate Limits delete failed!', [$e]);
    //         return error('Rate Limits delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $rateLimit = $this->rateLimitService->getRateLimitById($id);

            if (! $rateLimit) {
                return response()->json(['message' => 'Rate Limits not found!'], 404);
            }

            $this->rateLimitService->deleteRateLimit($rateLimit->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Rate Limits delete failed!', [$e]);

            return error('Rate Limits delete failed!.');
        }
    }
}
