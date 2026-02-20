<?php

namespace App\Repositories;

use App\Models\MeteredUsageAggregate;
use App\Repositories\Contracts\MeteredUsageAggregateRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MeteredUsageAggregateRepository extends BaseRepository implements MeteredUsageAggregateRepositoryInterface
{
    public function __construct(MeteredUsageAggregate $metered_usage_aggregate)
    {
        parent::__construct($metered_usage_aggregate);
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
