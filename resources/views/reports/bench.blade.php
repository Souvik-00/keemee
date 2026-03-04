@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Bench Report</h1>

    <form class="mb-3" method="GET" action="{{ route('reports.bench') }}">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search by employee name/code">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
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
                        <th>Subscriber</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($benchEmployees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($employee->employee_type)) }}</td>
                            <td>{{ $employee->subscriber?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No bench employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $benchEmployees->links() }}</div>
@endsection
