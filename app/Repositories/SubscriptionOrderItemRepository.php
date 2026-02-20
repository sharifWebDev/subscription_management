<?php

namespace App\Repositories;

use App\Models\SubscriptionOrderItem;
use App\Repositories\Contracts\SubscriptionOrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SubscriptionOrderItemRepository extends BaseRepository implements SubscriptionOrderItemRepositoryInterface
{
    public function __construct(SubscriptionOrderItem $subscription_order_item)
    {
        parent::__construct($subscription_order_item);
    }

    public function findByEmail(string $email): ?Model
    {
        return $this->model->where('email', $email)->first();
    }

    public function export(Request $request): Builder
    {
        return $this->model->newQuery();
    }
}
