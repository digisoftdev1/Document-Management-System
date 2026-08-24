<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
            'government_level_id' => ['required', 'exists:government_levels,id'],
            'budget_type_id' => ['required', 'exists:budget_types,id'],
            'month_id' => ['required', 'exists:months,id'],
            'day' => ['required', 'integer', 'between:1,31'],
            'file_number' => ['required', 'string', 'max:255'],
            'document_name' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:50240'],
        ];
    }

    public function messages(): array
    {
        return [
            'fiscal_year_id.required' => 'Please select a fiscal year.',
            'government_level_id.required' => 'Please select a government level.',
            'budget_type_id.required' => 'Please select a Program Type.',
            'month_id.required' => 'Please select a month.',
            'day.required' => 'Please enter the day.',
            'day.between' => 'The day must be between 1 and 31.',
            'file_number.required' => 'The file number is required.',
            'document_name.required' => 'The document name is required.',
            'file.mimes' => 'The document must be a PDF file.',
            'file.max' => 'The file size cannot exceed 10MB.',
        ];
    }
}
