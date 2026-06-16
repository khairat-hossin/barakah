<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', 'investments');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'investment_type_id' => ['required', 'exists:investment_types,id'],
            'investor_id' => ['nullable', 'exists:members,id'],
            'risk_level' => ['required', 'in:low,medium,high'],
            'return_type' => ['required', 'in:fixed,variable,dividend'],
            'expected_return_percentage' => ['nullable', 'numeric', 'between:0,999.99'],
            'tenure_months' => ['required', 'integer', 'min:1', 'max:600'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'maturity_date' => ['nullable', 'date', 'after:start_date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'investment_type_id.required' => 'Please select an investment type',
            'investment_type_id.exists' => 'The selected investment type is invalid',
            'tenure_months.min' => 'Tenure must be at least 1 month',
            'maturity_date.after' => 'Maturity date must be after the start date',
        ];
    }
}
