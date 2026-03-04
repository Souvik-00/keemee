@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($config))
        <div class="col-md-3">
            <label class="form-label" for="subscriber_id">Subscriber</label>
            <select class="form-select" id="subscriber_id" name="subscriber_id" required>
                <option value="">Select subscriber</option>
                @foreach($subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', $activeTenantId ?? '') == $subscriber->id)>{{ $subscriber->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-md-3">
        <label class="form-label" for="site_id">Site</label>
        <select class="form-select" id="site_id" name="site_id" required>
            <option value="">Select site</option>
            @foreach($sites as $site)
                <option value="{{ $site->id }}" @selected((string) old('site_id', $config->site_id ?? '') === (string) $site->id)>
                    {{ $site->name }} ({{ $site->site_code }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="customer_id">Customer</label>
        <select class="form-select" id="customer_id" name="customer_id" required>
            <option value="">Select customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $config->customer_id ?? '') === (string) $customer->id)>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="allowance_type">Allowance Type</label>
        <select class="form-select" id="allowance_type" name="allowance_type" required>
            @foreach(['food' => 'Food', 'night_shift' => 'Night Shift', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('allowance_type', $config->allowance_type ?? 'food') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="amount">Amount</label>
        <input class="form-control" id="amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount', $config->amount ?? 0) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="effective_from">Effective From</label>
        <input class="form-control" id="effective_from" name="effective_from" type="date" value="{{ old('effective_from', isset($config->effective_from) ? $config->effective_from->toDateString() : now()->toDateString()) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="effective_to">Effective To</label>
        <input class="form-control" id="effective_to" name="effective_to" type="date" value="{{ old('effective_to', isset($config->effective_to) ? $config->effective_to->toDateString() : '') }}">
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $config->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('site-allowance-configs.index') }}">Cancel</a>
</div>
