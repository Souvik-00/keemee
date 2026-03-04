@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($extraAllowance))
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
        <label class="form-label" for="employee_id">Employee</label>
        <select class="form-select" id="employee_id" name="employee_id" required>
            <option value="">Select employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected((string) old('employee_id', $extraAllowance->employee_id ?? '') === (string) $employee->id)>
                    {{ $employee->name }} ({{ $employee->employee_code }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="site_id">Site (optional)</label>
        <select class="form-select" id="site_id" name="site_id">
            <option value="">None</option>
            @foreach($sites as $site)
                <option value="{{ $site->id }}" @selected((string) old('site_id', $extraAllowance->site_id ?? '') === (string) $site->id)>
                    {{ $site->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="year">Year</label>
        <input class="form-control" id="year" name="year" type="number" min="2000" max="2100" value="{{ old('year', $extraAllowance->year ?? now()->year) }}" required>
    </div>

    <div class="col-md-1">
        <label class="form-label" for="month">Month</label>
        <input class="form-control" id="month" name="month" type="number" min="1" max="12" value="{{ old('month', $extraAllowance->month ?? now()->month) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="allowance_type">Type</label>
        <select class="form-select" id="allowance_type" name="allowance_type" required>
            @foreach(['food' => 'Food', 'night_shift' => 'Night Shift', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('allowance_type', $extraAllowance->allowance_type ?? 'food') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="amount">Amount</label>
        <input class="form-control" id="amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount', $extraAllowance->amount ?? 0) }}" required>
    </div>

    <div class="col-md-12">
        <label class="form-label" for="notes">Notes</label>
        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $extraAllowance->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('employee-extra-allowances.index') }}">Cancel</a>
</div>
