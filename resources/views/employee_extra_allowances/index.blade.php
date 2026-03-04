@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Employee Extra Allowances</h1>
        @if(auth()->user()->hasPermission('allowances.manage'))
            <a href="{{ route('employee-extra-allowances.create') }}" class="btn btn-primary btn-sm">Add Extra Allowance</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('employee-extra-allowances.index') }}">
        <div class="row g-2">
            <div class="col-md-2">
                <input class="form-control" type="number" name="year" min="2000" max="2100" value="{{ request('year', $filters['year']) }}" placeholder="Year">
            </div>
            <div class="col-md-2">
                <input class="form-control" type="number" name="month" min="1" max="12" value="{{ request('month', $filters['month']) }}" placeholder="Month">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="employee_id">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="site_id">
                    <option value="">All sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" @selected((string) request('site_id') === (string) $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="allowance_type">
                    <option value="">All types</option>
                    <option value="food" @selected(request('allowance_type') === 'food')>Food</option>
                    <option value="night_shift" @selected(request('allowance_type') === 'night_shift')>Night Shift</option>
                    <option value="other" @selected(request('allowance_type') === 'other')>Other</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-secondary w-100" type="submit">Go</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Monthly Totals by Employee</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th class="text-end">Total Extra Allowance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($totalsByEmployee as $total)
                        <tr>
                            <td>{{ $total->employee?->name ?? 'Unknown' }}</td>
                            <td class="text-end">{{ number_format((float) $total->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">No totals available for selected month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Site</th>
                        <th>Period</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allowances as $allowance)
                        <tr>
                            <td>{{ $allowance->employee?->name }} ({{ $allowance->employee?->employee_code }})</td>
                            <td>{{ $allowance->site?->name ?? '-' }}</td>
                            <td>{{ $allowance->year }}-{{ str_pad((string) $allowance->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($allowance->allowance_type)) }}</td>
                            <td>{{ number_format((float) $allowance->amount, 2) }}</td>
                            <td>{{ $allowance->notes ?: '-' }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('allowances.manage'))
                                    <a href="{{ route('employee-extra-allowances.edit', $allowance) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="{{ route('employee-extra-allowances.destroy', $allowance) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this extra allowance?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No extra allowances found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $allowances->links() }}</div>
@endsection
