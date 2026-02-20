<?php

namespace App\Repositories;

use App\Models\PaymentAllocation;
use App\Repositories\Contracts\PaymentAllocationRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentAllocationRepository extends BaseRepository implements PaymentAllocationRepositoryInterface
{
    public function __construct(PaymentAllocation $payment_allocation)
    {
        parent::__construct($payment_allocation);
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
