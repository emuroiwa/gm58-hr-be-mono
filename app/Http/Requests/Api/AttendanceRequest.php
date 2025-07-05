<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'check_in' => ['required', 'date_format:H:i:s'],
            'check_out' => ['nullable', 'date_format:H:i:s', 'after:check_in'],
            'break_duration' => ['nullable', 'integer', 'min:0', 'max:480'], // max 8 hours
            'status' => ['required', 'in:present,absent,late,half_day'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Date is required',
            'check_in.required' => 'Check-in time is required',
            'check_out.after' => 'Check-out time must be after check-in time',
            'status.required' => 'Attendance status is required',
            'break_duration.max' => 'Break duration cannot exceed 8 hours',
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
