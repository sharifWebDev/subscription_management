<?php

namespace App\Repositories;

use App\Models\Refund;
use App\Repositories\Contracts\RefundRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RefundRepository extends BaseRepository implements RefundRepositoryInterface
{
    public function __construct(Refund $refund)
    {
        parent::__construct($refund);
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
