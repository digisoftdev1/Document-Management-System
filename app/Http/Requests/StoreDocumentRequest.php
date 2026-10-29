<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'day' => ['required', 'integer', 'between:1,32'],
            'file_number' => ['required', 'string', 'max:255'],
            'document_name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'document_category_id' => ['required', 'exists:document_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'fiscal_year_id.required' => 'Please select a fiscal year.',
            'fiscal_year_id.exists' => 'The selected fiscal year is invalid.',
            'government_level_id.required' => 'Please select a government level.',
            'government_level_id.exists' => 'The selected government level is invalid.',
            'budget_type_id.required' => 'Please select a Program Type.',
            'budget_type_id.exists' => 'The selected Program Type is invalid.',
            'month_id.required' => 'Please select a month.',
            'month_id.exists' => 'The selected month is invalid.',
            'day.required' => 'Please select the day.',
            'day.between' => 'The day must be between 1 and 31.',
            'file_number.required' => 'The file number is required.',
            'document_name.required' => 'The document name is required.',
            'file.required' => 'Please upload a PDF document.',
            'file.mimes' => 'The document must be a file of type: pdf.',
            'file.max' => 'The file size cannot exceed 10MB.',
            'document_category_id.required' => 'Please select a document category.',
        ];
    }
}
