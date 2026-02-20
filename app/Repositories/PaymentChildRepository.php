<?php

namespace App\Repositories;

use App\Models\PaymentChild;
use App\Repositories\Contracts\PaymentChildRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentChildRepository extends BaseRepository implements PaymentChildRepositoryInterface
{
    public function __construct(PaymentChild $payment_child)
    {
        parent::__construct($payment_child);
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
