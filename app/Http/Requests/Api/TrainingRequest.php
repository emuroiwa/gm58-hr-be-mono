<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'trainer' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'duration_hours' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:scheduled,ongoing,completed,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Training title is required',
            'start_date.required' => 'Start date is required',
            'start_date.after_or_equal' => 'Start date cannot be in the past',
            'end_date.required' => 'End date is required',
            'end_date.after_or_equal' => 'End date must be on or after start date',
            'max_participants.min' => 'Maximum participants must be at least 1',
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
