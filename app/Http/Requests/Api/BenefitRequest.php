<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BenefitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'in:health,dental,vision,retirement,life_insurance,other'],
            'company_contribution' => ['nullable', 'numeric', 'min:0'],
            'employee_contribution' => ['nullable', 'numeric', 'min:0'],
            'is_mandatory' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Benefit name is required',
            'type.required' => 'Benefit type is required',
            'type.in' => 'Invalid benefit type selected',
            'company_contribution.min' => 'Company contribution must be positive',
            'employee_contribution.min' => 'Employee contribution must be positive',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
