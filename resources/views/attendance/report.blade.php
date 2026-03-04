@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Monthly Attendance Report</h1>

    <form class="mb-3" method="GET" action="{{ route('attendance.report') }}">
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
                        <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->name }} ({{ $employee->employee_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Apply</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('attendance-monthly-summaries.index', request()->query()) }}" class="btn btn-outline-primary w-100">Entry Screen</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Employee Type</th>
                        <th>Site</th>
                        <th>Period</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportRows as $row)
                        <tr>
                            <td>{{ $row->employee?->name }} ({{ $row->employee?->employee_code }})</td>
                            <td>{{ str_replace('_', ' ', ucfirst($row->employee?->employee_type ?? '-')) }}</td>
                            <td>{{ $row->site?->name ?? '-' }}</td>
                            <td>{{ $row->year }}-{{ str_pad((string) $row->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $row->present_days }}</td>
                            <td>{{ $row->absent_days }}</td>
                            <td>{{ number_format((float) $row->attendance_percent, 2) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No attendance data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $reportRows->links() }}</div>
@endsection
