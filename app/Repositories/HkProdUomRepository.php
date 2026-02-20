<?php

namespace App\Repositories;

use App\Models\HkProdUom;
use App\Repositories\Contracts\HkProdUomRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HkProdUomRepository extends BaseRepository implements HkProdUomRepositoryInterface
{
    public function __construct(HkProdUom $hk_prod_uom)
    {
        parent::__construct($hk_prod_uom);
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
