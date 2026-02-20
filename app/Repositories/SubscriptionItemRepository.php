<?php

namespace App\Repositories;

use App\Models\SubscriptionItem;
use App\Repositories\Contracts\SubscriptionItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SubscriptionItemRepository extends BaseRepository implements SubscriptionItemRepositoryInterface
{
    public function __construct(SubscriptionItem $subscription_item)
    {
        parent::__construct($subscription_item);
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
