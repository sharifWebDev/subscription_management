<?php

namespace App\Repositories;

use App\Models\PaymentMaster;
use App\Repositories\Contracts\PaymentMasterRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentMasterRepository extends BaseRepository implements PaymentMasterRepositoryInterface
{
    public function __construct(PaymentMaster $payment_master)
    {
        parent::__construct($payment_master);
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
