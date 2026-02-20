<?php

namespace App\Services;

use App\DTOs\RefundDto;
use App\Models\Refund;
use App\Repositories\Contracts\RefundRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RefundService
{
    public function __construct(
        protected RefundRepositoryInterface $refundRepository
    ) {}

    public function getAllRefunds(Request $request): LengthAwarePaginator
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
            2 => 'payment_transaction_id',
            3 => 'user_id',
            4 => 'refund_number',
            5 => 'type',
            6 => 'status',
            7 => 'initiated_by',
            8 => 'amount',
            9 => 'fee',
            10 => 'net_amount',
            11 => 'currency',
            12 => 'exchange_rate',
            13 => 'reason',
            14 => 'reason_details',
            15 => 'customer_comments',
            16 => 'requested_at',
            17 => 'approved_at',
            18 => 'approved_by',
            19 => 'processed_at',
            20 => 'completed_at',
            21 => 'failed_at',
            22 => 'gateway_refund_id',
            23 => 'gateway_response',
            24 => 'metadata',
            25 => 'documents',
            26 => 'processed_by',
            27 => 'rejection_reason',
            28 => 'created_by',
            29 => 'updated_by',
            30 => 'created_at',
            31 => 'updated_at',
            32 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->refundRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Refund)->getFillable() as $column) {
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

    public function getRefundById(int $id): ?Refund
    {
        $refund = $this->refundRepository->find($id);
        if (! $refund) {
            throw new ModelNotFoundException;
        }

        return $refund;
    }

    // public function storeRefund(RefundDto $dto, array $data): Refund
    // {
    //  //handleFileUploa
    //  return $this->refundRepository->create((array) $dto);
    //  }

    public function storeRefund(array $data): Refund
    {

        return $this->refundRepository->create($data);
    }

    public function updateRefund(int $id, array $data): Refund
    {

        return $this->refundRepository->update($id, $data);
    }

    public function deleteRefund(int $id): bool
    {
        return $this->refundRepository->delete($id);
    }
}
