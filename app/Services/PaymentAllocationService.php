<?php

namespace App\Services;

use App\DTOs\PaymentAllocationDto;
use App\Models\PaymentAllocation;
use App\Repositories\Contracts\PaymentAllocationRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentAllocationService
{
    public function __construct(
        protected PaymentAllocationRepositoryInterface $paymentAllocationRepository
    ) {}

    public function getAllPaymentAllocations(Request $request): LengthAwarePaginator
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
            1 => 'payment_master_id',
            2 => 'payment_child_id',
            3 => 'payment_transaction_id',
            4 => 'allocatable_type',
            5 => 'allocatable_id',
            6 => 'amount',
            7 => 'base_amount',
            8 => 'exchange_rate',
            9 => 'currency',
            10 => 'allocation_reference',
            11 => 'allocation_type',
            12 => 'is_reversed',
            13 => 'reversed_at',
            14 => 'reversal_id',
            15 => 'metadata',
            16 => 'notes',
            17 => 'created_by',
            18 => 'updated_by',
            19 => 'created_at',
            20 => 'updated_at',
            21 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentAllocationRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentAllocation)->getFillable() as $column) {
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

    public function getPaymentAllocationById(int $id): ?PaymentAllocation
    {
        $paymentAllocation = $this->paymentAllocationRepository->find($id);
        if (! $paymentAllocation) {
            throw new ModelNotFoundException;
        }

        return $paymentAllocation;
    }

    // public function storePaymentAllocation(PaymentAllocationDto $dto, array $data): PaymentAllocation
    // {
    //  //handleFileUploa
    //  return $this->paymentAllocationRepository->create((array) $dto);
    //  }

    public function storePaymentAllocation(array $data): PaymentAllocation
    {

        return $this->paymentAllocationRepository->create($data);
    }

    public function updatePaymentAllocation(int $id, array $data): PaymentAllocation
    {

        return $this->paymentAllocationRepository->update($id, $data);
    }

    public function deletePaymentAllocation(int $id): bool
    {
        return $this->paymentAllocationRepository->delete($id);
    }
}
