<?php

namespace App\Http\Requests\Attendance;

use App\Models\Employee;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendanceMonthlySummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $employeeQuery = Employee::query()->select('id');

        if ($user && $user->hasRole('security_guard_manager')) {
            $employeeQuery->where('employee_type', 'guard');
        }

        return [
            'employee_id' => ['required', Rule::in($employeeQuery->pluck('id')->all())],
            'site_id' => ['nullable', Rule::in(Site::query()->pluck('id')->all())],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'present_days' => ['required', 'integer', 'min:0', 'max:31'],
            'absent_days' => ['required', 'integer', 'min:0', 'max:31'],
        ];
    }
}
