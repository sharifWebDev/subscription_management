<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\SubscriptionOrderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionOrderRequest;
use App\Http\Requests\UpdateSubscriptionOrderRequest;
use App\Http\Resources\SubscriptionOrderResource;
use App\Models\SubscriptionOrder;
use App\Services\SubscriptionOrderService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionOrderController extends Controller
{
    public function __construct(
        protected SubscriptionOrderService $subscriptionOrderService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $subscriptionOrders = $this->subscriptionOrderService->getAllSubscriptionOrders($request);

            return success('Records retrieved successfully', SubscriptionOrderResource::collection($subscriptionOrders));

        } catch (Exception $e) {

            info('Error retrieved SubscriptionOrder!', [$e]);

            return error('Subscription Orders retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreSubscriptionOrderRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $subscriptionOrder = $this->subscriptionOrderService->storeSubscriptionOrder($dto);

    //         return success('Records saved successfully', new SubscriptionOrderResource($subscriptionOrder));

    //     } catch (Exception $e) {

    //         info('Subscription Orders data insert failed!', [$e]);
    //         return error('Subscription Orders insert failed!.');
    //     }
    // }

    public function store(StoreSubscriptionOrderRequest $request): JsonResponse
    {
        try {

            $subscriptionOrder = $this->subscriptionOrderService->storeSubscriptionOrder($request->validated());

            return success('Records saved successfully', new SubscriptionOrderResource($subscriptionOrder));

        } catch (Exception $e) {

            info('Subscription Orders data insert failed!', [$e]);

            return error('Subscription Orders insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(SubscriptionOrder $subscription_order) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new SubscriptionOrderResource($subscription_order));

    //     } catch (\Exception $e) {
    //         info('Subscription Orders data showing failed!', [$e]);
    //         return error('Subscription Orders retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $subscription_order = $this->subscriptionOrderService->getSubscriptionOrderById($id);

            if (! $subscription_order) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new SubscriptionOrderResource($subscription_order));

        } catch (\Exception $e) {
            info('Subscription Orders data showing failed!', [$e]);

            return error('Subscription Orders retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateSubscriptionOrderRequest $request, SubscriptionOrder $subscription_order): JsonResponse
    //   {
    //       try {

    //         $subscriptionOrder = $this->subscriptionOrderService->updateSubscriptionOrder($subscription_order->id, $request->validated());

    //         return success('Records updated successfully', new SubscriptionOrderResource($subscriptionOrder));

    //       } catch (\Exception $e) {
    //           info('Subscription Orders update failed!', [$e]);
    //           return error('Subscription Orders update failed!.');
    //       }
    //   }

    public function update(UpdateSubscriptionOrderRequest $request, int $id): JsonResponse
    {
        try {

            $subscriptionOrder = $this->subscriptionOrderService->getSubscriptionOrderById($id);

            // $dto = new SubscriptionOrderDto($request->validated());
            // $this->subscriptionOrderService->updateSubscriptionOrder($subscriptionOrder->id, $dto->toArray());

            $this->subscriptionOrderService->updateSubscriptionOrder($subscriptionOrder->id, $request->validated());

            return success('Records updated successfully', new SubscriptionOrderResource($subscriptionOrder));

        } catch (\Exception $e) {
            info('Subscription Orders update failed!', [$e]);

            return error('Subscription Orders update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(SubscriptionOrder $subscription_order): JsonResponse
    // {
    //     try {

    //         if (! $subscription_order) {
    //             return response()->json(['message' => 'Subscription Orders not found!'], 404);
    //         }

    //         $subscriptionOrder = $this->subscriptionOrderService->deleteSubscriptionOrder($subscription_order->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Subscription Orders delete failed!', [$e]);
    //         return error('Subscription Orders delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $subscriptionOrder = $this->subscriptionOrderService->getSubscriptionOrderById($id);

            if (! $subscriptionOrder) {
                return response()->json(['message' => 'Subscription Orders not found!'], 404);
            }

            $this->subscriptionOrderService->deleteSubscriptionOrder($subscriptionOrder->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Subscription Orders delete failed!', [$e]);

            return error('Subscription Orders delete failed!.');
        }
    }
}
