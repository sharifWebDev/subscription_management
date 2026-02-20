<?php

namespace App\Services;

use App\DTOs\PaymentMasterDto;
use App\Models\PaymentMaster;
use App\Repositories\Contracts\PaymentMasterRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentMasterService
{
    public function __construct(
        protected PaymentMasterRepositoryInterface $paymentMasterRepository
    ) {}

    public function getAllPaymentMasters(Request $request): LengthAwarePaginator
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
            2 => 'payment_number',
            3 => 'type',
            4 => 'status',
            5 => 'total_amount',
            6 => 'subtotal',
            7 => 'tax_amount',
            8 => 'discount_amount',
            9 => 'fee_amount',
            10 => 'net_amount',
            11 => 'paid_amount',
            12 => 'due_amount',
            13 => 'currency',
            14 => 'exchange_rate',
            15 => 'base_currency',
            16 => 'base_amount',
            17 => 'payment_method',
            18 => 'payment_method_details',
            19 => 'payment_gateway',
            20 => 'is_installment',
            21 => 'installment_count',
            22 => 'installment_frequency',
            23 => 'payment_date',
            24 => 'due_date',
            25 => 'paid_at',
            26 => 'cancelled_at',
            27 => 'expires_at',
            28 => 'customer_reference',
            29 => 'bank_reference',
            30 => 'gateway_reference',
            31 => 'metadata',
            32 => 'custom_fields',
            33 => 'notes',
            34 => 'failure_reason',
            35 => 'created_by',
            36 => 'updated_by',
            37 => 'created_at',
            38 => 'updated_at',
            39 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentMasterRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentMaster)->getFillable() as $column) {
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

    public function getPaymentMasterById(int $id): ?PaymentMaster
    {
        $paymentMaster = $this->paymentMasterRepository->find($id);
        if (! $paymentMaster) {
            throw new ModelNotFoundException;
        }

        return $paymentMaster;
    }

    // public function storePaymentMaster(PaymentMasterDto $dto, array $data): PaymentMaster
    // {
    //  //handleFileUploa
    //  return $this->paymentMasterRepository->create((array) $dto);
    //  }

    public function storePaymentMaster(array $data): PaymentMaster
    {

        return $this->paymentMasterRepository->create($data);
    }

    public function updatePaymentMaster(int $id, array $data): PaymentMaster
    {

        return $this->paymentMasterRepository->update($id, $data);
    }

    public function deletePaymentMaster(int $id): bool
    {
        return $this->paymentMasterRepository->delete($id);
    }
}
