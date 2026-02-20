<?php

namespace App\Repositories;

use App\Models\PaymentTransaction;
use App\Repositories\Contracts\PaymentTransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentTransactionRepository extends BaseRepository implements PaymentTransactionRepositoryInterface
{
    public function __construct(PaymentTransaction $payment_transaction)
    {
        parent::__construct($payment_transaction);
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
