<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $payments = $this->paymentService->getAllPayments($request);

            return success('Records retrieved successfully', PaymentResource::collection($payments));

        } catch (Exception $e) {

            info('Error retrieved Payment!', [$e]);

            return error('Payments retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $payment = $this->paymentService->storePayment($dto);

    //         return success('Records saved successfully', new PaymentResource($payment));

    //     } catch (Exception $e) {

    //         info('Payments data insert failed!', [$e]);
    //         return error('Payments insert failed!.');
    //     }
    // }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {

            $payment = $this->paymentService->storePayment($request->validated());

            return success('Records saved successfully', new PaymentResource($payment));

        } catch (Exception $e) {

            info('Payments data insert failed!', [$e]);

            return error('Payments insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Payment $payment) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentResource($payment));

    //     } catch (\Exception $e) {
    //         info('Payments data showing failed!', [$e]);
    //         return error('Payments retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment = $this->paymentService->getPaymentById($id);

            if (! $payment) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentResource($payment));

        } catch (\Exception $e) {
            info('Payments data showing failed!', [$e]);

            return error('Payments retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    //   {
    //       try {

    //         $payment = $this->paymentService->updatePayment($payment->id, $request->validated());

    //         return success('Records updated successfully', new PaymentResource($payment));

    //       } catch (\Exception $e) {
    //           info('Payments update failed!', [$e]);
    //           return error('Payments update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentRequest $request, int $id): JsonResponse
    {
        try {

            $payment = $this->paymentService->getPaymentById($id);

            // $dto = new PaymentDto($request->validated());
            // $this->paymentService->updatePayment($payment->id, $dto->toArray());

            $this->paymentService->updatePayment($payment->id, $request->validated());

            return success('Records updated successfully', new PaymentResource($payment));

        } catch (\Exception $e) {
            info('Payments update failed!', [$e]);

            return error('Payments update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Payment $payment): JsonResponse
    // {
    //     try {

    //         if (! $payment) {
    //             return response()->json(['message' => 'Payments not found!'], 404);
    //         }

    //         $payment = $this->paymentService->deletePayment($payment->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payments delete failed!', [$e]);
    //         return error('Payments delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $payment = $this->paymentService->getPaymentById($id);

            if (! $payment) {
                return response()->json(['message' => 'Payments not found!'], 404);
            }

            $this->paymentService->deletePayment($payment->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payments delete failed!', [$e]);

            return error('Payments delete failed!.');
        }
    }

    // getMethods
    public function getMethods(): JsonResponse
    {
        return success('Methods retrieved successfully', $this->paymentService->getMethods());
    }

    // addMethod
    public function addMethod(Request $request): JsonResponse
    {
        $data = $request->all();

        return success('Methods added successfully', $this->paymentService->addMethod($data));
    }

    // removeMethod
    public function removeMethod(int $id): JsonResponse
    {
        return success('Methods removed successfully', $this->paymentService->removeMethod($id));
    }
}
