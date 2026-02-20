<?php

namespace App\Services;

use App\DTOs\PaymentGatewayDto;
use App\Models\PaymentGateway;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentGatewayService
{
    public function __construct(
        protected PaymentGatewayRepositoryInterface $paymentGatewayRepository
    ) {}

    public function getAllPaymentGateways(Request $request): LengthAwarePaginator
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
            1 => 'name',
            2 => 'code',
            3 => 'type',
            4 => 'is_active',
            5 => 'is_test_mode',
            6 => 'supports_recurring',
            7 => 'supports_refunds',
            8 => 'supports_installments',
            9 => 'api_key',
            10 => 'api_secret',
            11 => 'webhook_secret',
            12 => 'merchant_id',
            13 => 'store_id',
            14 => 'store_password',
            15 => 'base_url',
            16 => 'callback_url',
            17 => 'webhook_url',
            18 => 'supported_currencies',
            19 => 'supported_countries',
            20 => 'excluded_countries',
            21 => 'percentage_fee',
            22 => 'fixed_fee',
            23 => 'fee_currency',
            24 => 'fee_structure',
            25 => 'config',
            26 => 'metadata',
            27 => 'settlement_days',
            28 => 'refund_days',
            29 => 'min_amount',
            30 => 'max_amount',
            31 => 'created_by',
            32 => 'updated_by',
            33 => 'created_at',
            34 => 'updated_at',
            35 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentGatewayRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentGateway)->getFillable() as $column) {
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

    public function getPaymentGatewayById(int $id): ?PaymentGateway
    {
        $paymentGateway = $this->paymentGatewayRepository->find($id);
        if (! $paymentGateway) {
            throw new ModelNotFoundException;
        }

        return $paymentGateway;
    }

    // public function storePaymentGateway(PaymentGatewayDto $dto, array $data): PaymentGateway
    // {
    //  //handleFileUploa
    //  return $this->paymentGatewayRepository->create((array) $dto);
    //  }

    public function storePaymentGateway(array $data): PaymentGateway
    {

        return $this->paymentGatewayRepository->create($data);
    }

    public function updatePaymentGateway(int $id, array $data): PaymentGateway
    {

        return $this->paymentGatewayRepository->update($id, $data);
    }

    public function deletePaymentGateway(int $id): bool
    {
        return $this->paymentGatewayRepository->delete($id);
    }
}
