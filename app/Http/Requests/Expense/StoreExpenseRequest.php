<?php

namespace App\Http\Requests\Expense;

use App\Models\Customer;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $subscriberRule = ['nullable'];

        if ($user && $user->hasRole('leadership')) {
            $subscriberRule = ['required', 'exists:subscribers,id'];
        }

        return [
            'subscriber_id' => $subscriberRule,
            'customer_id' => ['required', Rule::in(Customer::query()->pluck('id')->all())],
            'site_id' => ['nullable', Rule::in(Site::query()->pluck('id')->all())],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
