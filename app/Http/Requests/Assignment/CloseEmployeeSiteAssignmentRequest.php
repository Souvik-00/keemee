<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class CloseEmployeeSiteAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assignedFrom = optional(optional($this->route('employee_assignment'))->assigned_from)->toDateString();

        return [
            'assigned_to' => ['required', 'date', 'after_or_equal:'.$assignedFrom],
        ];
    }
}
