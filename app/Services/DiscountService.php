<?php

namespace App\Services;

use App\DTOs\DiscountDto;
use App\Models\Discount;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DiscountService
{
    public function __construct(
        protected DiscountRepositoryInterface $discountRepository
    ) {}

    public function getAllDiscounts(Request $request): LengthAwarePaginator
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
            3 => 'type',
            4 => 'amount',
            5 => 'currency',
            6 => 'applies_to',
            7 => 'applies_to_ids',
            8 => 'max_redemptions',
            9 => 'times_redeemed',
            10 => 'is_active',
            11 => 'starts_at',
            12 => 'expires_at',
            13 => 'duration',
            14 => 'duration_in_months',
            15 => 'metadata',
            16 => 'restrictions',
            17 => 'created_by',
            18 => 'updated_by',
            19 => 'created_at',
            20 => 'updated_at',
            21 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->discountRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Discount)->getFillable() as $column) {
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

    public function getDiscountById(int $id): ?Discount
    {
        $discount = $this->discountRepository->find($id);
        if (! $discount) {
            throw new ModelNotFoundException;
        }

        return $discount;
    }

    // public function storeDiscount(DiscountDto $dto, array $data): Discount
    // {
    //  //handleFileUploa
    //  return $this->discountRepository->create((array) $dto);
    //  }

    public function storeDiscount(array $data): Discount
    {

        return $this->discountRepository->create($data);
    }

    public function updateDiscount(int $id, array $data): Discount
    {

        return $this->discountRepository->update($id, $data);
    }

    public function deleteDiscount(int $id): bool
    {
        return $this->discountRepository->delete($id);
    }
}
