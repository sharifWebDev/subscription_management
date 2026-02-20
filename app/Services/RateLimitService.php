<?php

namespace App\Services;

use App\DTOs\RateLimitDto;
use App\Models\RateLimit;
use App\Repositories\Contracts\RateLimitRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RateLimitService
{
    public function __construct(
        protected RateLimitRepositoryInterface $rateLimitRepository
    ) {}

    public function getAllRateLimits(Request $request): LengthAwarePaginator
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
            3 => 'key',
            4 => 'max_attempts',
            5 => 'decay_seconds',
            6 => 'remaining',
            7 => 'resets_at',
            8 => 'created_by',
            9 => 'updated_by',
            10 => 'created_at',
            11 => 'updated_at',
            12 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->rateLimitRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new RateLimit)->getFillable() as $column) {
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

    public function getRateLimitById(int $id): ?RateLimit
    {
        $rateLimit = $this->rateLimitRepository->find($id);
        if (! $rateLimit) {
            throw new ModelNotFoundException;
        }

        return $rateLimit;
    }

    // public function storeRateLimit(RateLimitDto $dto, array $data): RateLimit
    // {
    //  //handleFileUploa
    //  return $this->rateLimitRepository->create((array) $dto);
    //  }

    public function storeRateLimit(array $data): RateLimit
    {

        return $this->rateLimitRepository->create($data);
    }

    public function updateRateLimit(int $id, array $data): RateLimit
    {

        return $this->rateLimitRepository->update($id, $data);
    }

    public function deleteRateLimit(int $id): bool
    {
        return $this->rateLimitRepository->delete($id);
    }
}
