<?php

namespace App\Services;

use App\DTOs\SubscriptionItemDto;
use App\Models\SubscriptionItem;
use App\Repositories\Contracts\SubscriptionItemRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionItemService
{
    public function __construct(
        protected SubscriptionItemRepositoryInterface $subscriptionItemRepository
    ) {}

    public function getAllSubscriptionItems(Request $request): LengthAwarePaginator
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
            2 => 'plan_price_id',
            3 => 'feature_id',
            4 => 'quantity',
            5 => 'unit_price',
            6 => 'amount',
            7 => 'metadata',
            8 => 'tiers',
            9 => 'effective_from',
            10 => 'effective_to',
            11 => 'created_by',
            12 => 'updated_by',
            13 => 'created_at',
            14 => 'updated_at',
            15 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->subscriptionItemRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new SubscriptionItem)->getFillable() as $column) {
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

    public function getSubscriptionItemById(int $id): ?SubscriptionItem
    {
        $subscriptionItem = $this->subscriptionItemRepository->find($id);
        if (! $subscriptionItem) {
            throw new ModelNotFoundException;
        }

        return $subscriptionItem;
    }

    // public function storeSubscriptionItem(SubscriptionItemDto $dto, array $data): SubscriptionItem
    // {
    //  //handleFileUploa
    //  return $this->subscriptionItemRepository->create((array) $dto);
    //  }

    public function storeSubscriptionItem(array $data): SubscriptionItem
    {

        return $this->subscriptionItemRepository->create($data);
    }

    public function updateSubscriptionItem(int $id, array $data): SubscriptionItem
    {

        return $this->subscriptionItemRepository->update($id, $data);
    }

    public function deleteSubscriptionItem(int $id): bool
    {
        return $this->subscriptionItemRepository->delete($id);
    }
}
