<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Company Information
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255', 'unique:companies,email'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_city' => ['nullable', 'string', 'max:100'],
            'company_state' => ['nullable', 'string', 'max:100'],
            'company_country' => ['nullable', 'string', 'max:100'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'currency_id' => ['nullable', 'integer', 'exists:currencies,id'],

            // Admin User Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],

            // Terms and Conditions
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required',
            'company_email.unique' => 'A company with this email already exists',
            'email.unique' => 'A user with this email already exists',
            'password.confirmed' => 'Password confirmation does not match',
            'terms_accepted.accepted' => 'You must accept the terms and conditions',
            'privacy_accepted.accepted' => 'You must accept the privacy policy',
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
