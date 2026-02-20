<?php

namespace App\Services;

use App\DTOs\SubscriptionDto;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepositoryInterface $subscriptionRepository
    ) {}

    public function getAllSubscriptions(Request $request): LengthAwarePaginator
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
            2 => 'plan_id',
            3 => 'plan_price_id',
            4 => 'parent_subscription_id',
            5 => 'status',
            6 => 'billing_cycle_anchor',
            7 => 'quantity',
            8 => 'unit_price',
            9 => 'amount',
            10 => 'currency',
            11 => 'trial_starts_at',
            12 => 'trial_ends_at',
            13 => 'trial_converted',
            14 => 'current_period_starts_at',
            15 => 'current_period_ends_at',
            16 => 'billing_cycle_anchor_date',
            17 => 'canceled_at',
            18 => 'cancellation_reason',
            19 => 'prorate',
            20 => 'proration_amount',
            21 => 'proration_date',
            22 => 'gateway',
            23 => 'gateway_subscription_id',
            24 => 'gateway_customer_id',
            25 => 'gateway_metadata',
            26 => 'metadata',
            27 => 'history',
            28 => 'created_by',
            29 => 'updated_by',
            30 => 'created_at',
            31 => 'updated_at',
            32 => 'deleted_at',
            33 => 'is_active',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->subscriptionRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Subscription)->getFillable() as $column) {
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

    public function getSubscriptionById(int $id): ?Subscription
    {
        $subscription = $this->subscriptionRepository->find($id);
        if (! $subscription) {
            throw new ModelNotFoundException;
        }

        return $subscription;
    }

    // public function storeSubscription(SubscriptionDto $dto, array $data): Subscription
    // {
    //  //handleFileUploa
    //  return $this->subscriptionRepository->create((array) $dto);
    //  }

    public function storeSubscription(array $data): Subscription
    {

        return $this->subscriptionRepository->create($data);
    }

    public function updateSubscription(int $id, array $data): Subscription
    {

        return $this->subscriptionRepository->update($id, $data);
    }

    public function deleteSubscription(int $id): bool
    {
        return $this->subscriptionRepository->delete($id);
    }
}
