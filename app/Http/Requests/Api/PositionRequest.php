<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class PositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $positionId = $this->route('position');
        $companyId = $this->get('company_id');

        return [
            'title' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('positions', 'title')
                    ->where('company_id', $companyId)
                    ->where('department_id', $this->get('department_id'))
                    ->ignore($positionId)
            ],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'requirements' => ['nullable', 'string', 'max:2000'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'min:0', 'gte:min_salary'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Position title is required',
            'title.unique' => 'A position with this title already exists in the selected department',
            'department_id.required' => 'Department is required',
            'department_id.exists' => 'Selected department does not exist',
            'max_salary.gte' => 'Maximum salary must be greater than or equal to minimum salary',
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
