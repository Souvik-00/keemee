@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Employee Assignments</h1>
        @if(auth()->user()->hasPermission('assignments.create'))
            <a href="{{ route('employee-assignments.create') }}" class="btn btn-primary btn-sm">New Assignment</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('employee-assignments.index') }}">
        <div class="row g-2">
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active Only</option>
                </select>
            </div>
            <div class="col-md-3">
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
                        <th>Site</th>
                        <th>Assigned From</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->employee?->name }} ({{ $assignment->employee?->employee_code }})</td>
                            <td>{{ $assignment->site?->name }} / {{ $assignment->site?->customer?->name }}</td>
                            <td>{{ optional($assignment->assigned_from)->format('Y-m-d') }}</td>
                            <td>{{ optional($assignment->assigned_to)->format('Y-m-d') ?: '-' }}</td>
                            <td>
                                <span class="badge text-bg-{{ $assignment->is_active ? 'success' : 'secondary' }}">
                                    {{ $assignment->is_active ? 'Active' : 'Closed' }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($assignment->is_active && auth()->user()->hasPermission('assignments.close'))
                                    <a href="{{ route('employee-assignments.edit', $assignment) }}" class="btn btn-outline-warning btn-sm">Close</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $assignments->links() }}</div>
@endsection
