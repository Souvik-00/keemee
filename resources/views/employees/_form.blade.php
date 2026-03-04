@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($employee))
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
        <label class="form-label" for="employee_code">Employee Code</label>
        <input class="form-control" id="employee_code" name="employee_code" type="text" value="{{ old('employee_code', $employee->employee_code ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="name">Name</label>
        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $employee->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Phone</label>
        <input class="form-control" id="phone" name="phone" type="text" value="{{ old('phone', $employee->phone ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $employee->email ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="designation">Designation</label>
        <input class="form-control" id="designation" name="designation" type="text" value="{{ old('designation', $employee->designation ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="employee_type">Employee Type</label>
        <select class="form-select" id="employee_type" name="employee_type" required>
            @foreach(['guard' => 'Guard', 'site_manager' => 'Site Manager', 'manager' => 'Manager', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('employee_type', $employee->employee_type ?? 'other') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="join_date">Join Date</label>
        <input class="form-control" id="join_date" name="join_date" type="date" value="{{ old('join_date', isset($employee->join_date) ? $employee->join_date->toDateString() : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="basic_salary">Basic Salary</label>
        <input class="form-control" id="basic_salary" name="basic_salary" type="number" step="0.01" min="0" value="{{ old('basic_salary', $employee->basic_salary ?? 0) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status" required>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $employee->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('employees.index') }}">Cancel</a>
</div>
