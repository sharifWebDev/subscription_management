<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\SubscriptionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $subscriptions = $this->subscriptionService->getAllSubscriptions($request);

            return success('Records retrieved successfully', SubscriptionResource::collection($subscriptions));

        } catch (Exception $e) {

            info('Error retrieved Subscription!', [$e]);

            return error('Subscriptions retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreSubscriptionRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $subscription = $this->subscriptionService->storeSubscription($dto);

    //         return success('Records saved successfully', new SubscriptionResource($subscription));

    //     } catch (Exception $e) {

    //         info('Subscriptions data insert failed!', [$e]);
    //         return error('Subscriptions insert failed!.');
    //     }
    // }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        try {

            $subscription = $this->subscriptionService->storeSubscription($request->validated());

            return success('Records saved successfully', new SubscriptionResource($subscription));

        } catch (Exception $e) {

            info('Subscriptions data insert failed!', [$e]);

            return error('Subscriptions insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Subscription $subscription) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new SubscriptionResource($subscription));

    //     } catch (\Exception $e) {
    //         info('Subscriptions data showing failed!', [$e]);
    //         return error('Subscriptions retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $subscription = $this->subscriptionService->getSubscriptionById($id);

            if (! $subscription) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new SubscriptionResource($subscription));

        } catch (\Exception $e) {
            info('Subscriptions data showing failed!', [$e]);

            return error('Subscriptions retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    //   {
    //       try {

    //         $subscription = $this->subscriptionService->updateSubscription($subscription->id, $request->validated());

    //         return success('Records updated successfully', new SubscriptionResource($subscription));

    //       } catch (\Exception $e) {
    //           info('Subscriptions update failed!', [$e]);
    //           return error('Subscriptions update failed!.');
    //       }
    //   }

    public function update(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        try {

            $subscription = $this->subscriptionService->getSubscriptionById($id);

            // $dto = new SubscriptionDto($request->validated());
            // $this->subscriptionService->updateSubscription($subscription->id, $dto->toArray());

            $this->subscriptionService->updateSubscription($subscription->id, $request->validated());

            return success('Records updated successfully', new SubscriptionResource($subscription));

        } catch (\Exception $e) {
            info('Subscriptions update failed!', [$e]);

            return error('Subscriptions update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Subscription $subscription): JsonResponse
    // {
    //     try {

    //         if (! $subscription) {
    //             return response()->json(['message' => 'Subscriptions not found!'], 404);
    //         }

    //         $subscription = $this->subscriptionService->deleteSubscription($subscription->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Subscriptions delete failed!', [$e]);
    //         return error('Subscriptions delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $subscription = $this->subscriptionService->getSubscriptionById($id);

            if (! $subscription) {
                return response()->json(['message' => 'Subscriptions not found!'], 404);
            }

            $this->subscriptionService->deleteSubscription($subscription->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Subscriptions delete failed!', [$e]);

            return error('Subscriptions delete failed!.');
        }
    }
}
