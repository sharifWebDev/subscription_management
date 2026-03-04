<?php

namespace App\Services;

use App\DTOs\InvoiceDto;
use App\Models\Invoice;
use App\Models\PaymentMaster;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepositoryInterface $invoiceRepository,
        protected PaymentService $paymentService
    ) {}

    public function getAllInvoices(Request $request): LengthAwarePaginator
    {
        $length = $request->input('length', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sortColumnIndex = $request->input('order.0.column');
        $sortDirection = $request->input('order.0.dir', 'desc');

        $columns = [
            0 => 'id',
            1 => 'user_id',
            2 => 'subscription_id',
            3 => 'number',
            4 => 'external_id',
            5 => 'type',
            6 => 'status',
            7 => 'subtotal',
            8 => 'tax',
            9 => 'total',
            10 => 'amount_due',
            11 => 'amount_paid',
            12 => 'amount_remaining',
            13 => 'currency',
            14 => 'issue_date',
            15 => 'due_date',
            16 => 'paid_at',
            17 => 'finalized_at',
            18 => 'line_items',
            19 => 'tax_rates',
            20 => 'discounts',
            21 => 'metadata',
            22 => 'history',
            23 => 'pdf_url',
            24 => 'created_by',
            25 => 'updated_by',
            26 => 'created_at',
            27 => 'updated_at',
            28 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->invoiceRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Invoice)->getFillable() as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            });

        $query->orderBy($sortColumn, $sortDirection);

        return $length === -1
            ? $query->paginate($query->get()->count())
            : $query->paginate($length);
    }

    public function getInvoiceById(int $id): ?Invoice
    {
        $invoice = $this->invoiceRepository->find($id);
        if (! $invoice) {
            throw new ModelNotFoundException;
        }

        return $invoice;
    }

    // public function storeInvoice(InvoiceDto $dto, array $data): Invoice
    // {
    //  //handleFileUploa
    //  return $this->invoiceRepository->create((array) $dto);
    //  }

    public function storeInvoice(array $data): Invoice
    {

        return $this->invoiceRepository->create($data);
    }

    public function updateInvoice(int $id, array $data): Invoice
    {

        return $this->invoiceRepository->update($id, $data);
    }

    public function deleteInvoice(int $id): bool
    {
        return $this->invoiceRepository->delete($id);
    }

    /**
     * Create a new invoice
     */
    public function createInvoice(array $data): Invoice
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($data['user_id']);
            $subscription = isset($data['subscription_id']) ? Subscription::find($data['subscription_id']) : null;

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Calculate amounts
            $subtotal = $data['amount'] ?? 0;
            $tax = $subtotal * ($data['tax_rate'] ?? 0.1);
            $total = $subtotal + $tax;

            // Create invoice
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription?->id,
                'number' => $invoiceNumber,
                'external_id' => $data['external_id'] ?? null,
                'type' => $data['type'] ?? 'subscription',
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'amount_due' => $total,
                'currency' => $data['currency'] ?? 'USD',
                'issue_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays($data['due_days'] ?? 7),
                'line_items' => json_encode($data['items'] ?? []),
                'tax_rates' => json_encode($data['tax_rates'] ?? []),
                'discounts' => json_encode($data['discounts'] ?? []),
                'metadata' => json_encode($data['metadata'] ?? []),
                'history' => json_encode([
                    [
                        'date' => Carbon::now()->toDateTimeString(),
                        'status' => 'draft',
                    ],
                ]),
                'created_by' => $user->id,
            ]);

            DB::commit();

            return $invoice;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create invoice: '.$e->getMessage());
            throw new Exception('Failed to create invoice: '.$e->getMessage());
        }
    }

    /**
     * Finalize invoice
     */
    public function finalizeInvoice(int $invoiceId): Invoice
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($invoiceId);

            if ($invoice->status !== 'draft') {
                throw new Exception('Only draft invoices can be finalized');
            }

            $history = json_decode($invoice->history, true) ?? [];
            $history[] = [
                'date' => Carbon::now()->toDateTimeString(),
                'status' => 'open',
            ];

            $invoice->update([
                'status' => 'open',
                'finalized_at' => Carbon::now(),
                'history' => json_encode($history),
            ]);

            DB::commit();

            return $invoice;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to finalize invoice: '.$e->getMessage());
            throw new Exception('Failed to finalize invoice: '.$e->getMessage());
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(int $invoiceId, int $paymentMasterId): Invoice
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($invoiceId);
            $paymentMaster = PaymentMaster::findOrFail($paymentMasterId);

            $history = json_decode($invoice->history, true) ?? [];
            $history[] = [
                'date' => Carbon::now()->toDateTimeString(),
                'status' => 'paid',
            ];

            $invoice->update([
                'status' => 'paid',
                'amount_paid' => $invoice->total,
                'paid_at' => Carbon::now(),
                'history' => json_encode($history),
            ]);

            DB::commit();

            return $invoice;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark invoice as paid: '.$e->getMessage());
            throw new Exception('Failed to mark invoice as paid: '.$e->getMessage());
        }
    }

    /**
     * Render invoice PDF
     */
    protected function renderInvoicePdf(Invoice $invoice): string
    {
        $pdf = PDF::loadView('pdf.invoice', compact('invoice'));

        return $pdf->output();
    }

    /**
     * Generate invoice PDF
     */
    public function generatePdf(int $invoiceId): string
    {
        try {
            $invoice = Invoice::with(['user', 'subscription.plan', 'subscription.price'])->findOrFail($invoiceId);

            // Parse JSON data
            $invoice->line_items = json_decode($invoice->line_items, true) ?? [];
            $invoice->tax_rates = json_decode($invoice->tax_rates, true) ?? [];
            $invoice->discounts = json_decode($invoice->discounts, true) ?? [];
            $invoice->metadata = json_decode($invoice->metadata, true) ?? [];

            // Get company settings from config or database
            $company = [
                'name' => config('app.name'),
                'email' => config('app.email', 'billing@example.com'),
                'phone' => config('app.phone', '+1 (555) 123-4567'),
                'address' => config('app.address', '123 Business St, Suite 100'),
                'city' => config('app.city', 'New York'),
                'state' => config('app.state', 'NY'),
                'zip' => config('app.zip', '10001'),
                'country' => config('app.country', 'USA'),
                'logo' => public_path('images/logo.png'),
                'tax_id' => config('app.tax_id', 'TAX-123456'),
            ];

            // Generate PDF using mPDF
            $pdf = PDF::loadView('pdf.invoice', [
                'invoice' => $invoice,
                'company' => $company,
            ]);

            // Set PDF options
            $pdf->setOptions([
                'format' => 'A4',
                'default_font_size' => 12,
                'default_font' => 'dejavusans',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'margin_header' => 9,
                'margin_footer' => 9,
            ]);

            $pdfContent = $pdf->output();

            // Save PDF to storage
            $pdfUrl = $this->savePdfToStorage($invoice, $pdfContent);

            // Update invoice with PDF URL
            $invoice->update(['pdf_url' => $pdfUrl]);

            return $pdfUrl;

        } catch (Exception $e) {
            Log::error('Failed to generate invoice PDF: '.$e->getMessage());
            throw new Exception('Failed to generate invoice PDF: '.$e->getMessage());
        }
    }

    /**
     * Generate invoice PDF and return as download response
     */
  public function downloadPdf(int $invoiceId)
{
    try {
        $invoice = Invoice::with(['user', 'subscription.plan', 'subscription.price'])->findOrFail($invoiceId);

        if (! $invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        // Parse JSON data
        $invoice->line_items = json_decode($invoice->line_items, true) ?? [];
        $invoice->tax_rates = json_decode($invoice->tax_rates, true) ?? [];
        $invoice->discounts = json_decode($invoice->discounts, true) ?? [];

        // Calculate amounts if not present
        if (!isset($invoice->subtotal)) {
            $invoice->subtotal = $invoice->total - ($invoice->tax ?? 0);
        }

        // Get company settings
        $company = [
            'name' => config('app.name', 'Your Company'),
            'email' => config('mail.from.address', 'billing@example.com'),
            'phone' => config('app.phone', '+1 (555) 123-4567'),
            'address' => config('app.address', '123 Business St, Suite 100'),
            'city' => config('app.city', 'New York'),
            'state' => config('app.state', 'NY'),
            'zip' => config('app.zip', '10001'),
            'country' => config('app.country', 'USA'),
            'logo' => file_exists(public_path('images/logo.png')) ? public_path('images/logo.png') : null,
            'tax_id' => config('app.tax_id', 'TAX-123456'),
        ];

        // Get user billing address
        $user = $invoice->user;
        $billingAddress = $user->billing_address ?? [];

        // Prepare data for view
        $data = [
            'invoice' => $invoice,
            'company' => $company,
            'user' => $user,
            'billingAddress' => $billingAddress,
            'lineItems' => $invoice->line_items,
            'taxRates' => $invoice->tax_rates,
            'discounts' => $invoice->discounts,
            'subscription' => $invoice->subscription,
        ];

        // Generate PDF
        $pdf = PDF::loadView('pdf.invoice', $data, [
            'format' => 'A4',
            'default_font_size' => 12,
            'default_font' => 'dejavusans',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
        ]);

        $filename = 'invoice-' . ($invoice->number ?? $invoice->id) . '.pdf';

        $pdfContent = $pdf->output();

            // Save PDF to storage
            $pdfUrl = $this->savePdfToStorage($invoice, $pdfContent);

            $invoice->update(['pdf_url' => $pdfUrl]);

            return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');


    } catch (\Exception $e) {
        \Log::error('Failed to download invoice PDF: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'invoice_id' => $invoiceId
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to download invoice PDF: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Get user invoices
     */
    public function getUserInvoices(int $userId, array $filters = []): array
    {
        $query = Invoice::where('user_id', $userId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->where('issue_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('issue_date', '<=', $filters['to_date']);
        }

        $invoices = $query->orderBy('issue_date', 'desc')->get();

        // Calculate this month's total
        $thisMonth = Invoice::where('user_id', $userId)
            ->whereYear('issue_date', Carbon::now()->year)
            ->whereMonth('issue_date', Carbon::now()->month)
            ->where('status', 'paid')
            ->sum('total');

        $totals = [
            'total_paid' => $invoices->where('status', 'paid')->sum('total'),
            'total_due' => $invoices->where('status', 'open')->sum('total'),
            'this_month' => $thisMonth,
            'count' => $invoices->count(),
        ];

        return [
            'invoices' => $invoices,
            'totals' => $totals,
        ];
    }

    /**
     * Generate invoice number
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $number = $prefix.'-'.$date.'-'.$random;

        // Ensure uniqueness
        while (Invoice::where('number', $number)->exists()) {
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $number = $prefix.'-'.$date.'-'.$random;
        }

        return $number;
    }

    /**
     * Save PDF to storage
     */
    protected function savePdfToStorage(Invoice $invoice, string $pdfContent): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $filename = "invoices/{$year}/{$month}/{$invoice->number}.pdf";

        // Ensure directory exists
        $directory = dirname($filename);
        if (! Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        Storage::put($filename, $pdfContent);

        return Storage::url($filename);
    }

    /**
     * Send invoice email with PDF attachment
     */
    public function sendInvoiceEmail(int $invoiceId): bool
    {
        try {
            $invoice = Invoice::with('user')->findOrFail($invoiceId);

            // Generate PDF if not exists
            if (! $invoice->pdf_url) {
                $this->generatePdf($invoiceId);
                $invoice->refresh();
            }

            // Get full PDF path
            $pdfPath = str_replace('/storage', 'public', $invoice->pdf_url);

            // Send email with attachment
            // You can implement your email logic here
            // Mail::to($invoice->user->email)->send(new InvoiceMail($invoice, $pdfPath));

            return true;

        } catch (Exception $e) {
            Log::error('Failed to send invoice email: '.$e->getMessage());

            return false;
        }
    }
}
