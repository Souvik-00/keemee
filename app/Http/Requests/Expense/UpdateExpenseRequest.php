<?php

namespace App\Http\Requests\Expense;

use App\Models\Customer;
use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', Rule::in(Customer::query()->pluck('id')->all())],
            'site_id' => ['nullable', Rule::in(Site::query()->pluck('id')->all())],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
