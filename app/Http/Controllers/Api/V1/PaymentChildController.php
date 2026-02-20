<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\PaymentChildDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentChildRequest;
use App\Http\Requests\UpdatePaymentChildRequest;
use App\Http\Resources\PaymentChildResource;
use App\Models\PaymentChild;
use App\Services\PaymentChildService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentChildController extends Controller
{
    public function __construct(
        protected PaymentChildService $paymentChildService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $paymentChilds = $this->paymentChildService->getAllPaymentChilds($request);

            return success('Records retrieved successfully', PaymentChildResource::collection($paymentChilds));

        } catch (Exception $e) {

            info('Error retrieved PaymentChild!', [$e]);

            return error('Payment Children retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePaymentChildRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $paymentChild = $this->paymentChildService->storePaymentChild($dto);

    //         return success('Records saved successfully', new PaymentChildResource($paymentChild));

    //     } catch (Exception $e) {

    //         info('Payment Children data insert failed!', [$e]);
    //         return error('Payment Children insert failed!.');
    //     }
    // }

    public function store(StorePaymentChildRequest $request): JsonResponse
    {
        try {

            $paymentChild = $this->paymentChildService->storePaymentChild($request->validated());

            return success('Records saved successfully', new PaymentChildResource($paymentChild));

        } catch (Exception $e) {

            info('Payment Children data insert failed!', [$e]);

            return error('Payment Children insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(PaymentChild $payment_child) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new PaymentChildResource($payment_child));

    //     } catch (\Exception $e) {
    //         info('Payment Children data showing failed!', [$e]);
    //         return error('Payment Children retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $payment_child = $this->paymentChildService->getPaymentChildById($id);

            if (! $payment_child) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new PaymentChildResource($payment_child));

        } catch (\Exception $e) {
            info('Payment Children data showing failed!', [$e]);

            return error('Payment Children retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdatePaymentChildRequest $request, PaymentChild $payment_child): JsonResponse
    //   {
    //       try {

    //         $paymentChild = $this->paymentChildService->updatePaymentChild($payment_child->id, $request->validated());

    //         return success('Records updated successfully', new PaymentChildResource($paymentChild));

    //       } catch (\Exception $e) {
    //           info('Payment Children update failed!', [$e]);
    //           return error('Payment Children update failed!.');
    //       }
    //   }

    public function update(UpdatePaymentChildRequest $request, int $id): JsonResponse
    {
        try {

            $paymentChild = $this->paymentChildService->getPaymentChildById($id);

            // $dto = new PaymentChildDto($request->validated());
            // $this->paymentChildService->updatePaymentChild($paymentChild->id, $dto->toArray());

            $this->paymentChildService->updatePaymentChild($paymentChild->id, $request->validated());

            return success('Records updated successfully', new PaymentChildResource($paymentChild));

        } catch (\Exception $e) {
            info('Payment Children update failed!', [$e]);

            return error('Payment Children update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(PaymentChild $payment_child): JsonResponse
    // {
    //     try {

    //         if (! $payment_child) {
    //             return response()->json(['message' => 'Payment Children not found!'], 404);
    //         }

    //         $paymentChild = $this->paymentChildService->deletePaymentChild($payment_child->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Payment Children delete failed!', [$e]);
    //         return error('Payment Children delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $paymentChild = $this->paymentChildService->getPaymentChildById($id);

            if (! $paymentChild) {
                return response()->json(['message' => 'Payment Children not found!'], 404);
            }

            $this->paymentChildService->deletePaymentChild($paymentChild->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Payment Children delete failed!', [$e]);

            return error('Payment Children delete failed!.');
        }
    }
}
