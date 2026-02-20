<?php

namespace App\Services;

use App\DTOs\PaymentTransactionDto;
use App\Models\PaymentTransaction;
use App\Repositories\Contracts\PaymentTransactionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentTransactionService
{
    public function __construct(
        protected PaymentTransactionRepositoryInterface $paymentTransactionRepository
    ) {}

    public function getAllPaymentTransactions(Request $request): LengthAwarePaginator
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
            3 => 'transaction_id',
            4 => 'reference_id',
            5 => 'type',
            6 => 'payment_method',
            7 => 'payment_gateway',
            8 => 'gateway_response',
            9 => 'payment_method_details',
            10 => 'amount',
            11 => 'fee',
            12 => 'tax',
            13 => 'net_amount',
            14 => 'currency',
            15 => 'exchange_rate',
            16 => 'status',
            17 => 'card_last4',
            18 => 'card_brand',
            19 => 'card_country',
            20 => 'card_exp_month',
            21 => 'card_exp_year',
            22 => 'bank_name',
            23 => 'bank_account_last4',
            24 => 'bank_routing_number',
            25 => 'wallet_type',
            26 => 'wallet_number',
            27 => 'wallet_transaction_id',
            28 => 'installment_number',
            29 => 'total_installments',
            30 => 'initiated_at',
            31 => 'authorized_at',
            32 => 'captured_at',
            33 => 'completed_at',
            34 => 'failed_at',
            35 => 'refunded_at',
            36 => 'fraud_indicators',
            37 => 'risk_score',
            38 => 'requires_review',
            39 => 'metadata',
            40 => 'custom_fields',
            41 => 'notes',
            42 => 'failure_reason',
            43 => 'ip_address',
            44 => 'user_agent',
            45 => 'location_data',
            46 => 'created_by',
            47 => 'updated_by',
            48 => 'created_at',
            49 => 'updated_at',
            50 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentTransactionRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentTransaction)->getFillable() as $column) {
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

    public function getPaymentTransactionById(int $id): ?PaymentTransaction
    {
        $paymentTransaction = $this->paymentTransactionRepository->find($id);
        if (! $paymentTransaction) {
            throw new ModelNotFoundException;
        }

        return $paymentTransaction;
    }

    // public function storePaymentTransaction(PaymentTransactionDto $dto, array $data): PaymentTransaction
    // {
    //  //handleFileUploa
    //  return $this->paymentTransactionRepository->create((array) $dto);
    //  }

    public function storePaymentTransaction(array $data): PaymentTransaction
    {

        return $this->paymentTransactionRepository->create($data);
    }

    public function updatePaymentTransaction(int $id, array $data): PaymentTransaction
    {

        return $this->paymentTransactionRepository->update($id, $data);
    }

    public function deletePaymentTransaction(int $id): bool
    {
        return $this->paymentTransactionRepository->delete($id);
    }
}
