<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PerformanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'reviewer_id' => ['required', 'exists:employees,id'],
            'review_period_start' => ['required', 'date'],
            'review_period_end' => ['required', 'date', 'after:review_period_start'],
            'goals' => ['nullable', 'string', 'max:2000'],
            'achievements' => ['nullable', 'string', 'max:2000'],
            'areas_for_improvement' => ['nullable', 'string', 'max:2000'],
            'overall_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'technical_skills' => ['nullable', 'integer', 'min:1', 'max:5'],
            'communication_skills' => ['nullable', 'integer', 'min:1', 'max:5'],
            'teamwork' => ['nullable', 'integer', 'min:1', 'max:5'],
            'leadership' => ['nullable', 'integer', 'min:1', 'max:5'],
            'punctuality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comments' => ['nullable', 'string', 'max:2000'],
            'goals_next_period' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee is required',
            'reviewer_id.required' => 'Reviewer is required',
            'review_period_start.required' => 'Review period start date is required',
            'review_period_end.required' => 'Review period end date is required',
            'review_period_end.after' => 'Review period end must be after start date',
            'overall_rating.min' => 'Rating must be between 1 and 5',
            'overall_rating.max' => 'Rating must be between 1 and 5',
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
