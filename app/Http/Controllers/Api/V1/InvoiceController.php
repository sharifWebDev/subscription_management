<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\InvoiceDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function indexOld(Request $request): JsonResponse
    {
        try {

            $invoices = $this->invoiceService->getAllInvoices($request);

            return success('Records retrieved successfully', InvoiceResource::collection($invoices));

        } catch (Exception $e) {

            info('Error retrieved Invoice!', [$e]);

            return error('Invoices retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreInvoiceRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $invoice = $this->invoiceService->storeInvoice($dto);

    //         return success('Records saved successfully', new InvoiceResource($invoice));

    //     } catch (Exception $e) {

    //         info('Invoices data insert failed!', [$e]);
    //         return error('Invoices insert failed!.');
    //     }
    // }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->storeInvoice($request->validated());

            return success('Records saved successfully', new InvoiceResource($invoice));

        } catch (Exception $e) {

            info('Invoices data insert failed!', [$e]);

            return error('Invoices insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Invoice $invoice) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new InvoiceResource($invoice));

    //     } catch (\Exception $e) {
    //         info('Invoices data showing failed!', [$e]);
    //         return error('Invoices retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->getInvoiceById($id);

            if (! $invoice) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new InvoiceResource($invoice));

        } catch (\Exception $e) {
            info('Invoices data showing failed!', [$e]);

            return error('Invoices retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    //   {
    //       try {

    //         $invoice = $this->invoiceService->updateInvoice($invoice->id, $request->validated());

    //         return success('Records updated successfully', new InvoiceResource($invoice));

    //       } catch (\Exception $e) {
    //           info('Invoices update failed!', [$e]);
    //           return error('Invoices update failed!.');
    //       }
    //   }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->getInvoiceById($id);

            // $dto = new InvoiceDto($request->validated());
            // $this->invoiceService->updateInvoice($invoice->id, $dto->toArray());

            $this->invoiceService->updateInvoice($invoice->id, $request->validated());

            return success('Records updated successfully', new InvoiceResource($invoice));

        } catch (\Exception $e) {
            info('Invoices update failed!', [$e]);

            return error('Invoices update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Invoice $invoice): JsonResponse
    // {
    //     try {

    //         if (! $invoice) {
    //             return response()->json(['message' => 'Invoices not found!'], 404);
    //         }

    //         $invoice = $this->invoiceService->deleteInvoice($invoice->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Invoices delete failed!', [$e]);
    //         return error('Invoices delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->getInvoiceById($id);

            if (! $invoice) {
                return response()->json(['message' => 'Invoices not found!'], 404);
            }

            $this->invoiceService->deleteInvoice($invoice->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Invoices delete failed!', [$e]);

            return error('Invoices delete failed!.');
        }
    }
    // /

    /**
     * Get user invoices
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $query = Invoice::where('user_id', $user->id)
                ->with(['subscription.plan'])
                ->orderBy('issue_date', 'desc');

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('from_date')) {
                $query->where('issue_date', '>=', $request->from_date);
            }

            if ($request->has('to_date')) {
                $query->where('issue_date', '<=', $request->to_date);
            }

            if ($request->has('subscription_id')) {
                $query->where('subscription_id', $request->subscription_id);
            }

            $invoices = $query->paginate($request->get('per_page', 15));

            // Calculate totals
            $totals = [
                'total_paid' => Invoice::where('user_id', $user->id)
                    ->where('status', 'paid')
                    ->sum('total'),
                'total_due' => Invoice::where('user_id', $user->id)
                    ->where('status', 'open')
                    ->sum('total'),
                'total_count' => Invoice::where('user_id', $user->id)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => InvoiceResource::collection($invoices),
                'meta' => [
                    'current_page' => $invoices->currentPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                    'totals' => $totals,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
            ], 500);
        }
    }

    /**
     * Get single invoice
     */
    public function show(int $id): JsonResponse
    {
        try {
            $invoice = Invoice::with(['user', 'subscription.plan', 'paymentMaster'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            // Parse JSON fields
            $lineItems = json_decode($invoice->line_items, true) ?? [];
            $taxRates = $invoice->tax_rates ? json_decode($invoice->tax_rates, true) : [];
            $discounts = json_decode($invoice->discounts, true) ?? [];
            $history = json_decode($invoice->history, true) ?? [];
            $metadata = json_decode($invoice->metadata, true) ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'status' => $invoice->status,
                    'type' => $invoice->type,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'paid_at' => $invoice->paid_at,
                    'subtotal' => $invoice->subtotal,
                    'tax' => $invoice->tax,
                    'total' => $invoice->total,
                    'amount_due' => $invoice->amount_due,
                    'amount_paid' => $invoice->amount_paid,
                    'currency' => $invoice->currency,
                    'formatted_subtotal' => $this->formatMoney($invoice->subtotal, $invoice->currency),
                    'formatted_tax' => $this->formatMoney($invoice->tax, $invoice->currency),
                    'formatted_total' => $this->formatMoney($invoice->total, $invoice->currency),
                    'formatted_amount_due' => $this->formatMoney($invoice->amount_due, $invoice->currency),
                    'formatted_amount_paid' => $this->formatMoney($invoice->amount_paid, $invoice->currency),
                    'line_items' => $lineItems,
                    'tax_rates' => $taxRates,
                    'discounts' => $discounts,
                    'history' => $history,
                    'metadata' => $metadata,
                    'pdf_url' => $invoice->pdf_url,
                    'subscription' => $invoice->subscription ? [
                        'id' => $invoice->subscription->id,
                        'plan_name' => $invoice->subscription->plan->name,
                        'status' => $invoice->subscription->status,
                    ] : null,
                    'payment' => $invoice->paymentMaster ? [
                        'id' => $invoice->paymentMaster->id,
                        'payment_number' => $invoice->paymentMaster->payment_number,
                        'payment_method' => $invoice->paymentMaster->payment_method,
                        'paid_at' => $invoice->paymentMaster->paid_at,
                    ] : null,
                    'created_at' => $invoice->created_at,
                    'updated_at' => $invoice->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice'.' '.$e->getMessage(),
            ], 404);
        }
    }

    /**
     * Download invoice PDF
     */
    public function download(int $id)
    {
        try {
            $invoice = Invoice::where('user_id', Auth::id())->findOrFail($id);

            // Generate and return PDF download
            return $this->invoiceService->downloadPdf($invoice->id);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download invoice'.' '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stream invoice PDF in browser
     */
    public function view(int $id)
    {
        try {
            $invoice = Invoice::where('user_id', Auth::id())->findOrFail($id);

            // Generate PDF and stream to browser
            $pdf = $this->invoiceService->downloadPdf($invoice->id);

            return $pdf;

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to view invoice',
            ], 500);
        }
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(int $id): JsonResponse
    {
        try {
            $invoice = Invoice::where('user_id', Auth::id())->findOrFail($id);

            $sent = $this->invoiceService->sendInvoiceEmail($invoice->id);

            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice sent to your email',
                ]);
            } else {
                throw new Exception('Failed to send email');
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice',
            ], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $user = Auth::user();
            $now = now();

            $stats = [
                'current_month' => [
                    'total' => Invoice::where('user_id', $user->id)
                        ->whereYear('issue_date', $now->year)
                        ->whereMonth('issue_date', $now->month)
                        ->sum('total'),
                    'count' => Invoice::where('user_id', $user->id)
                        ->whereYear('issue_date', $now->year)
                        ->whereMonth('issue_date', $now->month)
                        ->count(),
                    'paid' => Invoice::where('user_id', $user->id)
                        ->where('status', 'paid')
                        ->whereYear('issue_date', $now->year)
                        ->whereMonth('issue_date', $now->month)
                        ->sum('total'),
                ],
                'previous_month' => [
                    'total' => Invoice::where('user_id', $user->id)
                        ->whereYear('issue_date', $now->subMonth()->year)
                        ->whereMonth('issue_date', $now->subMonth()->month)
                        ->sum('total'),
                    'count' => Invoice::where('user_id', $user->id)
                        ->whereYear('issue_date', $now->subMonth()->year)
                        ->whereMonth('issue_date', $now->subMonth()->month)
                        ->count(),
                ],
                'year_to_date' => [
                    'total' => Invoice::where('user_id', $user->id)
                        ->whereYear('issue_date', $now->year)
                        ->sum('total'),
                    'count' => Invoice::where('user_id', $user->id)
                        ->whereYear('issue_date', $now->year)
                        ->count(),
                ],
                'all_time' => [
                    'total' => Invoice::where('user_id', $user->id)->sum('total'),
                    'count' => Invoice::where('user_id', $user->id)->count(),
                ],
                'by_status' => [
                    'paid' => Invoice::where('user_id', $user->id)->where('status', 'paid')->count(),
                    'open' => Invoice::where('user_id', $user->id)->where('status', 'open')->count(),
                    'draft' => Invoice::where('user_id', $user->id)->where('status', 'draft')->count(),
                    'void' => Invoice::where('user_id', $user->id)->where('status', 'void')->count(),
                    'uncollectible' => Invoice::where('user_id', $user->id)->where('status', 'uncollectible')->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
            ], 500);
        }
    }

    /**
     * Get upcoming invoice (for active subscription)
     */
    public function upcoming(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get active subscription
            $activeSubscription = $user->subscriptions()
                ->with(['plan', 'price'])
                ->whereIn('status', ['active', 'trialing'])
                ->first();

            if (! $activeSubscription) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No active subscription',
                ]);
            }

            $nextBillingDate = $activeSubscription->current_period_ends_at;
            $amount = $activeSubscription->amount;

            // Calculate proration if any
            $prorationAmount = 0;
            $lineItems = [
                [
                    'description' => $activeSubscription->plan->name.' - '.$nextBillingDate->format('F Y'),
                    'amount' => $amount,
                    'quantity' => 1,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'subscription' => [
                        'id' => $activeSubscription->id,
                        'plan_name' => $activeSubscription->plan->name,
                        'amount' => $amount,
                        'formatted_amount' => $this->formatMoney($amount, $activeSubscription->currency),
                        'currency' => $activeSubscription->currency,
                        'next_billing_date' => $nextBillingDate->format('Y-m-d'),
                    ],
                    'amount' => $amount,
                    'formatted_amount' => $this->formatMoney($amount, $activeSubscription->currency),
                    'currency' => $activeSubscription->currency,
                    'next_billing_date' => $nextBillingDate->format('Y-m-d'),
                    'line_items' => $lineItems,
                    'proration_amount' => $prorationAmount,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming invoice',
            ], 500);
        }
    }

    /**
     * Format money with currency
     */
    protected function formatMoney($amount, $currency = 'USD'): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'BDT' => '৳',
            'INR' => '₹',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        return $symbol.' '.number_format($amount, 2);
    }
}
