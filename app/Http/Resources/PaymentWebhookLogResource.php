<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentWebhookLogResource extends BaseResource
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
            'payment_gateway_id' => $this->payment_gateway_id,
            'payment_gateway_id_details' => $this->PaymentGateway,
            'payment_gateway_name' => $this->PaymentGateway?->name ?? $this->PaymentGateway?->title ?? $this->PaymentGateway?->code ?? null,
            'payment_transaction_id' => $this->payment_transaction_id,
            'payment_transaction_id_details' => $this->PaymentTransaction,
            'payment_transaction_name' => $this->PaymentTransaction?->name ?? $this->PaymentTransaction?->title ?? $this->PaymentTransaction?->code ?? null,
            'gateway' => $this->gateway,
            'event_type' => $this->event_type,
            'webhook_id' => $this->webhook_id,
            'reference_id' => $this->reference_id,
            'payload' => $this->payload ? json_decode($this->payload, true) : [],
            'headers' => $this->headers ? json_decode($this->headers, true) : [],
            'response_code' => (int) $this->response_code,
            'response_body' => $this->response_body,
            'status' => $this->status,
            'processing_error' => $this->processing_error,
            'retry_count' => (int) $this->retry_count,
            'next_retry_at' => $this->next_retry_at?->format('Y-m-d H:i:s'),
            'received_at' => $this->received_at?->format('Y-m-d H:i:s'),
            'processed_at' => $this->processed_at?->format('Y-m-d H:i:s'),
            'ip_address' => $this->ip_address,
            'is_verified' => (bool) $this->is_verified,
            'verification_error' => $this->verification_error,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}
