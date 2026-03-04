<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employees', 'employee_code')
                    ->where(fn ($query) => $query->where('subscriber_id', $this->route('employee')->subscriber_id))
                    ->ignore($this->route('employee')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'designation' => ['nullable', 'string', 'max:150'],
            'employee_type' => ['required', Rule::in(['guard', 'site_manager', 'manager', 'other'])],
            'join_date' => ['nullable', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
