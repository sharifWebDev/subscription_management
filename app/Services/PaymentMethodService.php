<?php

namespace App\Services;

use App\DTOs\PaymentMethodDto;
use App\Models\PaymentMethod;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentMethodService
{
    public function __construct(
        protected PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {}

    public function getAllPaymentMethods(Request $request): LengthAwarePaginator
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
            2 => 'type',
            3 => 'gateway',
            4 => 'gateway_customer_id',
            5 => 'gateway_payment_method_id',
            6 => 'nickname',
            7 => 'is_default',
            8 => 'is_verified',
            9 => 'card_last4',
            10 => 'card_brand',
            11 => 'card_exp_month',
            12 => 'card_exp_year',
            13 => 'card_country',
            14 => 'bank_name',
            15 => 'bank_account_last4',
            16 => 'bank_account_type',
            17 => 'bank_routing_number',
            18 => 'wallet_type',
            19 => 'wallet_number',
            20 => 'crypto_currency',
            21 => 'crypto_address',
            22 => 'encrypted_data',
            23 => 'fingerprint',
            24 => 'is_compromised',
            25 => 'metadata',
            26 => 'gateway_metadata',
            27 => 'verified_at',
            28 => 'verified_by',
            29 => 'last_used_at',
            30 => 'usage_count',
            31 => 'created_by',
            32 => 'updated_by',
            33 => 'created_at',
            34 => 'updated_at',
            35 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentMethodRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentMethod)->getFillable() as $column) {
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

    public function getPaymentMethodById(int $id): ?PaymentMethod
    {
        $paymentMethod = $this->paymentMethodRepository->find($id);
        if (! $paymentMethod) {
            throw new ModelNotFoundException;
        }

        return $paymentMethod;
    }

    // public function storePaymentMethod(PaymentMethodDto $dto, array $data): PaymentMethod
    // {
    //  //handleFileUploa
    //  return $this->paymentMethodRepository->create((array) $dto);
    //  }

    public function storePaymentMethod(array $data): PaymentMethod
    {

        return $this->paymentMethodRepository->create($data);
    }

    public function updatePaymentMethod(int $id, array $data): PaymentMethod
    {

        return $this->paymentMethodRepository->update($id, $data);
    }

    public function deletePaymentMethod(int $id): bool
    {
        return $this->paymentMethodRepository->delete($id);
    }
}
