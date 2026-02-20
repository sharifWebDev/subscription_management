<?php

namespace App\Services;

use App\DTOs\InvoiceDto;
use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepositoryInterface $invoiceRepository
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
            ->when(! empty($fromDate) && ! empty($toDate), function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [
                    "{$fromDate} 00:00:00",
                    "{$toDate} 23:59:59",
                ]);
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
}
