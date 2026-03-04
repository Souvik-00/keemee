@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="name">Name</label>
        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $managedUser->name ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="username">Username</label>
        <input class="form-control" id="username" name="username" type="text" value="{{ old('username', $managedUser->username ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $managedUser->email ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="subscriber_id">Subscriber</label>
        <select class="form-select" id="subscriber_id" name="subscriber_id">
            <option value="">No subscriber (leadership only)</option>
            @foreach($subscribers as $subscriber)
                <option value="{{ $subscriber->id }}" @selected((string) old('subscriber_id', $managedUser->subscriber_id ?? '') === (string) $subscriber->id)>{{ $subscriber->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status" required>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $managedUser->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="customer_id">Linked Customer (only for customer role)</label>
        <select class="form-select" id="customer_id" name="customer_id">
            <option value="">No customer link</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $linkedCustomerId ?? '') === (string) $customer->id)>
                    {{ $customer->name }} @if($customer->subscriber) ({{ $customer->subscriber->name }}) @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label d-block">Roles</label>
        @php
            $selectedRoles = collect(old('role_ids', isset($managedUser) ? $managedUser->roles->pluck('id')->all() : []))
                ->map(fn($id) => (int) $id)
                ->all();
        @endphp
        <div class="row g-2">
            @foreach($roles as $role)
                <div class="col-md-3">
                    <label class="form-check-label d-flex gap-2 align-items-start">
                        <input class="form-check-input mt-1" type="checkbox" name="role_ids[]" value="{{ $role->id }}" @checked(in_array((int) $role->id, $selectedRoles, true))>
                        <span>
                            <span class="d-block">{{ $role->name }}</span>
                            <code class="small">{{ $role->slug }}</code>
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    @if(!isset($managedUser))
        <div class="col-md-6">
            <label class="form-label" for="temp_password">Temporary Password</label>
            <input class="form-control" id="temp_password" name="temp_password" type="password" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="temp_password_confirmation">Confirm Temporary Password</label>
            <input class="form-control" id="temp_password_confirmation" name="temp_password_confirmation" type="password" required>
        </div>
    @endif
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
</div>
