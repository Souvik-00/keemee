@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Site Visits</h1>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('site_visits.manager_report'))
                <a href="{{ route('site-visits.report') }}" class="btn btn-outline-primary btn-sm">Manager Report</a>
            @endif
            @if(auth()->user()->hasPermission('site_visits.create'))
                <a href="{{ route('site-visits.create') }}" class="btn btn-primary btn-sm">Log Visit</a>
            @endif
        </div>
    </div>

    <form class="mb-3" method="GET" action="{{ route('site-visits.index') }}">
        <div class="row g-2">
            <div class="col-md-4">
                <select class="form-select" name="site_id">
                    <option value="">All sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" @selected((string) request('site_id') === (string) $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="manager_employee_id">
                    <option value="">All managers</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" @selected((string) request('manager_employee_id') === (string) $manager->id)>{{ $manager->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Visit Date</th>
                        <th>Site</th>
                        <th>Manager</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Remarks</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                        <tr>
                            <td>{{ optional($visit->visit_date)->format('Y-m-d') }}</td>
                            <td>{{ $visit->site?->name }} / {{ $visit->site?->customer?->name }}</td>
                            <td>{{ $visit->managerEmployee?->name }}</td>
                            <td>{{ $visit->in_time }}</td>
                            <td>{{ $visit->out_time ?: '-' }}</td>
                            <td>{{ $visit->remarks ?: '-' }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('site_visits.update'))
                                    <a href="{{ route('site-visits.edit', $visit) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No site visits found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $visits->links() }}</div>
@endsection
