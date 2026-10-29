<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFiscalYearRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('fiscal_years', 'name')
                    ->ignore($this->route('fiscal_year')->id),
            ],


        ];
    }
}
