<?php

namespace App\Repositories;

use App\Models\SubscriptionEvent;
use App\Repositories\Contracts\SubscriptionEventRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SubscriptionEventRepository extends BaseRepository implements SubscriptionEventRepositoryInterface
{
    public function __construct(SubscriptionEvent $subscription_event)
    {
        parent::__construct($subscription_event);
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
