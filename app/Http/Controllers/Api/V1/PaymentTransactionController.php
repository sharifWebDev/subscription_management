<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentTransactionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentTransactionRequest;
use App\Http\Requests\UpdatePaymentTransactionRequest;
use App\Http\Resources\PaymentTransactionResource;
use App\Models\PaymentTransaction;
use App\Services\PaymentTransactionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    public function __construct(
        protected PaymentTransactionService $paymentTransactionService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentTransactions = $this->paymentTransactionService->getAllPaymentTransactions($request);

            return success('Records retrieved successfully', PaymentTransactionResource::collection($paymentTransactions));

        } catch (Exception $e) {

            info('Error retrieved PaymentTransaction!', [$e]);

            return error('Payment Transactions retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentTransactionRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentTransaction = $this->paymentTransactionService->storePaymentTransaction($dto);

    //         return success('Records saved successfully', new PaymentTransactionResource($paymentTransaction));

    //     } catch (Exception $e) {

    //         info('Payment Transactions data insert failed!', [$e]);
    //         return error('Payment Transactions insert failed!.');
    //     }
    // }

    public function store(StorePaymentTransactionRequest $request): JsonResponse
    {
        try {

            $paymentTransaction = $this->paymentTransactionService->storePaymentTransaction($request->validated());

            return success('Records saved successfully', new PaymentTransactionResource($paymentTransaction));

        } catch (Exception $e) {

            info('Payment Transactions data insert failed!', [$e]);

            return error('Payment Transactions insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentTransaction $payment_transaction) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentTransactionResource($payment_transaction));

    //     } catch (\Exception $e) {
    //         info('Payment Transactions data showing failed!', [$e]);
    //         return error('Payment Transactions retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_transaction = $this->paymentTransactionService->getPaymentTransactionById($id);

            if (! $payment_transaction) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentTransactionResource($payment_transaction));

        } catch (\Exception $e) {
            info('Payment Transactions data showing failed!', [$e]);

            return error('Payment Transactions retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentTransactionRequest $request, PaymentTransaction $payment_transaction): JsonResponse
    //   {
    //       try {

    //         $paymentTransaction = $this->paymentTransactionService->updatePaymentTransaction($payment_transaction->id, $request->validated());

    //         return success('Records updated successfully', new PaymentTransactionResource($paymentTransaction));

    //       } catch (\Exception $e) {
    //           info('Payment Transactions update failed!', [$e]);
    //           return error('Payment Transactions update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentTransactionRequest $request, int $id): JsonResponse
    {
        try {

            $paymentTransaction = $this->paymentTransactionService->getPaymentTransactionById($id);

            // $dto = new PaymentTransactionDto($request->validated());
            // $this->paymentTransactionService->updatePaymentTransaction($paymentTransaction->id, $dto->toArray());

            $this->paymentTransactionService->updatePaymentTransaction($paymentTransaction->id, $request->validated());

            return success('Records updated successfully', new PaymentTransactionResource($paymentTransaction));

        } catch (\Exception $e) {
            info('Payment Transactions update failed!', [$e]);

            return error('Payment Transactions update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentTransaction $payment_transaction): JsonResponse
    // {
    //     try {

    //         if (! $payment_transaction) {
    //             return response()->json(['message' => 'Payment Transactions not found!'], 404);
    //         }

    //         $paymentTransaction = $this->paymentTransactionService->deletePaymentTransaction($payment_transaction->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Transactions delete failed!', [$e]);
    //         return error('Payment Transactions delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentTransaction = $this->paymentTransactionService->getPaymentTransactionById($id);

            if (! $paymentTransaction) {
                return response()->json(['message' => 'Payment Transactions not found!'], 404);
            }

            $this->paymentTransactionService->deletePaymentTransaction($paymentTransaction->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Transactions delete failed!', [$e]);

            return error('Payment Transactions delete failed!.');
        }
    }
}
