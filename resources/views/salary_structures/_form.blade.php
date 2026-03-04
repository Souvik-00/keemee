@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($salaryStructure))
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

    <div class="col-md-4">
        <label class="form-label" for="employee_id">Employee</label>
        <select class="form-select" id="employee_id" name="employee_id" required>
            <option value="">Select employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected((string) old('employee_id', $salaryStructure->employee_id ?? '') === (string) $employee->id)>
                    {{ $employee->name }} ({{ $employee->employee_code }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="basic_salary">Basic Salary</label>
        <input class="form-control" id="basic_salary" name="basic_salary" type="number" step="0.01" min="0" value="{{ old('basic_salary', $salaryStructure->basic_salary ?? 0) }}" required>
    </div>

    <div class="col-md-1">
        <label class="form-label" for="pf_percent">PF %</label>
        <input class="form-control" id="pf_percent" name="pf_percent" type="number" step="0.01" min="0" max="100" value="{{ old('pf_percent', $salaryStructure->pf_percent ?? 0) }}" required>
    </div>

    <div class="col-md-1">
        <label class="form-label" for="esi_percent">ESI %</label>
        <input class="form-control" id="esi_percent" name="esi_percent" type="number" step="0.01" min="0" max="100" value="{{ old('esi_percent', $salaryStructure->esi_percent ?? 0) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="other_deduction_fixed">Other Deduction</label>
        <input class="form-control" id="other_deduction_fixed" name="other_deduction_fixed" type="number" step="0.01" min="0" value="{{ old('other_deduction_fixed', $salaryStructure->other_deduction_fixed ?? 0) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="effective_from">Effective From</label>
        <input class="form-control" id="effective_from" name="effective_from" type="date" value="{{ old('effective_from', isset($salaryStructure->effective_from) ? $salaryStructure->effective_from->toDateString() : now()->toDateString()) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="effective_to">Effective To</label>
        <input class="form-control" id="effective_to" name="effective_to" type="date" value="{{ old('effective_to', isset($salaryStructure->effective_to) ? $salaryStructure->effective_to->toDateString() : '') }}">
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('salary-structures.index') }}">Cancel</a>
</div>
