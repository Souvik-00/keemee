<?php

namespace App\Http\Requests\SiteAllowanceConfig;

use App\Models\Customer;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteAllowanceConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['required', Rule::in(Site::query()->pluck('id')->all())],
            'customer_id' => ['required', Rule::in(Customer::query()->pluck('id')->all())],
            'allowance_type' => ['required', Rule::in(['food', 'night_shift', 'other'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
