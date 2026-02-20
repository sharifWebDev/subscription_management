<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentGatewayDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentGatewayRequest;
use App\Http\Requests\UpdatePaymentGatewayRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGatewayService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentGateways = $this->paymentGatewayService->getAllPaymentGateways($request);

            return success('Records retrieved successfully', PaymentGatewayResource::collection($paymentGateways));

        } catch (Exception $e) {

            info('Error retrieved PaymentGateway!', [$e]);

            return error('Payment Gateways retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentGatewayRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentGateway = $this->paymentGatewayService->storePaymentGateway($dto);

    //         return success('Records saved successfully', new PaymentGatewayResource($paymentGateway));

    //     } catch (Exception $e) {

    //         info('Payment Gateways data insert failed!', [$e]);
    //         return error('Payment Gateways insert failed!.');
    //     }
    // }

    public function store(StorePaymentGatewayRequest $request): JsonResponse
    {
        try {

            $paymentGateway = $this->paymentGatewayService->storePaymentGateway($request->validated());

            return success('Records saved successfully', new PaymentGatewayResource($paymentGateway));

        } catch (Exception $e) {

            info('Payment Gateways data insert failed!', [$e]);

            return error('Payment Gateways insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentGateway $payment_gateway) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentGatewayResource($payment_gateway));

    //     } catch (\Exception $e) {
    //         info('Payment Gateways data showing failed!', [$e]);
    //         return error('Payment Gateways retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_gateway = $this->paymentGatewayService->getPaymentGatewayById($id);

            if (! $payment_gateway) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentGatewayResource($payment_gateway));

        } catch (\Exception $e) {
            info('Payment Gateways data showing failed!', [$e]);

            return error('Payment Gateways retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentGatewayRequest $request, PaymentGateway $payment_gateway): JsonResponse
    //   {
    //       try {

    //         $paymentGateway = $this->paymentGatewayService->updatePaymentGateway($payment_gateway->id, $request->validated());

    //         return success('Records updated successfully', new PaymentGatewayResource($paymentGateway));

    //       } catch (\Exception $e) {
    //           info('Payment Gateways update failed!', [$e]);
    //           return error('Payment Gateways update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentGatewayRequest $request, int $id): JsonResponse
    {
        try {

            $paymentGateway = $this->paymentGatewayService->getPaymentGatewayById($id);

            // $dto = new PaymentGatewayDto($request->validated());
            // $this->paymentGatewayService->updatePaymentGateway($paymentGateway->id, $dto->toArray());

            $this->paymentGatewayService->updatePaymentGateway($paymentGateway->id, $request->validated());

            return success('Records updated successfully', new PaymentGatewayResource($paymentGateway));

        } catch (\Exception $e) {
            info('Payment Gateways update failed!', [$e]);

            return error('Payment Gateways update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentGateway $payment_gateway): JsonResponse
    // {
    //     try {

    //         if (! $payment_gateway) {
    //             return response()->json(['message' => 'Payment Gateways not found!'], 404);
    //         }

    //         $paymentGateway = $this->paymentGatewayService->deletePaymentGateway($payment_gateway->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Gateways delete failed!', [$e]);
    //         return error('Payment Gateways delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentGateway = $this->paymentGatewayService->getPaymentGatewayById($id);

            if (! $paymentGateway) {
                return response()->json(['message' => 'Payment Gateways not found!'], 404);
            }

            $this->paymentGatewayService->deletePaymentGateway($paymentGateway->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Gateways delete failed!', [$e]);

            return error('Payment Gateways delete failed!.');
        }
    }
}
