<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Plan basic information
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:plans,code',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['recurring', 'usage', 'one_time', 'hybrid'])],
            'billing_period' => ['required', Rule::in(['monthly', 'yearly', 'quarterly', 'weekly', 'daily'])],
            'billing_interval' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_visible' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_featured' => 'boolean',
            'metadata' => 'nullable|array',

            // Features validation
            'features' => 'nullable|array',
            'features.*.feature_id' => 'required_with:features|exists:features,id',
            'features.*.value' => 'required_with:features|string|max:255',
            'features.*.config' => 'nullable|array',
            'features.*.sort_order' => 'nullable|integer|min:0',

            // Prices validation
            'prices' => 'nullable|array|min:1',
            'prices.*.currency' => 'required_with:prices|string|size:3',
            'prices.*.amount' => 'required_with:prices|numeric|min:0',
            'prices.*.interval' => ['required_with:prices', Rule::in(['month', 'year', 'quarter', 'week', 'day'])],
            'prices.*.interval_count' => 'required_with:prices|integer|min:1',
            'prices.*.usage_type' => ['required_with:prices', Rule::in(['licensed', 'metered', 'tiered'])],
            'prices.*.tiers' => 'nullable|array',
            'prices.*.stripe_price_id' => 'nullable|string|max:255',
            'prices.*.active_from' => 'nullable|date',
            'prices.*.active_to' => 'nullable|date|after:prices.*.active_from',

            // Discounts validation
            'discounts' => 'nullable|array',
            'discounts.*' => 'exists:discounts,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'prices.min' => 'At least one price is required for the plan.',
            'prices.*.currency.required_with' => 'Currency is required for each price.',
            'prices.*.amount.required_with' => 'Amount is required for each price.',
            'features.*.feature_id.required_with' => 'Feature ID is required.',
            'features.*.value.required_with' => 'Feature value is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_visible' => $this->boolean('is_visible', true),
            'is_featured' => $this->boolean('is_featured', false),
            'billing_interval' => (int) ($this->billing_interval ?? 1),
            'sort_order' => (int) ($this->sort_order ?? 0),
        ]);
    }
}
