<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    public function __construct(PaymentMethod $payment_method)
    {
        parent::__construct($payment_method);
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
