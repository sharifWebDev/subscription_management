<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\SubscriptionItemDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionItemRequest;
use App\Http\Requests\UpdateSubscriptionItemRequest;
use App\Http\Resources\SubscriptionItemResource;
use App\Models\SubscriptionItem;
use App\Services\SubscriptionItemService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionItemController extends Controller
{
    public function __construct(
        protected SubscriptionItemService $subscriptionItemService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $subscriptionItems = $this->subscriptionItemService->getAllSubscriptionItems($request);

            return success('Records retrieved successfully', SubscriptionItemResource::collection($subscriptionItems));

        } catch (Exception $e) {

            info('Error retrieved SubscriptionItem!', [$e]);

            return error('Subscription Items retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreSubscriptionItemRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $subscriptionItem = $this->subscriptionItemService->storeSubscriptionItem($dto);

    //         return success('Records saved successfully', new SubscriptionItemResource($subscriptionItem));

    //     } catch (Exception $e) {

    //         info('Subscription Items data insert failed!', [$e]);
    //         return error('Subscription Items insert failed!.');
    //     }
    // }

    public function store(StoreSubscriptionItemRequest $request): JsonResponse
    {
        try {

            $subscriptionItem = $this->subscriptionItemService->storeSubscriptionItem($request->validated());

            return success('Records saved successfully', new SubscriptionItemResource($subscriptionItem));

        } catch (Exception $e) {

            info('Subscription Items data insert failed!', [$e]);

            return error('Subscription Items insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(SubscriptionItem $subscription_item) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new SubscriptionItemResource($subscription_item));

    //     } catch (\Exception $e) {
    //         info('Subscription Items data showing failed!', [$e]);
    //         return error('Subscription Items retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $subscription_item = $this->subscriptionItemService->getSubscriptionItemById($id);

            if (! $subscription_item) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new SubscriptionItemResource($subscription_item));

        } catch (\Exception $e) {
            info('Subscription Items data showing failed!', [$e]);

            return error('Subscription Items retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateSubscriptionItemRequest $request, SubscriptionItem $subscription_item): JsonResponse
    //   {
    //       try {

    //         $subscriptionItem = $this->subscriptionItemService->updateSubscriptionItem($subscription_item->id, $request->validated());

    //         return success('Records updated successfully', new SubscriptionItemResource($subscriptionItem));

    //       } catch (\Exception $e) {
    //           info('Subscription Items update failed!', [$e]);
    //           return error('Subscription Items update failed!.');
    //       }
    //   }

    public function update(UpdateSubscriptionItemRequest $request, int $id): JsonResponse
    {
        try {

            $subscriptionItem = $this->subscriptionItemService->getSubscriptionItemById($id);

            // $dto = new SubscriptionItemDto($request->validated());
            // $this->subscriptionItemService->updateSubscriptionItem($subscriptionItem->id, $dto->toArray());

            $this->subscriptionItemService->updateSubscriptionItem($subscriptionItem->id, $request->validated());

            return success('Records updated successfully', new SubscriptionItemResource($subscriptionItem));

        } catch (\Exception $e) {
            info('Subscription Items update failed!', [$e]);

            return error('Subscription Items update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(SubscriptionItem $subscription_item): JsonResponse
    // {
    //     try {

    //         if (! $subscription_item) {
    //             return response()->json(['message' => 'Subscription Items not found!'], 404);
    //         }

    //         $subscriptionItem = $this->subscriptionItemService->deleteSubscriptionItem($subscription_item->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Subscription Items delete failed!', [$e]);
    //         return error('Subscription Items delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $subscriptionItem = $this->subscriptionItemService->getSubscriptionItemById($id);

            if (! $subscriptionItem) {
                return response()->json(['message' => 'Subscription Items not found!'], 404);
            }

            $this->subscriptionItemService->deleteSubscriptionItem($subscriptionItem->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Subscription Items delete failed!', [$e]);

            return error('Subscription Items delete failed!.');
        }
    }
}
