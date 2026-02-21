<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\SubscriptionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\UsageRecordResource;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Services\InvoiceService;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected InvoiceService $invoiceService
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

    // /

    /**
     * Get user subscriptions
     */
    public function getUserSubscriptions(): JsonResponse
    {
        try {
            $user = Auth::user();
            $subscriptions = $this->subscriptionService->getUserSubscriptions($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'active' => $subscriptions['active'] ? new SubscriptionResource($subscriptions['active']) : null,
                    'all' => SubscriptionResource::collection($subscriptions['all']),
                    'past' => SubscriptionResource::collection($subscriptions['past']),
                    'has_active' => $subscriptions['has_active'],
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscriptions'.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subscription details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $subscription = Subscription::with(['plan', 'price', 'items.feature', 'invoices'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new SubscriptionResource($subscription),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription'.$e->getMessage(),
            ], 404);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'required|string|in:customer,payment_failed,other',
                'reason_details' => 'nullable|string|max:500',
            ]);

            $subscription = Subscription::where('user_id', Auth::id())
                ->whereIn('status', ['active', 'trialing'])
                ->findOrFail($id);

            $result = $this->subscriptionService->cancelSubscription(
                $subscription->id,
                $request->reason,
                $request->reason_details
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Request refund
     */
    public function refund(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'required|string|in:duplicate,fraudulent,requested_by_customer,service_issue,other',
                'reason_details' => 'nullable|string|max:500',
                'amount' => 'nullable|numeric|min:0.01',
            ]);

            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);

            // Get last payment
            $payment = $subscription->payments()->latest()->first();

            if (! $payment) {
                throw new Exception('No payment found for refund');
            }

            $refundAmount = $request->amount ?? $payment->amount;

            $result = $this->subscriptionService->processRefund([
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'amount' => $refundAmount,
                'reason' => $request->reason,
                'reason_details' => $request->reason_details,
                'initiated_by' => 'customer',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => $result,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Renew subscription
     */
    public function renew(int $id): JsonResponse
    {
        try {
            $subscription = Subscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->findOrFail($id);

            $result = $this->subscriptionService->renewSubscription($subscription->id);

            return response()->json([
                'success' => true,
                'message' => 'Subscription renewed successfully',
                'data' => new SubscriptionResource($result),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get subscription invoices
     */
    public function invoices(int $id): JsonResponse
    {
        try {
            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);

            $invoices = $subscription->invoices()
                ->orderBy('issue_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => InvoiceResource::collection($invoices),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
            ], 500);
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(int $id, int $invoiceId): JsonResponse
    {
        try {
            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);
            $invoice = $subscription->invoices()->findOrFail($invoiceId);

            if (! $invoice->pdf_url) {
                $invoice->pdf_url = $this->invoiceService->generatePdf($invoice->id);
                $invoice->save();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'pdf_url' => $invoice->pdf_url,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download invoice',
            ], 500);
        }
    }

    /**
     * Get usage records
     */
    public function usage(int $id, Request $request): JsonResponse
    {
        try {
            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);

            $query = $subscription->usageRecords()
                ->with('feature')
                ->orderBy('recorded_at', 'desc');

            if ($request->has('from_date')) {
                $query->where('billing_date', '>=', $request->from_date);
            }

            if ($request->has('to_date')) {
                $query->where('billing_date', '<=', $request->to_date);
            }

            $usage = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => UsageRecordResource::collection($usage),
                'meta' => [
                    'total' => $usage->total(),
                    'per_page' => $usage->perPage(),
                    'current_page' => $usage->currentPage(),
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch usage records',
            ], 500);
        }
    }

    /**
     * Get subscription events
     */
    public function events(int $id): JsonResponse
    {
        try {
            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);

            $events = $subscription->events()
                ->orderBy('occurred_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events',
            ], 500);
        }
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'payment_method_id' => 'required|exists:payment_methods,id',
            ]);

            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);

            $paymentMethod = PaymentMethod::where('user_id', Auth::id())
                ->where('id', $request->payment_method_id)
                ->firstOrFail();

            $subscription->update([
                'gateway_payment_method_id' => $paymentMethod->gateway_payment_method_id,
                'gateway_customer_id' => $paymentMethod->gateway_customer_id,
            ]);

            // Set as default if needed
            if ($request->boolean('make_default')) {
                PaymentMethod::where('user_id', Auth::id())
                    ->update(['is_default' => false]);

                $paymentMethod->update(['is_default' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // settings
    public function settings(): JsonResponse
    {
        $data = [
            'user' => Auth::user(),
            'payment_methods' => PaymentMethod::where('user_id', Auth::id())->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
