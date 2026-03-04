@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($site))
        <div class="col-md-4">
            <label class="form-label" for="subscriber_id">Subscriber</label>
            <select class="form-select" id="subscriber_id" name="subscriber_id" required>
                <option value="">Select subscriber</option>
                @foreach($subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', $activeTenantId ?? '') == $subscriber->id)>{{ $subscriber->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <label class="form-label" for="customer_id">Customer</label>
        <select class="form-select" id="customer_id" name="customer_id" required>
            <option value="">Select customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $site->customer_id ?? '') === (string) $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="site_code">Site Code</label>
        <input class="form-control" id="site_code" name="site_code" type="text" value="{{ old('site_code', $site->site_code ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="name">Site Name</label>
        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $site->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status" required>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $site->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="address">Address</label>
        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $site->address ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('sites.index') }}">Cancel</a>
</div>
