<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionResource extends BaseResource
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
            'plan_id' => $this->plan_id,
            'plan' => $this->Plan,
            'plan_name' => $this->Plan?->name ?? $this->Plan?->title ?? $this->Plan?->code ?? null,
            'plan_price_id' => $this->plan_price_id,
            'plan_price_id_details' => $this->PlanPrice,
            'price' => $this->PlanPrice,
            'plan_price_name' => $this->PlanPrice?->name ?? $this->PlanPrice?->title ?? $this->PlanPrice?->code ?? null,
            'parent_subscription_id' => $this->parent_subscription_id,
            'status' => $this->status,
            'billing_cycle_anchor' => $this->billing_cycle_anchor,
            'quantity' => (int) $this->quantity,
            'unit_price' => $this->unit_price ? (float) $this->unit_price : 0.0,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'currency' => $this->currency,
            'trial_starts_at' => $this->trial_starts_at?->format('Y-m-d H:i:s'),
            'trial_ends_at' => $this->trial_ends_at?->format('Y-m-d H:i:s'),
            'trial_converted' => (bool) $this->trial_converted,
            'current_period_starts_at' => $this->current_period_starts_at?->format('Y-m-d H:i:s'),
            'current_period_ends_at' => $this->current_period_ends_at?->format('Y-m-d H:i:s'),
            'billing_cycle_anchor_date' => $this->billing_cycle_anchor_date?->format('Y-m-d H:i:s'),
            'canceled_at' => $this->canceled_at?->format('Y-m-d H:i:s'),
            'cancellation_reason' => $this->cancellation_reason,
            'prorate' => (bool) $this->prorate,
            'proration_amount' => $this->proration_amount ? (float) $this->proration_amount : 0.0,
            'proration_date' => $this->proration_date?->format('Y-m-d H:i:s'),
            'gateway' => $this->gateway,
            'gateway_subscription_id' => $this->gateway_subscription_id,
            'gateway_customer_id' => $this->gateway_customer_id,
            'gateway_metadata' => $this->gateway_metadata ? json_decode($this->gateway_metadata, true) : [],
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'history' => $this->history ? json_decode($this->history, true) : [],
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}
