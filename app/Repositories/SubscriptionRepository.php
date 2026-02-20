<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function __construct(Subscription $subscription)
    {
        parent::__construct($subscription);
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
