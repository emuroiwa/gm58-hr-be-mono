<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->get('company_id');

        return [
            // Basic Information
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'email', 
                'max:255',
                Rule::unique('companies', 'email')->ignore($companyId)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            
            // Address Information
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            
            // Business Information
            'tax_id' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'currency_id' => ['nullable', 'exists:currencies,id'],
            
            // Settings
            'settings' => ['nullable', 'array'],
            'employee_limit' => ['nullable', 'integer', 'min:1'],
            
            // Files
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A company with this email already exists',
            'website.url' => 'Please provide a valid website URL',
            'currency_id.exists' => 'Selected currency is invalid',
            'logo.image' => 'Logo must be an image file',
            'logo.max' => 'Logo size cannot exceed 2MB',
            'employee_limit.min' => 'Employee limit must be at least 1',
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
