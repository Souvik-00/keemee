<?php

namespace App\Http\Requests\ClientContact;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerIds = Customer::query()->pluck('id');

        return [
            'customer_id' => ['required', Rule::in($customerIds)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'designation' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
