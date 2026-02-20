<?php

namespace App\Services;

use App\DTOs\SubscriptionOrderDto;
use App\Models\SubscriptionOrder;
use App\Repositories\Contracts\SubscriptionOrderRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionOrderService
{
    public function __construct(
        protected SubscriptionOrderRepositoryInterface $subscriptionOrderRepository
    ) {}

    public function getAllSubscriptionOrders(Request $request): LengthAwarePaginator
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
            1 => 'user_id',
            2 => 'payment_master_id',
            3 => 'order_number',
            4 => 'status',
            5 => 'type',
            6 => 'subtotal',
            7 => 'tax_amount',
            8 => 'discount_amount',
            9 => 'total_amount',
            10 => 'currency',
            11 => 'customer_info',
            12 => 'billing_address',
            13 => 'ordered_at',
            14 => 'processed_at',
            15 => 'cancelled_at',
            16 => 'coupon_code',
            17 => 'applied_discounts',
            18 => 'metadata',
            19 => 'notes',
            20 => 'failure_reason',
            21 => 'created_by',
            22 => 'updated_by',
            23 => 'created_at',
            24 => 'updated_at',
            25 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->subscriptionOrderRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new SubscriptionOrder)->getFillable() as $column) {
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

    public function getSubscriptionOrderById(int $id): ?SubscriptionOrder
    {
        $subscriptionOrder = $this->subscriptionOrderRepository->find($id);
        if (! $subscriptionOrder) {
            throw new ModelNotFoundException;
        }

        return $subscriptionOrder;
    }

    // public function storeSubscriptionOrder(SubscriptionOrderDto $dto, array $data): SubscriptionOrder
    // {
    //  //handleFileUploa
    //  return $this->subscriptionOrderRepository->create((array) $dto);
    //  }

    public function storeSubscriptionOrder(array $data): SubscriptionOrder
    {

        return $this->subscriptionOrderRepository->create($data);
    }

    public function updateSubscriptionOrder(int $id, array $data): SubscriptionOrder
    {

        return $this->subscriptionOrderRepository->update($id, $data);
    }

    public function deleteSubscriptionOrder(int $id): bool
    {
        return $this->subscriptionOrderRepository->delete($id);
    }
}
