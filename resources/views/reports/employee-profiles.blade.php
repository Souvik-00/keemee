@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Employee Profile Report</h1>

    <form class="mb-3" method="GET" action="{{ route('reports.employee-profiles') }}">
        <div class="row g-2">
            <div class="col-md-4">
                <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="Search by name/code/designation/type">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="employee_type">
                    <option value="">All employee types</option>
                    @foreach(['guard', 'site_manager', 'manager', 'other'] as $type)
                        <option value="{{ $type }}" @selected(request('employee_type') === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Apply</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Designation</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Active Site(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($employee->employee_type)) }}</td>
                            <td>{{ $employee->designation }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ ucfirst($employee->status) }}</td>
                            <td>
                                @if($employee->activeAssignments->isEmpty())
                                    <span class="text-muted">Bench</span>
                                @else
                                    {{ $employee->activeAssignments->map(fn($a) => $a->site?->name)->filter()->implode(', ') }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $employees->links() }}</div>
@endsection
