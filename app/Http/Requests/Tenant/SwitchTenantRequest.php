<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SwitchTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscriber_id' => ['required', Rule::in(array_merge(['all'], $this->subscriberIds()))],
        ];
    }

    /**
     * @return array<int, int>
     */
    protected function subscriberIds(): array
    {
        return \App\Models\Subscriber::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
