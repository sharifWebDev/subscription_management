<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InvoiceResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name ?? 'Unknown User',
            'subscription_id' => $this->subscription_id,
            'subscription_name' => $this->subscription?->plan?->name ?? 'Unknown Subscription',
            'number' => $this->number,
            'external_id' => $this->external_id,
            'status' => $this->status,
            'type' => $this->type,
            'subtotal' => (float) $this->subtotal,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'amount_due' => (float) $this->amount_due,
            'amount_paid' => (float) $this->amount_paid,
            'currency' => $this->currency,
            'formatted_subtotal' => $this->formatMoney($this->subtotal, $this->currency),
            'formatted_tax' => $this->formatMoney($this->tax, $this->currency),
            'formatted_total' => $this->formatMoney($this->total, $this->currency),
            'issue_date' => $this->issue_date?->format('M d, Y h:i A'),
            'due_date' => $this->due_date?->format('M d, Y h:i A'),
            'paid_at' => $this->paid_at?->format('M d, Y h:i A'),
            'line_items' => json_decode($this->line_items, true) ?? [],
            'tax_rates' => json_decode($this->tax_rates, true) ?? [],
            'discounts' => json_decode($this->discounts, true) ?? [],
            'metadata' => json_decode($this->metadata, true) ?? [],
            'history' => json_decode($this->history, true) ?? [],
            'pdf_url' => $this->pdf_url,
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }

    /**
     * Format money with currency
     */
    protected function formatMoney($amount, $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'BDT' => '৳',
            'INR' => '₹',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        return $symbol.' '.number_format($amount, 2);
    }
}

// return [
//     'id' => $this->id,
//     'user_id' => $this->user_id,
//     'user_id_details' => $this->User,
//     'user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
//     'subscription_id' => $this->subscription_id,
//     'subscription_id_details' => $this->Subscription,
//     'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
//     'number' => $this->number,
//     'external_id' => $this->external_id,
//     'type' => $this->type,
//     'status' => $this->status,
//     'subtotal' => $this->subtotal ? (float) $this->subtotal : 0.0,
//     'tax' => $this->tax ? (float) $this->tax : 0.0,
//     'total' => $this->total ? (float) $this->total : 0.0,
//     'amount_due' => $this->amount_due ? (float) $this->amount_due : 0.0,
//     'amount_paid' => $this->amount_paid ? (float) $this->amount_paid : 0.0,
//     'amount_remaining' => $this->amount_remaining ? (float) $this->amount_remaining : 0.0,
//     'currency' => $this->currency,
//     'issue_date' => $this->issue_date?->format('Y-m-d H:i:s'),
//     'due_date' => $this->due_date?->format('Y-m-d H:i:s'),
//     'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
//     'finalized_at' => $this->finalized_at?->format('Y-m-d H:i:s'),
//     'line_items' => $this->line_items,
//     'tax_rates' => $this->tax_rates,
//     'discounts' => $this->discounts ? json_decode($this->discounts, true) : [],
//     'metadata' => $this->metadata,
//     'history' => $this->history,
//     'pdf_url' => $this->pdf_url,
//     'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
//     'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
//     'created_at_human' => $this->created_at?->diffForHumans(),
// ];
