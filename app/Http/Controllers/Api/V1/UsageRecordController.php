<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\UsageRecordDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsageRecordRequest;
use App\Http\Requests\UpdateUsageRecordRequest;
use App\Http\Resources\UsageRecordResource;
use App\Models\UsageRecord;
use App\Services\UsageRecordService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageRecordController extends Controller
{
    public function __construct(
        protected UsageRecordService $usageRecordService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $usageRecords = $this->usageRecordService->getAllUsageRecords($request);

            return success('Records retrieved successfully', UsageRecordResource::collection($usageRecords));

        } catch (Exception $e) {

            info('Error retrieved UsageRecord!', [$e]);

            return error('Usage Records retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreUsageRecordRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $usageRecord = $this->usageRecordService->storeUsageRecord($dto);

    //         return success('Records saved successfully', new UsageRecordResource($usageRecord));

    //     } catch (Exception $e) {

    //         info('Usage Records data insert failed!', [$e]);
    //         return error('Usage Records insert failed!.');
    //     }
    // }

    public function store(StoreUsageRecordRequest $request): JsonResponse
    {
        try {

            $usageRecord = $this->usageRecordService->storeUsageRecord($request->validated());

            return success('Records saved successfully', new UsageRecordResource($usageRecord));

        } catch (Exception $e) {

            info('Usage Records data insert failed!', [$e]);

            return error('Usage Records insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(UsageRecord $usage_record) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new UsageRecordResource($usage_record));

    //     } catch (\Exception $e) {
    //         info('Usage Records data showing failed!', [$e]);
    //         return error('Usage Records retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $usage_record = $this->usageRecordService->getUsageRecordById($id);

            if (! $usage_record) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new UsageRecordResource($usage_record));

        } catch (\Exception $e) {
            info('Usage Records data showing failed!', [$e]);

            return error('Usage Records retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateUsageRecordRequest $request, UsageRecord $usage_record): JsonResponse
    //   {
    //       try {

    //         $usageRecord = $this->usageRecordService->updateUsageRecord($usage_record->id, $request->validated());

    //         return success('Records updated successfully', new UsageRecordResource($usageRecord));

    //       } catch (\Exception $e) {
    //           info('Usage Records update failed!', [$e]);
    //           return error('Usage Records update failed!.');
    //       }
    //   }

    public function update(UpdateUsageRecordRequest $request, int $id): JsonResponse
    {
        try {

            $usageRecord = $this->usageRecordService->getUsageRecordById($id);

            // $dto = new UsageRecordDto($request->validated());
            // $this->usageRecordService->updateUsageRecord($usageRecord->id, $dto->toArray());

            $this->usageRecordService->updateUsageRecord($usageRecord->id, $request->validated());

            return success('Records updated successfully', new UsageRecordResource($usageRecord));

        } catch (\Exception $e) {
            info('Usage Records update failed!', [$e]);

            return error('Usage Records update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(UsageRecord $usage_record): JsonResponse
    // {
    //     try {

    //         if (! $usage_record) {
    //             return response()->json(['message' => 'Usage Records not found!'], 404);
    //         }

    //         $usageRecord = $this->usageRecordService->deleteUsageRecord($usage_record->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Usage Records delete failed!', [$e]);
    //         return error('Usage Records delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $usageRecord = $this->usageRecordService->getUsageRecordById($id);

            if (! $usageRecord) {
                return response()->json(['message' => 'Usage Records not found!'], 404);
            }

            $this->usageRecordService->deleteUsageRecord($usageRecord->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Usage Records delete failed!', [$e]);

            return error('Usage Records delete failed!.');
        }
    }
}
