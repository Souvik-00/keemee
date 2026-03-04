<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $subscriberRule = ['nullable'];

        if ($user && $user->hasRole('leadership')) {
            $subscriberRule = ['required', 'exists:subscribers,id'];
        }

        return [
            'subscriber_id' => $subscriberRule,
            'employee_code' => ['required', 'string', 'max:100'],
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
