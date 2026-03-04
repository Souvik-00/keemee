<?php

namespace App\Http\Requests\EmployeeExtraAllowance;

use App\Models\Employee;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeExtraAllowanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', Rule::in(Employee::query()->pluck('id')->all())],
            'site_id' => ['nullable', Rule::in(Site::query()->pluck('id')->all())],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'allowance_type' => ['required', Rule::in(['food', 'night_shift', 'other'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
