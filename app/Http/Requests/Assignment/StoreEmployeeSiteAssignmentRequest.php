<?php

namespace App\Http\Requests\Assignment;

use App\Models\Employee;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeSiteAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subscriberRule = ['nullable'];

        if ($this->user() && $this->user()->hasRole('leadership')) {
            $subscriberRule = ['required', 'exists:subscribers,id'];
        }

        return [
            'subscriber_id' => $subscriberRule,
            'employee_id' => ['required', Rule::in(Employee::query()->pluck('id')->all())],
            'site_id' => ['required', Rule::in(Site::query()->pluck('id')->all())],
            'assigned_from' => ['required', 'date'],
        ];
    }
}
