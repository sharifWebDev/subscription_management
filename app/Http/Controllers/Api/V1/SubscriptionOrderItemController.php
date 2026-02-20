<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\SubscriptionOrderItemDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionOrderItemRequest;
use App\Http\Requests\UpdateSubscriptionOrderItemRequest;
use App\Http\Resources\SubscriptionOrderItemResource;
use App\Models\SubscriptionOrderItem;
use App\Services\SubscriptionOrderItemService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionOrderItemController extends Controller
{
    public function __construct(
        protected SubscriptionOrderItemService $subscriptionOrderItemService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $subscriptionOrderItems = $this->subscriptionOrderItemService->getAllSubscriptionOrderItems($request);

            return success('Records retrieved successfully', SubscriptionOrderItemResource::collection($subscriptionOrderItems));

        } catch (Exception $e) {

            info('Error retrieved SubscriptionOrderItem!', [$e]);

            return error('Subscription Order Items retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreSubscriptionOrderItemRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $subscriptionOrderItem = $this->subscriptionOrderItemService->storeSubscriptionOrderItem($dto);

    //         return success('Records saved successfully', new SubscriptionOrderItemResource($subscriptionOrderItem));

    //     } catch (Exception $e) {

    //         info('Subscription Order Items data insert failed!', [$e]);
    //         return error('Subscription Order Items insert failed!.');
    //     }
    // }

    public function store(StoreSubscriptionOrderItemRequest $request): JsonResponse
    {
        try {

            $subscriptionOrderItem = $this->subscriptionOrderItemService->storeSubscriptionOrderItem($request->validated());

            return success('Records saved successfully', new SubscriptionOrderItemResource($subscriptionOrderItem));

        } catch (Exception $e) {

            info('Subscription Order Items data insert failed!', [$e]);

            return error('Subscription Order Items insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(SubscriptionOrderItem $subscription_order_item) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new SubscriptionOrderItemResource($subscription_order_item));

    //     } catch (\Exception $e) {
    //         info('Subscription Order Items data showing failed!', [$e]);
    //         return error('Subscription Order Items retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $subscription_order_item = $this->subscriptionOrderItemService->getSubscriptionOrderItemById($id);

            if (! $subscription_order_item) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new SubscriptionOrderItemResource($subscription_order_item));

        } catch (\Exception $e) {
            info('Subscription Order Items data showing failed!', [$e]);

            return error('Subscription Order Items retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateSubscriptionOrderItemRequest $request, SubscriptionOrderItem $subscription_order_item): JsonResponse
    //   {
    //       try {

    //         $subscriptionOrderItem = $this->subscriptionOrderItemService->updateSubscriptionOrderItem($subscription_order_item->id, $request->validated());

    //         return success('Records updated successfully', new SubscriptionOrderItemResource($subscriptionOrderItem));

    //       } catch (\Exception $e) {
    //           info('Subscription Order Items update failed!', [$e]);
    //           return error('Subscription Order Items update failed!.');
    //       }
    //   }

    public function update(UpdateSubscriptionOrderItemRequest $request, int $id): JsonResponse
    {
        try {

            $subscriptionOrderItem = $this->subscriptionOrderItemService->getSubscriptionOrderItemById($id);

            // $dto = new SubscriptionOrderItemDto($request->validated());
            // $this->subscriptionOrderItemService->updateSubscriptionOrderItem($subscriptionOrderItem->id, $dto->toArray());

            $this->subscriptionOrderItemService->updateSubscriptionOrderItem($subscriptionOrderItem->id, $request->validated());

            return success('Records updated successfully', new SubscriptionOrderItemResource($subscriptionOrderItem));

        } catch (\Exception $e) {
            info('Subscription Order Items update failed!', [$e]);

            return error('Subscription Order Items update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(SubscriptionOrderItem $subscription_order_item): JsonResponse
    // {
    //     try {

    //         if (! $subscription_order_item) {
    //             return response()->json(['message' => 'Subscription Order Items not found!'], 404);
    //         }

    //         $subscriptionOrderItem = $this->subscriptionOrderItemService->deleteSubscriptionOrderItem($subscription_order_item->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Subscription Order Items delete failed!', [$e]);
    //         return error('Subscription Order Items delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $subscriptionOrderItem = $this->subscriptionOrderItemService->getSubscriptionOrderItemById($id);

            if (! $subscriptionOrderItem) {
                return response()->json(['message' => 'Subscription Order Items not found!'], 404);
            }

            $this->subscriptionOrderItemService->deleteSubscriptionOrderItem($subscriptionOrderItem->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Subscription Order Items delete failed!', [$e]);

            return error('Subscription Order Items delete failed!.');
        }
    }
}
