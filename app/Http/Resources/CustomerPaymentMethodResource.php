<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CustomerPaymentMethodResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_id_details' => $this->User,
            'user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
            'type' => $this->type,
            'gateway' => $this->gateway,
            'gateway_customer_id' => $this->gateway_customer_id,
            'gateway_payment_method_id' => $this->gateway_payment_method_id,
            'nickname' => $this->nickname,
            'is_default' => (bool) $this->is_default,
            'is_verified' => (bool) $this->is_verified,
            'card_last4' => $this->card_last4,
            'card_brand' => $this->card_brand,
            'card_exp_month' => (int) $this->card_exp_month,
            'card_exp_year' => (int) $this->card_exp_year,
            'card_country' => $this->card_country,
            'bank_name' => $this->bank_name,
            'bank_account_last4' => $this->bank_account_last4,
            'bank_account_type' => $this->bank_account_type,
            'bank_routing_number' => $this->bank_routing_number,
            'wallet_type' => $this->wallet_type,
            'wallet_number' => $this->wallet_number,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'gateway_metadata' => $this->gateway_metadata ? json_decode($this->gateway_metadata, true) : [],
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
        ];
    }
}
