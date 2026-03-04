@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership'))
        <div class="col-md-4">
            <label class="form-label" for="subscriber_id">Subscriber</label>
            <select class="form-select" id="subscriber_id" name="subscriber_id" required>
                <option value="">Select subscriber</option>
                @foreach($subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', $customer->subscriber_id ?? ($activeTenantId ?? '')) == $subscriber->id)>{{ $subscriber->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <label class="form-label" for="name">Name</label>
        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $customer->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="code">Code</label>
        <input class="form-control" id="code" name="code" type="text" value="{{ old('code', $customer->code ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="user_id">Portal User (optional)</label>
        <select class="form-select" id="user_id" name="user_id">
            <option value="">None</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $customer->user_id ?? '') === (string) $user->id)>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status" required>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $customer->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="billing_address">Billing Address</label>
        <textarea class="form-control" id="billing_address" name="billing_address" rows="3">{{ old('billing_address', $customer->billing_address ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Cancel</a>
</div>
