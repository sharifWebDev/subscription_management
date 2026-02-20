<?php

namespace App\Services;

use App\DTOs\PlanPriceDto;
use App\Models\PlanPrice;
use App\Repositories\Contracts\PlanPriceRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanPriceService
{
    public function __construct(
        protected PlanPriceRepositoryInterface $planPriceRepository
    ) {}

    public function getAllPlanPrices(Request $request): LengthAwarePaginator
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
            1 => 'plan_id',
            2 => 'currency',
            3 => 'amount',
            4 => 'interval',
            5 => 'interval_count',
            6 => 'usage_type',
            7 => 'tiers',
            8 => 'transformations',
            9 => 'stripe_price_id',
            10 => 'active_from',
            11 => 'active_to',
            12 => 'created_by',
            13 => 'updated_by',
            14 => 'created_at',
            15 => 'updated_at',
            16 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->planPriceRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PlanPrice)->getFillable() as $column) {
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

    public function getPlanPriceById(int $id): ?PlanPrice
    {
        $planPrice = $this->planPriceRepository->find($id);
        if (! $planPrice) {
            throw new ModelNotFoundException;
        }

        return $planPrice;
    }

    // public function storePlanPrice(PlanPriceDto $dto, array $data): PlanPrice
    // {
    //  //handleFileUploa
    //  return $this->planPriceRepository->create((array) $dto);
    //  }

    public function storePlanPrice(array $data): PlanPrice
    {

        return $this->planPriceRepository->create($data);
    }

    public function updatePlanPrice(int $id, array $data): PlanPrice
    {

        return $this->planPriceRepository->update($id, $data);
    }

    public function deletePlanPrice(int $id): bool
    {
        return $this->planPriceRepository->delete($id);
    }
}
