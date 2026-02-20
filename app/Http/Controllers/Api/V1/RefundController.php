<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\RefundDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefundRequest;
use App\Http\Requests\UpdateRefundRequest;
use App\Http\Resources\RefundResource;
use App\Models\Refund;
use App\Services\RefundService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct(
        protected RefundService $refundService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $refunds = $this->refundService->getAllRefunds($request);

            return success('Records retrieved successfully', RefundResource::collection($refunds));

        } catch (Exception $e) {

            info('Error retrieved Refund!', [$e]);

            return error('Refunds retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreRefundRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $refund = $this->refundService->storeRefund($dto);

    //         return success('Records saved successfully', new RefundResource($refund));

    //     } catch (Exception $e) {

    //         info('Refunds data insert failed!', [$e]);
    //         return error('Refunds insert failed!.');
    //     }
    // }

    public function store(StoreRefundRequest $request): JsonResponse
    {
        try {

            $refund = $this->refundService->storeRefund($request->validated());

            return success('Records saved successfully', new RefundResource($refund));

        } catch (Exception $e) {

            info('Refunds data insert failed!', [$e]);

            return error('Refunds insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Refund $refund) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new RefundResource($refund));

    //     } catch (\Exception $e) {
    //         info('Refunds data showing failed!', [$e]);
    //         return error('Refunds retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $refund = $this->refundService->getRefundById($id);

            if (! $refund) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new RefundResource($refund));

        } catch (\Exception $e) {
            info('Refunds data showing failed!', [$e]);

            return error('Refunds retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateRefundRequest $request, Refund $refund): JsonResponse
    //   {
    //       try {

    //         $refund = $this->refundService->updateRefund($refund->id, $request->validated());

    //         return success('Records updated successfully', new RefundResource($refund));

    //       } catch (\Exception $e) {
    //           info('Refunds update failed!', [$e]);
    //           return error('Refunds update failed!.');
    //       }
    //   }

    public function update(UpdateRefundRequest $request, int $id): JsonResponse
    {
        try {

            $refund = $this->refundService->getRefundById($id);

            // $dto = new RefundDto($request->validated());
            // $this->refundService->updateRefund($refund->id, $dto->toArray());

            $this->refundService->updateRefund($refund->id, $request->validated());

            return success('Records updated successfully', new RefundResource($refund));

        } catch (\Exception $e) {
            info('Refunds update failed!', [$e]);

            return error('Refunds update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Refund $refund): JsonResponse
    // {
    //     try {

    //         if (! $refund) {
    //             return response()->json(['message' => 'Refunds not found!'], 404);
    //         }

    //         $refund = $this->refundService->deleteRefund($refund->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Refunds delete failed!', [$e]);
    //         return error('Refunds delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $refund = $this->refundService->getRefundById($id);

            if (! $refund) {
                return response()->json(['message' => 'Refunds not found!'], 404);
            }

            $this->refundService->deleteRefund($refund->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Refunds delete failed!', [$e]);

            return error('Refunds delete failed!.');
        }
    }
}
