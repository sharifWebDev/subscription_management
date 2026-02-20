<?php

namespace App\Repositories;

use App\Models\Discount;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DiscountRepository extends BaseRepository implements DiscountRepositoryInterface
{
    public function __construct(Discount $discount)
    {
        parent::__construct($discount);
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
