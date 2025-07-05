<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('department');
        $companyId = $this->get('company_id');

        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('departments', 'name')
                    ->where('company_id', $companyId)
                    ->ignore($departmentId)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required',
            'name.unique' => 'A department with this name already exists in your company',
            'manager_id.exists' => 'Selected manager does not exist',
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
