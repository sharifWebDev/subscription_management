<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentAllocationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentAllocationRequest;
use App\Http\Requests\UpdatePaymentAllocationRequest;
use App\Http\Resources\PaymentAllocationResource;
use App\Models\PaymentAllocation;
use App\Services\PaymentAllocationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentAllocationController extends Controller
{
    public function __construct(
        protected PaymentAllocationService $paymentAllocationService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentAllocations = $this->paymentAllocationService->getAllPaymentAllocations($request);

            return success('Records retrieved successfully', PaymentAllocationResource::collection($paymentAllocations));

        } catch (Exception $e) {

            info('Error retrieved PaymentAllocation!', [$e]);

            return error('Payment Allocations retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentAllocationRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentAllocation = $this->paymentAllocationService->storePaymentAllocation($dto);

    //         return success('Records saved successfully', new PaymentAllocationResource($paymentAllocation));

    //     } catch (Exception $e) {

    //         info('Payment Allocations data insert failed!', [$e]);
    //         return error('Payment Allocations insert failed!.');
    //     }
    // }

    public function store(StorePaymentAllocationRequest $request): JsonResponse
    {
        try {

            $paymentAllocation = $this->paymentAllocationService->storePaymentAllocation($request->validated());

            return success('Records saved successfully', new PaymentAllocationResource($paymentAllocation));

        } catch (Exception $e) {

            info('Payment Allocations data insert failed!', [$e]);

            return error('Payment Allocations insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentAllocation $payment_allocation) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentAllocationResource($payment_allocation));

    //     } catch (\Exception $e) {
    //         info('Payment Allocations data showing failed!', [$e]);
    //         return error('Payment Allocations retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_allocation = $this->paymentAllocationService->getPaymentAllocationById($id);

            if (! $payment_allocation) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentAllocationResource($payment_allocation));

        } catch (\Exception $e) {
            info('Payment Allocations data showing failed!', [$e]);

            return error('Payment Allocations retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentAllocationRequest $request, PaymentAllocation $payment_allocation): JsonResponse
    //   {
    //       try {

    //         $paymentAllocation = $this->paymentAllocationService->updatePaymentAllocation($payment_allocation->id, $request->validated());

    //         return success('Records updated successfully', new PaymentAllocationResource($paymentAllocation));

    //       } catch (\Exception $e) {
    //           info('Payment Allocations update failed!', [$e]);
    //           return error('Payment Allocations update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentAllocationRequest $request, int $id): JsonResponse
    {
        try {

            $paymentAllocation = $this->paymentAllocationService->getPaymentAllocationById($id);

            // $dto = new PaymentAllocationDto($request->validated());
            // $this->paymentAllocationService->updatePaymentAllocation($paymentAllocation->id, $dto->toArray());

            $this->paymentAllocationService->updatePaymentAllocation($paymentAllocation->id, $request->validated());

            return success('Records updated successfully', new PaymentAllocationResource($paymentAllocation));

        } catch (\Exception $e) {
            info('Payment Allocations update failed!', [$e]);

            return error('Payment Allocations update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentAllocation $payment_allocation): JsonResponse
    // {
    //     try {

    //         if (! $payment_allocation) {
    //             return response()->json(['message' => 'Payment Allocations not found!'], 404);
    //         }

    //         $paymentAllocation = $this->paymentAllocationService->deletePaymentAllocation($payment_allocation->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Allocations delete failed!', [$e]);
    //         return error('Payment Allocations delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentAllocation = $this->paymentAllocationService->getPaymentAllocationById($id);

            if (! $paymentAllocation) {
                return response()->json(['message' => 'Payment Allocations not found!'], 404);
            }

            $this->paymentAllocationService->deletePaymentAllocation($paymentAllocation->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Allocations delete failed!', [$e]);

            return error('Payment Allocations delete failed!.');
        }
    }
}
