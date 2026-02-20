<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\SubscriptionEventDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionEventRequest;
use App\Http\Requests\UpdateSubscriptionEventRequest;
use App\Http\Resources\SubscriptionEventResource;
use App\Models\SubscriptionEvent;
use App\Services\SubscriptionEventService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionEventController extends Controller
{
    public function __construct(
        protected SubscriptionEventService $subscriptionEventService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $subscriptionEvents = $this->subscriptionEventService->getAllSubscriptionEvents($request);

            return success('Records retrieved successfully', SubscriptionEventResource::collection($subscriptionEvents));

        } catch (Exception $e) {

            info('Error retrieved SubscriptionEvent!', [$e]);

            return error('Subscription Events retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreSubscriptionEventRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $subscriptionEvent = $this->subscriptionEventService->storeSubscriptionEvent($dto);

    //         return success('Records saved successfully', new SubscriptionEventResource($subscriptionEvent));

    //     } catch (Exception $e) {

    //         info('Subscription Events data insert failed!', [$e]);
    //         return error('Subscription Events insert failed!.');
    //     }
    // }

    public function store(StoreSubscriptionEventRequest $request): JsonResponse
    {
        try {

            $subscriptionEvent = $this->subscriptionEventService->storeSubscriptionEvent($request->validated());

            return success('Records saved successfully', new SubscriptionEventResource($subscriptionEvent));

        } catch (Exception $e) {

            info('Subscription Events data insert failed!', [$e]);

            return error('Subscription Events insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(SubscriptionEvent $subscription_event) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new SubscriptionEventResource($subscription_event));

    //     } catch (\Exception $e) {
    //         info('Subscription Events data showing failed!', [$e]);
    //         return error('Subscription Events retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $subscription_event = $this->subscriptionEventService->getSubscriptionEventById($id);

            if (! $subscription_event) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new SubscriptionEventResource($subscription_event));

        } catch (\Exception $e) {
            info('Subscription Events data showing failed!', [$e]);

            return error('Subscription Events retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateSubscriptionEventRequest $request, SubscriptionEvent $subscription_event): JsonResponse
    //   {
    //       try {

    //         $subscriptionEvent = $this->subscriptionEventService->updateSubscriptionEvent($subscription_event->id, $request->validated());

    //         return success('Records updated successfully', new SubscriptionEventResource($subscriptionEvent));

    //       } catch (\Exception $e) {
    //           info('Subscription Events update failed!', [$e]);
    //           return error('Subscription Events update failed!.');
    //       }
    //   }

    public function update(UpdateSubscriptionEventRequest $request, int $id): JsonResponse
    {
        try {

            $subscriptionEvent = $this->subscriptionEventService->getSubscriptionEventById($id);

            // $dto = new SubscriptionEventDto($request->validated());
            // $this->subscriptionEventService->updateSubscriptionEvent($subscriptionEvent->id, $dto->toArray());

            $this->subscriptionEventService->updateSubscriptionEvent($subscriptionEvent->id, $request->validated());

            return success('Records updated successfully', new SubscriptionEventResource($subscriptionEvent));

        } catch (\Exception $e) {
            info('Subscription Events update failed!', [$e]);

            return error('Subscription Events update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(SubscriptionEvent $subscription_event): JsonResponse
    // {
    //     try {

    //         if (! $subscription_event) {
    //             return response()->json(['message' => 'Subscription Events not found!'], 404);
    //         }

    //         $subscriptionEvent = $this->subscriptionEventService->deleteSubscriptionEvent($subscription_event->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Subscription Events delete failed!', [$e]);
    //         return error('Subscription Events delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $subscriptionEvent = $this->subscriptionEventService->getSubscriptionEventById($id);

            if (! $subscriptionEvent) {
                return response()->json(['message' => 'Subscription Events not found!'], 404);
            }

            $this->subscriptionEventService->deleteSubscriptionEvent($subscriptionEvent->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Subscription Events delete failed!', [$e]);

            return error('Subscription Events delete failed!.');
        }
    }
}
