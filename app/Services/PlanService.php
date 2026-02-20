<?php

namespace App\Services;

use App\DTOs\PlanDto;
use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanService
{
    public function __construct(
        protected PlanRepositoryInterface $planRepository
    ) {}

    public function getAllPlans(Request $request): LengthAwarePaginator
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
            2 => 'slug',
            3 => 'code',
            4 => 'description',
            5 => 'type',
            6 => 'billing_period',
            7 => 'billing_interval',
            8 => 'is_active',
            9 => 'is_visible',
            10 => 'sort_order',
            11 => 'is_featured',
            12 => 'metadata',
            13 => 'created_by',
            14 => 'updated_by',
            15 => 'created_at',
            16 => 'updated_at',
            17 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->planRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Plan)->getFillable() as $column) {
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

    public function getPlanById(int $id): ?Plan
    {
        $plan = $this->planRepository->find($id);
        if (! $plan) {
            throw new ModelNotFoundException;
        }

        return $plan;
    }

    // public function storePlan(PlanDto $dto, array $data): Plan
    // {
    //  //handleFileUploa
    //  return $this->planRepository->create((array) $dto);
    //  }

    public function storePlan(array $data): Plan
    {

        return $this->planRepository->create($data);
    }

    public function updatePlan(int $id, array $data): Plan
    {

        return $this->planRepository->update($id, $data);
    }

    public function deletePlan(int $id): bool
    {
        return $this->planRepository->delete($id);
    }
}
