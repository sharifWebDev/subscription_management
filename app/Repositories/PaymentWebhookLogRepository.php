<?php

namespace App\Repositories;

use App\Models\PaymentWebhookLog;
use App\Repositories\Contracts\PaymentWebhookLogRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentWebhookLogRepository extends BaseRepository implements PaymentWebhookLogRepositoryInterface
{
    public function __construct(PaymentWebhookLog $payment_webhook_log)
    {
        parent::__construct($payment_webhook_log);
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
