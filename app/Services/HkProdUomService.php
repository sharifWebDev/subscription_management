<?php

namespace App\Services;

use App\DTOs\HkProdUomDto;
use App\Models\HkProdUom;
use App\Repositories\Contracts\HkProdUomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class HkProdUomService
{
    public function __construct(
        protected HkProdUomRepositoryInterface $hkProdUomRepository
    ) {}

    public function getAllHkProdUoms(Request $request): LengthAwarePaginator
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
            1 => 'code',
            2 => 'name',
            3 => 'is_active',
            4 => 'sequence',
            5 => 'created_by',
            6 => 'updated_by',
            7 => 'created_at',
            8 => 'updated_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->hkProdUomRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new HkProdUom)->getFillable() as $column) {
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

    public function getHkProdUomById(int $id): ?HkProdUom
    {
        $hkProdUom = $this->hkProdUomRepository->find($id);
        if (! $hkProdUom) {
            throw new ModelNotFoundException;
        }

        return $hkProdUom;
    }

    // public function storeHkProdUom(HkProdUomDto $dto, array $data): HkProdUom
    // {
    //  //handleFileUploa
    //  return $this->hkProdUomRepository->create((array) $dto);
    //  }

    public function storeHkProdUom(array $data): HkProdUom
    {

        return $this->hkProdUomRepository->create($data);
    }

    public function updateHkProdUom(int $id, array $data): HkProdUom
    {

        return $this->hkProdUomRepository->update($id, $data);
    }

    public function deleteHkProdUom(int $id): bool
    {
        return $this->hkProdUomRepository->delete($id);
    }
}
