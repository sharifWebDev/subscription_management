<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentTransactionResource extends BaseResource
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
            'payment_master_id' => $this->payment_master_id,
            'payment_master_id_details' => $this->PaymentMaster,
            'payment_master_name' => $this->PaymentMaster?->name ?? $this->PaymentMaster?->title ?? $this->PaymentMaster?->code ?? null,
            'payment_child_id' => $this->payment_child_id,
            'payment_child_id_details' => $this->PaymentChild,
            'payment_child_name' => $this->PaymentChild?->name ?? $this->PaymentChild?->title ?? $this->PaymentChild?->code ?? null,
            'transaction_id' => $this->transaction_id,
            'reference_id' => $this->reference_id,
            'type' => $this->type,
            'payment_method' => $this->payment_method,
            'payment_gateway' => $this->payment_gateway,
            'gateway_response' => $this->gateway_response ? json_decode($this->gateway_response, true) : [],
            'payment_method_details' => $this->payment_method_details ? json_decode($this->payment_method_details, true) : [],
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'fee' => $this->fee ? (float) $this->fee : 0.0,
            'tax' => $this->tax ? (float) $this->tax : 0.0,
            'net_amount' => $this->net_amount ? (float) $this->net_amount : 0.0,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate ? (float) $this->exchange_rate : 0.0,
            'status' => $this->status,
            'card_last4' => $this->card_last4,
            'card_brand' => $this->card_brand,
            'card_country' => $this->card_country,
            'card_exp_month' => (int) $this->card_exp_month,
            'card_exp_year' => (int) $this->card_exp_year,
            'bank_name' => $this->bank_name,
            'bank_account_last4' => $this->bank_account_last4,
            'bank_routing_number' => $this->bank_routing_number,
            'wallet_type' => $this->wallet_type,
            'wallet_number' => $this->wallet_number,
            'wallet_transaction_id' => $this->wallet_transaction_id,
            'installment_number' => (int) $this->installment_number,
            'total_installments' => (int) $this->total_installments,
            'initiated_at' => $this->initiated_at?->format('M d, Y h:i A'),
            'authorized_at' => $this->authorized_at?->format('M d, Y h:i A'),
            'captured_at' => $this->captured_at?->format('M d, Y h:i A'),
            'completed_at' => $this->completed_at?->format('M d, Y h:i A'),
            'failed_at' => $this->failed_at?->format('M d, Y h:i A'),
            'refunded_at' => $this->refunded_at?->format('M d, Y h:i A'),
            'fraud_indicators' => $this->fraud_indicators ? json_decode($this->fraud_indicators, true) : [],
            'risk_score' => $this->risk_score ? (float) $this->risk_score : 0.0,
            'requires_review' => (bool) $this->requires_review,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'custom_fields' => $this->custom_fields ? json_decode($this->custom_fields, true) : [],
            'notes' => $this->notes,
            'failure_reason' => $this->failure_reason,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'location_data' => $this->location_data ? json_decode($this->location_data, true) : [],
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A')
        ];
    }
}
