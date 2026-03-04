<?php

namespace App\Http\Requests\SalaryStructure;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalaryStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', Rule::in(Employee::query()->pluck('id')->all())],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'pf_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'esi_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'other_deduction_fixed' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
        ];
    }
}
