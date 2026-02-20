<?php

namespace App\Services;

use App\DTOs\PaymentWebhookLogDto;
use App\Models\PaymentWebhookLog;
use App\Repositories\Contracts\PaymentWebhookLogRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentWebhookLogService
{
    public function __construct(
        protected PaymentWebhookLogRepositoryInterface $paymentWebhookLogRepository
    ) {}

    public function getAllPaymentWebhookLogs(Request $request): LengthAwarePaginator
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
            1 => 'payment_gateway_id',
            2 => 'payment_transaction_id',
            3 => 'gateway',
            4 => 'event_type',
            5 => 'webhook_id',
            6 => 'reference_id',
            7 => 'payload',
            8 => 'headers',
            9 => 'response_code',
            10 => 'response_body',
            11 => 'status',
            12 => 'processing_error',
            13 => 'retry_count',
            14 => 'next_retry_at',
            15 => 'received_at',
            16 => 'processed_at',
            17 => 'ip_address',
            18 => 'is_verified',
            19 => 'verification_error',
            20 => 'created_by',
            21 => 'updated_by',
            22 => 'created_at',
            23 => 'updated_at',
            24 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentWebhookLogRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PaymentWebhookLog)->getFillable() as $column) {
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

    public function getPaymentWebhookLogById(int $id): ?PaymentWebhookLog
    {
        $paymentWebhookLog = $this->paymentWebhookLogRepository->find($id);
        if (! $paymentWebhookLog) {
            throw new ModelNotFoundException;
        }

        return $paymentWebhookLog;
    }

    // public function storePaymentWebhookLog(PaymentWebhookLogDto $dto, array $data): PaymentWebhookLog
    // {
    //  //handleFileUploa
    //  return $this->paymentWebhookLogRepository->create((array) $dto);
    //  }

    public function storePaymentWebhookLog(array $data): PaymentWebhookLog
    {

        return $this->paymentWebhookLogRepository->create($data);
    }

    public function updatePaymentWebhookLog(int $id, array $data): PaymentWebhookLog
    {

        return $this->paymentWebhookLogRepository->update($id, $data);
    }

    public function deletePaymentWebhookLog(int $id): bool
    {
        return $this->paymentWebhookLogRepository->delete($id);
    }
}
