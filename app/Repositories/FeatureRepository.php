<?php

namespace App\Repositories;

use App\Models\Feature;
use App\Repositories\Contracts\FeatureRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FeatureRepository extends BaseRepository implements FeatureRepositoryInterface
{
    public function __construct(Feature $feature)
    {
        parent::__construct($feature);
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
