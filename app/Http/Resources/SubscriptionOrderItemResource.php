<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionOrderItemResource extends BaseResource
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
            'subscription_order_id' => $this->subscription_order_id,
            'subscription_order_id_details' => $this->SubscriptionOrder,
            'subscription_order_name' => $this->SubscriptionOrder?->name ?? $this->SubscriptionOrder?->title ?? $this->SubscriptionOrder?->code ?? null,
            'plan_id' => $this->plan_id,
            'plan_id_details' => $this->Plan,
            'plan_name' => $this->Plan?->name ?? $this->Plan?->title ?? $this->Plan?->code ?? null,
            'user_id' => $this->user_id,
            'user_id_details' => $this->User,
            'user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
            'recipient_user_id' => $this->recipient_user_id,
            'recipient_user_id_details' => $this->User,
            'recipient_user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
            'subscription_id' => $this->subscription_id,
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'plan_name' => $this->plan_name,
            'billing_cycle' => $this->billing_cycle,
            'quantity' => (int) $this->quantity,
            'recipient_info' => $this->recipient_info ? json_decode($this->recipient_info, true) : [],
            'unit_price' => $this->unit_price ? (float) $this->unit_price : 0.0,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'tax_amount' => $this->tax_amount ? (float) $this->tax_amount : 0.0,
            'discount_amount' => $this->discount_amount ? (float) $this->discount_amount : 0.0,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : 0.0,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'subscription_status' => $this->subscription_status,
            'processing_error' => $this->processing_error,
            'processed_at' => $this->processed_at?->format('M d, Y h:i A'),
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }
}
