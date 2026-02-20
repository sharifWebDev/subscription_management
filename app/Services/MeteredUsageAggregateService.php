<?php

namespace App\Services;

use App\DTOs\MeteredUsageAggregateDto;
use App\Models\MeteredUsageAggregate;
use App\Repositories\Contracts\MeteredUsageAggregateRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MeteredUsageAggregateService
{
    public function __construct(
        protected MeteredUsageAggregateRepositoryInterface $meteredUsageAggregateRepository
    ) {}

    public function getAllMeteredUsageAggregates(Request $request): LengthAwarePaginator
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
            2 => 'feature_id',
            3 => 'aggregate_date',
            4 => 'aggregate_period',
            5 => 'total_quantity',
            6 => 'tier1_quantity',
            7 => 'tier2_quantity',
            8 => 'tier3_quantity',
            9 => 'total_amount',
            10 => 'record_count',
            11 => 'last_calculated_at',
            12 => 'created_by',
            13 => 'updated_by',
            14 => 'created_at',
            15 => 'updated_at',
            16 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->meteredUsageAggregateRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new MeteredUsageAggregate)->getFillable() as $column) {
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

    public function getMeteredUsageAggregateById(int $id): ?MeteredUsageAggregate
    {
        $meteredUsageAggregate = $this->meteredUsageAggregateRepository->find($id);
        if (! $meteredUsageAggregate) {
            throw new ModelNotFoundException;
        }

        return $meteredUsageAggregate;
    }

    // public function storeMeteredUsageAggregate(MeteredUsageAggregateDto $dto, array $data): MeteredUsageAggregate
    // {
    //  //handleFileUploa
    //  return $this->meteredUsageAggregateRepository->create((array) $dto);
    //  }

    public function storeMeteredUsageAggregate(array $data): MeteredUsageAggregate
    {

        return $this->meteredUsageAggregateRepository->create($data);
    }

    public function updateMeteredUsageAggregate(int $id, array $data): MeteredUsageAggregate
    {

        return $this->meteredUsageAggregateRepository->update($id, $data);
    }

    public function deleteMeteredUsageAggregate(int $id): bool
    {
        return $this->meteredUsageAggregateRepository->delete($id);
    }
}
