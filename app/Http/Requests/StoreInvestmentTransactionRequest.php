<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', 'investment transactions');
    }

    public function rules(): array
    {
        return [
            'transaction_type' => [
                'required',
                'in:INITIAL_INVESTMENT,ADDITIONAL_INVESTMENT,PROFIT_DISTRIBUTION,LOSS_ADJUSTMENT,WITHDRAWAL,MATURITY_CLOSURE,ADMINISTRATIVE_ADJUSTMENT,DIVIDEND_PAYMENT,REINVESTMENT',
            ],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:500'],
            'metadata' => ['nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_type.required' => 'Please select a transaction type',
            'transaction_type.in' => 'The selected transaction type is invalid',
            'amount.min' => 'Amount must be greater than zero',
            'transaction_date.before_or_equal' => 'Transaction date cannot be in the future',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $investment = $this->route('investment');
            $transactionType = $this->input('transaction_type');

            // Validate transaction type is allowed for current investment status
            $allowedTypes = [
                'draft' => ['INITIAL_INVESTMENT'],
                'active' => ['ADDITIONAL_INVESTMENT', 'PROFIT_DISTRIBUTION', 'LOSS_ADJUSTMENT', 'WITHDRAWAL', 'DIVIDEND_PAYMENT', 'REINVESTMENT', 'ADMINISTRATIVE_ADJUSTMENT'],
                'suspended' => ['LOSS_ADJUSTMENT', 'ADMINISTRATIVE_ADJUSTMENT'],
                'matured' => ['MATURITY_CLOSURE', 'WITHDRAWAL'],
                'closed' => [],
            ];

            if (!in_array($transactionType, $allowedTypes[$investment->status] ?? [])) {
                $validator->errors()->add('transaction_type', "Transaction type {$transactionType} is not allowed for {$investment->status} investments");
            }
        });
    }
}
