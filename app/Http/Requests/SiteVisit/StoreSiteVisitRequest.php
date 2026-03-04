<?php

namespace App\Http\Requests\SiteVisit;

use App\Models\Employee;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiteVisitRequest extends FormRequest
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
            'site_id' => ['required', Rule::in(Site::query()->pluck('id')->all())],
            'manager_employee_id' => ['required', Rule::in(Employee::query()->where('employee_type', 'manager')->pluck('id')->all())],
            'visit_date' => ['required', 'date'],
            'in_time' => ['required', 'date_format:H:i'],
            'out_time' => ['nullable', 'date_format:H:i', 'after:in_time'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
