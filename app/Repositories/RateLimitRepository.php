<?php

namespace App\Repositories;

use App\Models\RateLimit;
use App\Repositories\Contracts\RateLimitRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RateLimitRepository extends BaseRepository implements RateLimitRepositoryInterface
{
    public function __construct(RateLimit $rate_limit)
    {
        parent::__construct($rate_limit);
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
