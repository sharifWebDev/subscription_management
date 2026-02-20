<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentGatewayResource extends BaseResource
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
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'is_active' => (bool) $this->is_active,
            'is_test_mode' => (bool) $this->is_test_mode,
            'supports_recurring' => (bool) $this->supports_recurring,
            'supports_refunds' => (bool) $this->supports_refunds,
            'supports_installments' => (bool) $this->supports_installments,
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'webhook_secret' => $this->webhook_secret,
            'merchant_id' => $this->merchant_id,
            'store_id' => $this->store_id,
            'store_password' => $this->store_password,
            'base_url' => $this->base_url,
            'callback_url' => $this->callback_url,
            'webhook_url' => $this->webhook_url,
            'supported_currencies' => $this->supported_currencies ? json_decode($this->supported_currencies, true) : [],
            'supported_countries' => $this->supported_countries ? json_decode($this->supported_countries, true) : [],
            'excluded_countries' => $this->excluded_countries ? json_decode($this->excluded_countries, true) : [],
            'percentage_fee' => $this->percentage_fee ? (float) $this->percentage_fee : 0.0,
            'fixed_fee' => $this->fixed_fee ? (float) $this->fixed_fee : 0.0,
            'fee_currency' => $this->fee_currency,
            'fee_structure' => $this->fee_structure ? json_decode($this->fee_structure, true) : [],
            'config' => $this->config ? json_decode($this->config, true) : [],
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'settlement_days' => (int) $this->settlement_days,
            'refund_days' => (int) $this->refund_days,
            'min_amount' => $this->min_amount ? (float) $this->min_amount : 0.0,
            'max_amount' => $this->max_amount ? (float) $this->max_amount : 0.0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}
