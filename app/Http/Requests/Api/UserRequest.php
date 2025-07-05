<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        $companyId = $this->get('company_id');

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => $this->isMethod('POST') ? 'required|string|min:8' : 'sometimes|string|min:8',
            'role' => ['required', 'in:admin,hr,manager,employee'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists',
            'role.required' => 'User role is required',
            'role.in' => 'Invalid user role selected',
            'employee_id.exists' => 'Selected employee does not exist',
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
