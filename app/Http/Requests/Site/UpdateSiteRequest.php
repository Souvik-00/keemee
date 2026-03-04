<?php

namespace App\Http\Requests\Site;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', Rule::in(Customer::query()->pluck('id')->all())],
            'site_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sites', 'site_code')
                    ->where(fn ($query) => $query->where('subscriber_id', $this->route('site')->subscriber_id))
                    ->ignore($this->route('site')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
