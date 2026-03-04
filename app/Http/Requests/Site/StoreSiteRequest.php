<?php

namespace App\Http\Requests\Site;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerIds = Customer::query()->pluck('id');
        $user = $this->user();
        $subscriberRule = ['nullable'];

        if ($user && $user->hasRole('leadership')) {
            $subscriberRule = ['required', 'exists:subscribers,id'];
        }

        return [
            'subscriber_id' => $subscriberRule,
            'customer_id' => ['required', Rule::in($customerIds)],
            'site_code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
