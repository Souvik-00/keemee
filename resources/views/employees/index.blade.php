@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Employees</h1>
        @if(auth()->user()->hasPermission('employees.create'))
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">Add Employee</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('employees.index') }}">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search by name/code/email">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Subscriber</th>
                        <th>Status</th>
                        <th>Bench</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($employee->employee_type)) }}</td>
                            <td>{{ $employee->subscriber?->name ?? '-' }}</td>
                            <td><span class="badge text-bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($employee->status) }}</span></td>
                            <td>
                                @if($employee->active_assignments_count === 0)
                                    <span class="badge text-bg-warning">Bench</span>
                                @else
                                    <span class="badge text-bg-info">Assigned</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('employees.update'))
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                @endif
                                @if(auth()->user()->hasPermission('employees.delete'))
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit" onclick="return confirm('Delete this employee?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $employees->links() }}</div>
@endsection
