<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\MeteredUsageAggregateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeteredUsageAggregateRequest;
use App\Http\Requests\UpdateMeteredUsageAggregateRequest;
use App\Http\Resources\MeteredUsageAggregateResource;
use App\Models\MeteredUsageAggregate;
use App\Services\MeteredUsageAggregateService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeteredUsageAggregateController extends Controller
{
    public function __construct(
        protected MeteredUsageAggregateService $meteredUsageAggregateService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $meteredUsageAggregates = $this->meteredUsageAggregateService->getAllMeteredUsageAggregates($request);

            return success('Records retrieved successfully', MeteredUsageAggregateResource::collection($meteredUsageAggregates));

        } catch (Exception $e) {

            info('Error retrieved MeteredUsageAggregate!', [$e]);

            return error('Metered Usage Aggregates retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreMeteredUsageAggregateRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $meteredUsageAggregate = $this->meteredUsageAggregateService->storeMeteredUsageAggregate($dto);

    //         return success('Records saved successfully', new MeteredUsageAggregateResource($meteredUsageAggregate));

    //     } catch (Exception $e) {

    //         info('Metered Usage Aggregates data insert failed!', [$e]);
    //         return error('Metered Usage Aggregates insert failed!.');
    //     }
    // }

    public function store(StoreMeteredUsageAggregateRequest $request): JsonResponse
    {
        try {

            $meteredUsageAggregate = $this->meteredUsageAggregateService->storeMeteredUsageAggregate($request->validated());

            return success('Records saved successfully', new MeteredUsageAggregateResource($meteredUsageAggregate));

        } catch (Exception $e) {

            info('Metered Usage Aggregates data insert failed!', [$e]);

            return error('Metered Usage Aggregates insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(MeteredUsageAggregate $metered_usage_aggregate) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new MeteredUsageAggregateResource($metered_usage_aggregate));

    //     } catch (\Exception $e) {
    //         info('Metered Usage Aggregates data showing failed!', [$e]);
    //         return error('Metered Usage Aggregates retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $metered_usage_aggregate = $this->meteredUsageAggregateService->getMeteredUsageAggregateById($id);

            if (! $metered_usage_aggregate) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new MeteredUsageAggregateResource($metered_usage_aggregate));

        } catch (\Exception $e) {
            info('Metered Usage Aggregates data showing failed!', [$e]);

            return error('Metered Usage Aggregates retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateMeteredUsageAggregateRequest $request, MeteredUsageAggregate $metered_usage_aggregate): JsonResponse
    //   {
    //       try {

    //         $meteredUsageAggregate = $this->meteredUsageAggregateService->updateMeteredUsageAggregate($metered_usage_aggregate->id, $request->validated());

    //         return success('Records updated successfully', new MeteredUsageAggregateResource($meteredUsageAggregate));

    //       } catch (\Exception $e) {
    //           info('Metered Usage Aggregates update failed!', [$e]);
    //           return error('Metered Usage Aggregates update failed!.');
    //       }
    //   }

    public function update(UpdateMeteredUsageAggregateRequest $request, int $id): JsonResponse
    {
        try {

            $meteredUsageAggregate = $this->meteredUsageAggregateService->getMeteredUsageAggregateById($id);

            // $dto = new MeteredUsageAggregateDto($request->validated());
            // $this->meteredUsageAggregateService->updateMeteredUsageAggregate($meteredUsageAggregate->id, $dto->toArray());

            $this->meteredUsageAggregateService->updateMeteredUsageAggregate($meteredUsageAggregate->id, $request->validated());

            return success('Records updated successfully', new MeteredUsageAggregateResource($meteredUsageAggregate));

        } catch (\Exception $e) {
            info('Metered Usage Aggregates update failed!', [$e]);

            return error('Metered Usage Aggregates update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(MeteredUsageAggregate $metered_usage_aggregate): JsonResponse
    // {
    //     try {

    //         if (! $metered_usage_aggregate) {
    //             return response()->json(['message' => 'Metered Usage Aggregates not found!'], 404);
    //         }

    //         $meteredUsageAggregate = $this->meteredUsageAggregateService->deleteMeteredUsageAggregate($metered_usage_aggregate->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Metered Usage Aggregates delete failed!', [$e]);
    //         return error('Metered Usage Aggregates delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $meteredUsageAggregate = $this->meteredUsageAggregateService->getMeteredUsageAggregateById($id);

            if (! $meteredUsageAggregate) {
                return response()->json(['message' => 'Metered Usage Aggregates not found!'], 404);
            }

            $this->meteredUsageAggregateService->deleteMeteredUsageAggregate($meteredUsageAggregate->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Metered Usage Aggregates delete failed!', [$e]);

            return error('Metered Usage Aggregates delete failed!.');
        }
    }
}
