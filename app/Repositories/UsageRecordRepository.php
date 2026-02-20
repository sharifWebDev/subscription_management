<?php

namespace App\Repositories;

use App\Models\UsageRecord;
use App\Repositories\Contracts\UsageRecordRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UsageRecordRepository extends BaseRepository implements UsageRecordRepositoryInterface
{
    public function __construct(UsageRecord $usage_record)
    {
        parent::__construct($usage_record);
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
