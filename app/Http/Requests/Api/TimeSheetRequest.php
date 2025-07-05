<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TimeSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['nullable', 'date_format:H:i:s', 'after:start_time'],
            'break_duration' => ['nullable', 'integer', 'min:0', 'max:480'], // max 8 hours in minutes
            'description' => ['nullable', 'string', 'max:1000'],
            'project' => ['nullable', 'string', 'max:255'],
            'task' => ['nullable', 'string', 'max:255'],
            'billable' => ['nullable', 'boolean'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Date is required',
            'start_time.required' => 'Start time is required',
            'end_time.after' => 'End time must be after start time',
            'break_duration.max' => 'Break duration cannot exceed 8 hours',
            'hourly_rate.min' => 'Hourly rate must be positive',
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
