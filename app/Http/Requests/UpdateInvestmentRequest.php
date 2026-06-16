<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->investment);
    }

    public function rules(): array
    {
        $investment = $this->route('investment');

        // Only allow certain fields to be updated based on status
        $rules = [
            'description' => ['nullable', 'string'],
            'risk_level' => ['sometimes', 'in:low,medium,high'],
            'expected_return_percentage' => ['nullable', 'numeric', 'between:0,999.99'],
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'json'],
        ];

        // Only allow these updates for draft status
        if ($investment->status === 'draft') {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'max:255'],
                'investment_type_id' => ['required', 'exists:investment_types,id'],
                'return_type' => ['required', 'in:fixed,variable,dividend'],
                'tenure_months' => ['required', 'integer', 'min:1', 'max:600'],
                'maturity_date' => ['nullable', 'date', 'after:start_date'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'investment_type_id.required' => 'Please select an investment type',
            'investment_type_id.exists' => 'The selected investment type is invalid',
            'tenure_months.min' => 'Tenure must be at least 1 month',
        ];
    }
}
