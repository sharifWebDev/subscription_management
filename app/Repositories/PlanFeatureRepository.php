<?php

namespace App\Repositories;

use App\Models\PlanFeature;
use App\Repositories\Contracts\PlanFeatureRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PlanFeatureRepository extends BaseRepository implements PlanFeatureRepositoryInterface
{
    public function __construct(PlanFeature $plan_feature)
    {
        parent::__construct($plan_feature);
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
