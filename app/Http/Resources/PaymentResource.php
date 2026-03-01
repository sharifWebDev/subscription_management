<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentResource extends BaseResource
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
            'invoice_id' => $this->invoice_id,
            'invoice_id_details' => $this->Invoice,
            'invoice_name' => $this->Invoice?->name ?? $this->Invoice?->title ?? $this->Invoice?->code ?? null,
            'user_id' => $this->user_id,
            'user_id_details' => $this->User,
            'user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
            'external_id' => $this->external_id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'fee' => $this->fee ? (float) $this->fee : 0.0,
            'net' => $this->net ? (float) $this->net : 0.0,
            'currency' => $this->currency,
            'gateway' => $this->gateway,
            'gateway_response' => $this->gateway_response ? json_decode($this->gateway_response, true) : [],
            'payment_method' => $this->payment_method ? json_decode($this->payment_method, true) : [],
            'processed_at' => $this->processed_at?->format('M d, Y h:i A'),
            'refunded_at' => $this->refunded_at?->format('M d, Y h:i A'),
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'fraud_indicators' => $this->fraud_indicators ? json_decode($this->fraud_indicators, true) : [],
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }
}
