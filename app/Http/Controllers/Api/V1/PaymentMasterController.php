<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentMasterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMasterRequest;
use App\Http\Requests\UpdatePaymentMasterRequest;
use App\Http\Resources\PaymentMasterResource;
use App\Models\PaymentMaster;
use App\Services\PaymentMasterService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMasterController extends Controller
{
    public function __construct(
        protected PaymentMasterService $paymentMasterService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentMasters = $this->paymentMasterService->getAllPaymentMasters($request);

            return success('Records retrieved successfully', PaymentMasterResource::collection($paymentMasters));

        } catch (Exception $e) {

            info('Error retrieved PaymentMaster!', [$e]);

            return error('Payment Masters retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentMasterRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentMaster = $this->paymentMasterService->storePaymentMaster($dto);

    //         return success('Records saved successfully', new PaymentMasterResource($paymentMaster));

    //     } catch (Exception $e) {

    //         info('Payment Masters data insert failed!', [$e]);
    //         return error('Payment Masters insert failed!.');
    //     }
    // }

    public function store(StorePaymentMasterRequest $request): JsonResponse
    {
        try {

            $paymentMaster = $this->paymentMasterService->storePaymentMaster($request->validated());

            return success('Records saved successfully', new PaymentMasterResource($paymentMaster));

        } catch (Exception $e) {

            info('Payment Masters data insert failed!', [$e]);

            return error('Payment Masters insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentMaster $payment_master) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentMasterResource($payment_master));

    //     } catch (\Exception $e) {
    //         info('Payment Masters data showing failed!', [$e]);
    //         return error('Payment Masters retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_master = $this->paymentMasterService->getPaymentMasterById($id);

            if (! $payment_master) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentMasterResource($payment_master));

        } catch (\Exception $e) {
            info('Payment Masters data showing failed!', [$e]);

            return error('Payment Masters retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentMasterRequest $request, PaymentMaster $payment_master): JsonResponse
    //   {
    //       try {

    //         $paymentMaster = $this->paymentMasterService->updatePaymentMaster($payment_master->id, $request->validated());

    //         return success('Records updated successfully', new PaymentMasterResource($paymentMaster));

    //       } catch (\Exception $e) {
    //           info('Payment Masters update failed!', [$e]);
    //           return error('Payment Masters update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentMasterRequest $request, int $id): JsonResponse
    {
        try {

            $paymentMaster = $this->paymentMasterService->getPaymentMasterById($id);

            // $dto = new PaymentMasterDto($request->validated());
            // $this->paymentMasterService->updatePaymentMaster($paymentMaster->id, $dto->toArray());

            $this->paymentMasterService->updatePaymentMaster($paymentMaster->id, $request->validated());

            return success('Records updated successfully', new PaymentMasterResource($paymentMaster));

        } catch (\Exception $e) {
            info('Payment Masters update failed!', [$e]);

            return error('Payment Masters update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentMaster $payment_master): JsonResponse
    // {
    //     try {

    //         if (! $payment_master) {
    //             return response()->json(['message' => 'Payment Masters not found!'], 404);
    //         }

    //         $paymentMaster = $this->paymentMasterService->deletePaymentMaster($payment_master->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Masters delete failed!', [$e]);
    //         return error('Payment Masters delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentMaster = $this->paymentMasterService->getPaymentMasterById($id);

            if (! $paymentMaster) {
                return response()->json(['message' => 'Payment Masters not found!'], 404);
            }

            $this->paymentMasterService->deletePaymentMaster($paymentMaster->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Masters delete failed!', [$e]);

            return error('Payment Masters delete failed!.');
        }
    }
}
