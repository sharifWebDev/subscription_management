<?php

namespace App\Services;

use App\DTOs\SubscriptionEventDto;
use App\Models\SubscriptionEvent;
use App\Repositories\Contracts\SubscriptionEventRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionEventService
{
    public function __construct(
        protected SubscriptionEventRepositoryInterface $subscriptionEventRepository
    ) {}

    public function getAllSubscriptionEvents(Request $request): LengthAwarePaginator
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
            2 => 'type',
            3 => 'data',
            4 => 'changes',
            5 => 'causer_id',
            6 => 'causer_type',
            7 => 'ip_address',
            8 => 'user_agent',
            9 => 'metadata',
            10 => 'occurred_at',
            11 => 'created_by',
            12 => 'updated_by',
            13 => 'created_at',
            14 => 'updated_at',
            15 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->subscriptionEventRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new SubscriptionEvent)->getFillable() as $column) {
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

    public function getSubscriptionEventById(int $id): ?SubscriptionEvent
    {
        $subscriptionEvent = $this->subscriptionEventRepository->find($id);
        if (! $subscriptionEvent) {
            throw new ModelNotFoundException;
        }

        return $subscriptionEvent;
    }

    // public function storeSubscriptionEvent(SubscriptionEventDto $dto, array $data): SubscriptionEvent
    // {
    //  //handleFileUploa
    //  return $this->subscriptionEventRepository->create((array) $dto);
    //  }

    public function storeSubscriptionEvent(array $data): SubscriptionEvent
    {

        return $this->subscriptionEventRepository->create($data);
    }

    public function updateSubscriptionEvent(int $id, array $data): SubscriptionEvent
    {

        return $this->subscriptionEventRepository->update($id, $data);
    }

    public function deleteSubscriptionEvent(int $id): bool
    {
        return $this->subscriptionEventRepository->delete($id);
    }
}
