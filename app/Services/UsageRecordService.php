<?php

namespace App\Services;

use App\DTOs\UsageRecordDto;
use App\Models\UsageRecord;
use App\Repositories\Contracts\UsageRecordRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UsageRecordService
{
    public function __construct(
        protected UsageRecordRepositoryInterface $usageRecordRepository
    ) {}

    public function getAllUsageRecords(Request $request): LengthAwarePaginator
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
            1 => 'subscription_id',
            2 => 'subscription_item_id',
            3 => 'feature_id',
            4 => 'quantity',
            5 => 'tier_quantity',
            6 => 'amount',
            7 => 'unit',
            8 => 'status',
            9 => 'recorded_at',
            10 => 'billing_date',
            11 => 'metadata',
            12 => 'dimensions',
            13 => 'created_by',
            14 => 'updated_by',
            15 => 'created_at',
            16 => 'updated_at',
            17 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->usageRecordRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new UsageRecord)->getFillable() as $column) {
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

    public function getUsageRecordById(int $id): ?UsageRecord
    {
        $usageRecord = $this->usageRecordRepository->find($id);
        if (! $usageRecord) {
            throw new ModelNotFoundException;
        }

        return $usageRecord;
    }

    // public function storeUsageRecord(UsageRecordDto $dto, array $data): UsageRecord
    // {
    //  //handleFileUploa
    //  return $this->usageRecordRepository->create((array) $dto);
    //  }

    public function storeUsageRecord(array $data): UsageRecord
    {

        return $this->usageRecordRepository->create($data);
    }

    public function updateUsageRecord(int $id, array $data): UsageRecord
    {

        return $this->usageRecordRepository->update($id, $data);
    }

    public function deleteUsageRecord(int $id): bool
    {
        return $this->usageRecordRepository->delete($id);
    }
}
