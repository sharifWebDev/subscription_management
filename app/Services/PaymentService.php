<?php

namespace App\Services;

use App\DTOs\PaymentDto;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository
    ) {}

    public function getAllPayments(Request $request): LengthAwarePaginator
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
            1 => 'invoice_id',
            2 => 'user_id',
            3 => 'external_id',
            4 => 'type',
            5 => 'status',
            6 => 'amount',
            7 => 'fee',
            8 => 'net',
            9 => 'currency',
            10 => 'gateway',
            11 => 'gateway_response',
            12 => 'payment_method',
            13 => 'processed_at',
            14 => 'refunded_at',
            15 => 'metadata',
            16 => 'fraud_indicators',
            17 => 'created_by',
            18 => 'updated_by',
            19 => 'created_at',
            20 => 'updated_at',
            21 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Payment)->getFillable() as $column) {
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

    public function getPaymentById(int $id): ?Payment
    {
        $payment = $this->paymentRepository->find($id);
        if (! $payment) {
            throw new ModelNotFoundException;
        }

        return $payment;
    }

    // public function storePayment(PaymentDto $dto, array $data): Payment
    // {
    //  //handleFileUploa
    //  return $this->paymentRepository->create((array) $dto);
    //  }

    public function storePayment(array $data): Payment
    {

        return $this->paymentRepository->create($data);
    }

    public function updatePayment(int $id, array $data): Payment
    {

        return $this->paymentRepository->update($id, $data);
    }

    public function deletePayment(int $id): bool
    {
        return $this->paymentRepository->delete($id);
    }
}
