<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentMethodDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct(
        protected PaymentMethodService $paymentMethodService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentMethods = $this->paymentMethodService->getAllPaymentMethods($request);

            return success('Records retrieved successfully', PaymentMethodResource::collection($paymentMethods));

        } catch (Exception $e) {

            info('Error retrieved PaymentMethod!', [$e]);

            return error('Payment Methods retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentMethodRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentMethod = $this->paymentMethodService->storePaymentMethod($dto);

    //         return success('Records saved successfully', new PaymentMethodResource($paymentMethod));

    //     } catch (Exception $e) {

    //         info('Payment Methods data insert failed!', [$e]);
    //         return error('Payment Methods insert failed!.');
    //     }
    // }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        try {

            $paymentMethod = $this->paymentMethodService->storePaymentMethod($request->validated());

            return success('Records saved successfully', new PaymentMethodResource($paymentMethod));

        } catch (Exception $e) {

            info('Payment Methods data insert failed!', [$e]);

            return error('Payment Methods insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentMethod $payment_method) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentMethodResource($payment_method));

    //     } catch (\Exception $e) {
    //         info('Payment Methods data showing failed!', [$e]);
    //         return error('Payment Methods retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_method = $this->paymentMethodService->getPaymentMethodById($id);

            if (! $payment_method) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentMethodResource($payment_method));

        } catch (\Exception $e) {
            info('Payment Methods data showing failed!', [$e]);

            return error('Payment Methods retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentMethodRequest $request, PaymentMethod $payment_method): JsonResponse
    //   {
    //       try {

    //         $paymentMethod = $this->paymentMethodService->updatePaymentMethod($payment_method->id, $request->validated());

    //         return success('Records updated successfully', new PaymentMethodResource($paymentMethod));

    //       } catch (\Exception $e) {
    //           info('Payment Methods update failed!', [$e]);
    //           return error('Payment Methods update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentMethodRequest $request, int $id): JsonResponse
    {
        try {

            $paymentMethod = $this->paymentMethodService->getPaymentMethodById($id);

            // $dto = new PaymentMethodDto($request->validated());
            // $this->paymentMethodService->updatePaymentMethod($paymentMethod->id, $dto->toArray());

            $this->paymentMethodService->updatePaymentMethod($paymentMethod->id, $request->validated());

            return success('Records updated successfully', new PaymentMethodResource($paymentMethod));

        } catch (\Exception $e) {
            info('Payment Methods update failed!', [$e]);

            return error('Payment Methods update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentMethod $payment_method): JsonResponse
    // {
    //     try {

    //         if (! $payment_method) {
    //             return response()->json(['message' => 'Payment Methods not found!'], 404);
    //         }

    //         $paymentMethod = $this->paymentMethodService->deletePaymentMethod($payment_method->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Methods delete failed!', [$e]);
    //         return error('Payment Methods delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentMethod = $this->paymentMethodService->getPaymentMethodById($id);

            if (! $paymentMethod) {
                return response()->json(['message' => 'Payment Methods not found!'], 404);
            }

            $this->paymentMethodService->deletePaymentMethod($paymentMethod->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Methods delete failed!', [$e]);

            return error('Payment Methods delete failed!.');
        }
    }
}
