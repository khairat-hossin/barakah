<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', 'investment documents');
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'in:agreement,certificate,statement,proof_of_investment,valuation,contract,legal_document,other'],
            'file' => ['required', 'file', 'max:10240', 'mimetypes:application/pdf,image/jpeg,image/png,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Please select a document type',
            'document_type.in' => 'The selected document type is invalid',
            'file.required' => 'Please select a file to upload',
            'file.file' => 'The uploaded item must be a file',
            'file.max' => 'File size must not exceed 10MB',
            'file.mimetypes' => 'Only PDF, images (JPG, PNG), Excel, and Word documents are allowed',
        ];
    }
}
