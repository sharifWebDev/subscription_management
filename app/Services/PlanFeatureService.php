<?php

namespace App\Services;

use App\DTOs\PlanFeatureDto;
use App\Models\PlanFeature;
use App\Repositories\Contracts\PlanFeatureRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanFeatureService
{
    public function __construct(
        protected PlanFeatureRepositoryInterface $planFeatureRepository
    ) {}

    public function getAllPlanFeatures(Request $request): LengthAwarePaginator
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
            2 => 'feature_id',
            3 => 'value',
            4 => 'config',
            5 => 'sort_order',
            6 => 'is_inherited',
            7 => 'parent_feature_id',
            8 => 'effective_from',
            9 => 'effective_to',
            10 => 'created_by',
            11 => 'updated_by',
            12 => 'created_at',
            13 => 'updated_at',
            14 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->planFeatureRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new PlanFeature)->getFillable() as $column) {
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

    public function getPlanFeatureById(int $id): ?PlanFeature
    {
        $planFeature = $this->planFeatureRepository->find($id);
        if (! $planFeature) {
            throw new ModelNotFoundException;
        }

        return $planFeature;
    }

    // public function storePlanFeature(PlanFeatureDto $dto, array $data): PlanFeature
    // {
    //  //handleFileUploa
    //  return $this->planFeatureRepository->create((array) $dto);
    //  }

    public function storePlanFeature(array $data): PlanFeature
    {

        return $this->planFeatureRepository->create($data);
    }

    public function updatePlanFeature(int $id, array $data): PlanFeature
    {

        return $this->planFeatureRepository->update($id, $data);
    }

    public function deletePlanFeature(int $id): bool
    {
        return $this->planFeatureRepository->delete($id);
    }
}
