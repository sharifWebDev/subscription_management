<?php

namespace App\Services;

use App\DTOs\FeatureDto;
use App\Models\Feature;
use App\Repositories\Contracts\FeatureRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FeatureService
{
    public function __construct(
        protected FeatureRepositoryInterface $featureRepository
    ) {}

    public function getAllFeatures(Request $request): LengthAwarePaginator
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
            3 => 'description',
            4 => 'type',
            5 => 'scope',
            6 => 'is_resettable',
            7 => 'reset_period',
            8 => 'metadata',
            9 => 'validations',
            10 => 'created_by',
            11 => 'updated_by',
            12 => 'created_at',
            13 => 'updated_at',
            14 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->featureRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Feature)->getFillable() as $column) {
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

    public function getFeatureById(int $id): ?Feature
    {
        $feature = $this->featureRepository->find($id);
        if (! $feature) {
            throw new ModelNotFoundException;
        }

        return $feature;
    }

    // public function storeFeature(FeatureDto $dto, array $data): Feature
    // {
    //  //handleFileUploa
    //  return $this->featureRepository->create((array) $dto);
    //  }

    public function storeFeature(array $data): Feature
    {

        return $this->featureRepository->create($data);
    }

    public function updateFeature(int $id, array $data): Feature
    {

        return $this->featureRepository->update($id, $data);
    }

    public function deleteFeature(int $id): bool
    {
        return $this->featureRepository->delete($id);
    }
}
