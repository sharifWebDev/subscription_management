<?php

namespace App\Services;

use App\DTOs\SubscriptionOrderItemDto;
use App\Models\SubscriptionOrderItem;
use App\Repositories\Contracts\SubscriptionOrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionOrderItemService
{
    public function __construct(
        protected SubscriptionOrderItemRepositoryInterface $subscriptionOrderItemRepository
    ) {}

    public function getAllSubscriptionOrderItems(Request $request): LengthAwarePaginator
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
            1 => 'subscription_order_id',
            2 => 'plan_id',
            3 => 'user_id',
            4 => 'recipient_user_id',
            5 => 'subscription_id',
            6 => 'plan_name',
            7 => 'billing_cycle',
            8 => 'quantity',
            9 => 'recipient_info',
            10 => 'unit_price',
            11 => 'amount',
            12 => 'tax_amount',
            13 => 'discount_amount',
            14 => 'total_amount',
            15 => 'start_date',
            16 => 'end_date',
            17 => 'subscription_status',
            18 => 'processing_error',
            19 => 'processed_at',
            20 => 'created_by',
            21 => 'updated_by',
            22 => 'created_at',
            23 => 'updated_at',
            24 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->subscriptionOrderItemRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new SubscriptionOrderItem)->getFillable() as $column) {
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

    public function getSubscriptionOrderItemById(int $id): ?SubscriptionOrderItem
    {
        $subscriptionOrderItem = $this->subscriptionOrderItemRepository->find($id);
        if (! $subscriptionOrderItem) {
            throw new ModelNotFoundException;
        }

        return $subscriptionOrderItem;
    }

    // public function storeSubscriptionOrderItem(SubscriptionOrderItemDto $dto, array $data): SubscriptionOrderItem
    // {
    //  //handleFileUploa
    //  return $this->subscriptionOrderItemRepository->create((array) $dto);
    //  }

    public function storeSubscriptionOrderItem(array $data): SubscriptionOrderItem
    {

        return $this->subscriptionOrderItemRepository->create($data);
    }

    public function updateSubscriptionOrderItem(int $id, array $data): SubscriptionOrderItem
    {

        return $this->subscriptionOrderItemRepository->update($id, $data);
    }

    public function deleteSubscriptionOrderItem(int $id): bool
    {
        return $this->subscriptionOrderItemRepository->delete($id);
    }
}
