@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Attendance Monthly Summaries</h1>
        @if(auth()->user()->hasPermission('attendance.create'))
            <a href="{{ route('attendance-monthly-summaries.create') }}" class="btn btn-primary btn-sm">Add Attendance</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('attendance-monthly-summaries.index') }}">
        <div class="row g-2">
            <div class="col-md-2">
                <input class="form-control" name="year" type="number" min="2000" max="2100" value="{{ request('year', $filters['year']) }}" placeholder="Year">
            </div>
            <div class="col-md-2">
                <input class="form-control" name="month" type="number" min="1" max="12" value="{{ request('month', $filters['month']) }}" placeholder="Month">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="employee_id">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>
                            {{ $employee->name }} ({{ $employee->employee_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
            </div>
            <div class="col-md-2">
                @if(auth()->user()->hasPermission('attendance.monthly_report'))
                    <a href="{{ route('attendance.report', request()->query()) }}" class="btn btn-outline-primary w-100">Open Report</a>
                @endif
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
                        <th>Period</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Percent</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaries as $summary)
                        <tr>
                            <td>{{ $summary->employee?->name }} ({{ $summary->employee?->employee_code }})</td>
                            <td>{{ $summary->site?->name ?? '-' }}</td>
                            <td>{{ $summary->year }}-{{ str_pad((string) $summary->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $summary->present_days }}</td>
                            <td>{{ $summary->absent_days }}</td>
                            <td>{{ number_format((float) $summary->attendance_percent, 2) }}%</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('attendance.update'))
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('attendance-monthly-summaries.edit', $summary) }}">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No attendance summaries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $summaries->links() }}</div>
@endsection
