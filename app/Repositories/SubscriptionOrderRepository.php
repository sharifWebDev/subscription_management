<?php

namespace App\Repositories;

use App\Models\SubscriptionOrder;
use App\Repositories\Contracts\SubscriptionOrderRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SubscriptionOrderRepository extends BaseRepository implements SubscriptionOrderRepositoryInterface
{
    public function __construct(SubscriptionOrder $subscription_order)
    {
        parent::__construct($subscription_order);
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
