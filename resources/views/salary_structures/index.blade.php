@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Salary Structures</h1>
        @if(auth()->user()->hasPermission('payroll.process'))
            <a href="{{ route('salary-structures.create') }}" class="btn btn-primary btn-sm">Add Structure</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('salary-structures.index') }}">
        <div class="row g-2">
            <div class="col-md-6">
                <select class="form-select" name="employee_id">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Basic</th>
                        <th>PF%</th>
                        <th>ESI%</th>
                        <th>Other Deduction</th>
                        <th>Effective</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($structures as $structure)
                        <tr>
                            <td>{{ $structure->employee?->name }}</td>
                            <td>{{ number_format((float) $structure->basic_salary, 2) }}</td>
                            <td>{{ number_format((float) $structure->pf_percent, 2) }}</td>
                            <td>{{ number_format((float) $structure->esi_percent, 2) }}</td>
                            <td>{{ number_format((float) $structure->other_deduction_fixed, 2) }}</td>
                            <td>{{ optional($structure->effective_from)->format('Y-m-d') }} to {{ optional($structure->effective_to)->format('Y-m-d') ?: 'Open' }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('payroll.process'))
                                    <a href="{{ route('salary-structures.edit', $structure) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('salary-structures.destroy', $structure) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit" onclick="return confirm('Delete this salary structure?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No salary structures found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $structures->links() }}</div>
@endsection
