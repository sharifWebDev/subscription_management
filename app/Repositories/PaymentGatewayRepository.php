<?php

namespace App\Repositories;

use App\Models\PaymentGateway;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentGatewayRepository extends BaseRepository implements PaymentGatewayRepositoryInterface
{
    public function __construct(PaymentGateway $payment_gateway)
    {
        parent::__construct($payment_gateway);
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
