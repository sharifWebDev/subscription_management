<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    public function __construct(Plan $plan)
    {
        parent::__construct($plan);
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
