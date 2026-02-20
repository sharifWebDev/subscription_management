<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentWebhookLogDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentWebhookLogRequest;
use App\Http\Requests\UpdatePaymentWebhookLogRequest;
use App\Http\Resources\PaymentWebhookLogResource;
use App\Models\PaymentWebhookLog;
use App\Services\PaymentWebhookLogService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookLogController extends Controller
{
    public function __construct(
        protected PaymentWebhookLogService $paymentWebhookLogService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentWebhookLogs = $this->paymentWebhookLogService->getAllPaymentWebhookLogs($request);

            return success('Records retrieved successfully', PaymentWebhookLogResource::collection($paymentWebhookLogs));

        } catch (Exception $e) {

            info('Error retrieved PaymentWebhookLog!', [$e]);

            return error('Payment Webhook Logs retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentWebhookLogRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentWebhookLog = $this->paymentWebhookLogService->storePaymentWebhookLog($dto);

    //         return success('Records saved successfully', new PaymentWebhookLogResource($paymentWebhookLog));

    //     } catch (Exception $e) {

    //         info('Payment Webhook Logs data insert failed!', [$e]);
    //         return error('Payment Webhook Logs insert failed!.');
    //     }
    // }

    public function store(StorePaymentWebhookLogRequest $request): JsonResponse
    {
        try {

            $paymentWebhookLog = $this->paymentWebhookLogService->storePaymentWebhookLog($request->validated());

            return success('Records saved successfully', new PaymentWebhookLogResource($paymentWebhookLog));

        } catch (Exception $e) {

            info('Payment Webhook Logs data insert failed!', [$e]);

            return error('Payment Webhook Logs insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentWebhookLog $payment_webhook_log) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentWebhookLogResource($payment_webhook_log));

    //     } catch (\Exception $e) {
    //         info('Payment Webhook Logs data showing failed!', [$e]);
    //         return error('Payment Webhook Logs retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_webhook_log = $this->paymentWebhookLogService->getPaymentWebhookLogById($id);

            if (! $payment_webhook_log) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentWebhookLogResource($payment_webhook_log));

        } catch (\Exception $e) {
            info('Payment Webhook Logs data showing failed!', [$e]);

            return error('Payment Webhook Logs retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentWebhookLogRequest $request, PaymentWebhookLog $payment_webhook_log): JsonResponse
    //   {
    //       try {

    //         $paymentWebhookLog = $this->paymentWebhookLogService->updatePaymentWebhookLog($payment_webhook_log->id, $request->validated());

    //         return success('Records updated successfully', new PaymentWebhookLogResource($paymentWebhookLog));

    //       } catch (\Exception $e) {
    //           info('Payment Webhook Logs update failed!', [$e]);
    //           return error('Payment Webhook Logs update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentWebhookLogRequest $request, int $id): JsonResponse
    {
        try {

            $paymentWebhookLog = $this->paymentWebhookLogService->getPaymentWebhookLogById($id);

            // $dto = new PaymentWebhookLogDto($request->validated());
            // $this->paymentWebhookLogService->updatePaymentWebhookLog($paymentWebhookLog->id, $dto->toArray());

            $this->paymentWebhookLogService->updatePaymentWebhookLog($paymentWebhookLog->id, $request->validated());

            return success('Records updated successfully', new PaymentWebhookLogResource($paymentWebhookLog));

        } catch (\Exception $e) {
            info('Payment Webhook Logs update failed!', [$e]);

            return error('Payment Webhook Logs update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentWebhookLog $payment_webhook_log): JsonResponse
    // {
    //     try {

    //         if (! $payment_webhook_log) {
    //             return response()->json(['message' => 'Payment Webhook Logs not found!'], 404);
    //         }

    //         $paymentWebhookLog = $this->paymentWebhookLogService->deletePaymentWebhookLog($payment_webhook_log->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Webhook Logs delete failed!', [$e]);
    //         return error('Payment Webhook Logs delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentWebhookLog = $this->paymentWebhookLogService->getPaymentWebhookLogById($id);

            if (! $paymentWebhookLog) {
                return response()->json(['message' => 'Payment Webhook Logs not found!'], 404);
            }

            $this->paymentWebhookLogService->deletePaymentWebhookLog($paymentWebhookLog->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Webhook Logs delete failed!', [$e]);

            return error('Payment Webhook Logs delete failed!.');
        }
    }
}
