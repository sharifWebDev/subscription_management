<?php

namespace App\Repositories;

use App\Models\PlanPrice;
use App\Repositories\Contracts\PlanPriceRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PlanPriceRepository extends BaseRepository implements PlanPriceRepositoryInterface
{
    public function __construct(PlanPrice $plan_price)
    {
        parent::__construct($plan_price);
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
