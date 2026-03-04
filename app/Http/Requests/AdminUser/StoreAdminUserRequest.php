<?php

namespace App\Http\Requests\AdminUser;

use App\Models\Customer;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'subscriber_id' => ['nullable', Rule::exists('subscribers', 'id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', Rule::exists('roles', 'id')],
            'customer_id' => ['nullable', Rule::exists('customers', 'id')],
            'temp_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $roleSlugs = $this->roleSlugs();
            $isLeadership = in_array('leadership', $roleSlugs, true);
            $isCustomer = in_array('customer', $roleSlugs, true);

            $subscriberId = $this->integer('subscriber_id') ?: null;
            $customerId = $this->integer('customer_id') ?: null;

            if (! $isLeadership && ! $subscriberId) {
                $validator->errors()->add('subscriber_id', 'Subscriber is required for non-leadership users.');
            }

            if ($isCustomer && ! $customerId) {
                $validator->errors()->add('customer_id', 'Customer selection is required for customer role.');
            }

            if (! $isCustomer && $customerId) {
                $validator->errors()->add('customer_id', 'Customer can be linked only when customer role is selected.');
            }

            if ($customerId) {
                $customer = Customer::withoutGlobalScopes()->find($customerId);

                if (! $customer) {
                    $validator->errors()->add('customer_id', 'Selected customer is invalid.');

                    return;
                }

                if (! $subscriberId || (int) $customer->subscriber_id !== (int) $subscriberId) {
                    $validator->errors()->add('customer_id', 'Customer must belong to the selected subscriber.');
                }

                if ($customer->user_id) {
                    $validator->errors()->add('customer_id', 'Selected customer is already linked to another user.');
                }
            }
        });
    }

    /**
     * @return array<int, string>
     */
    protected function roleSlugs(): array
    {
        return Role::query()
            ->whereIn('id', $this->input('role_ids', []))
            ->pluck('slug')
            ->all();
    }
}
