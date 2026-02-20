<?php

namespace App\Services;

use App\DTOs\PaymentChildDto;
use App\Models\PaymentChild;
use App\Repositories\Contracts\PaymentChildRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentChildService
{
    public function __construct(
        protected PaymentChildRepositoryInterface $paymentChildRepository
    ) {}

    public function getAllPaymentChilds(Request $request): LengthAwarePaginator
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
            2 => 'subscription_id',
            3 => 'plan_id',
            4 => 'invoice_id',
            5 => 'item_type',
            6 => 'item_id',
            7 => 'description',
            8 => 'item_code',
            9 => 'unit_price',
            10 => 'quantity',
            11 => 'amount',
            12 => 'tax_amount',
            13 => 'discount_amount',
            14 => 'total_amount',
            15 => 'period_start',
            16 => 'period_end',
            17 => 'billing_cycle',
            18 => 'status',
            19 => 'paid_at',
            20 => 'allocated_amount',
            21 => 'is_fully_allocated',
            22 => 'metadata',
            23 => 'tax_breakdown',
            24 => 'discount_breakdown',
            25 => 'created_by',
            26 => 'updated_by',
            27 => 'created_at',
            28 => 'updated_at',
            29 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentChildRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentChild)->getFillable() as $column) {
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

    public function getPaymentChildById(int $id): ?PaymentChild
    {
        $paymentChild = $this->paymentChildRepository->find($id);
        if (! $paymentChild) {
            throw new ModelNotFoundException;
        }

        return $paymentChild;
    }

    // public function storePaymentChild(PaymentChildDto $dto, array $data): PaymentChild
    // {
    //  //handleFileUploa
    //  return $this->paymentChildRepository->create((array) $dto);
    //  }

    public function storePaymentChild(array $data): PaymentChild
    {

        return $this->paymentChildRepository->create($data);
    }

    public function updatePaymentChild(int $id, array $data): PaymentChild
    {

        return $this->paymentChildRepository->update($id, $data);
    }

    public function deletePaymentChild(int $id): bool
    {
        return $this->paymentChildRepository->delete($id);
    }
}
