@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Salary List Report</h1>

    <form class="mb-3" method="GET" action="{{ route('salary-reports.list') }}">
        <div class="row g-2">
            <div class="col-md-2">
                <input class="form-control" type="number" min="2000" max="2100" name="year" value="{{ request('year', $filters['year']) }}" placeholder="Year">
            </div>
            <div class="col-md-2">
                <input class="form-control" type="number" min="1" max="12" name="month" value="{{ request('month', $filters['month']) }}" placeholder="Month">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="type">
                    @if(auth()->user()->hasPermission('salary_lists.view_all'))
                        <option value="all" @selected($filters['type'] === 'all')>All Employees</option>
                    @endif
                    @if(auth()->user()->hasPermission('salary_lists.view_guards'))
                        <option value="guards" @selected($filters['type'] === 'guards')>Guards</option>
                    @endif
                    @if(auth()->user()->hasPermission('salary_lists.view_site_managers'))
                        <option value="site_managers" @selected($filters['type'] === 'site_managers')>Site Managers</option>
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Apply</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Basic Total</div><div class="h6 mb-0">{{ number_format((float) ($totals->basic_total ?? 0), 2) }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Extra Total</div><div class="h6 mb-0">{{ number_format((float) ($totals->extra_total ?? 0), 2) }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Deduction Total</div><div class="h6 mb-0">{{ number_format((float) ($totals->deduction_total ?? 0), 2) }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Net Total</div><div class="h6 mb-0">{{ number_format((float) ($totals->net_total ?? 0), 2) }}</div></div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Site</th>
                        <th>Basic</th>
                        <th>Extra</th>
                        <th>Deductions</th>
                        <th>Net</th>
                        <th>Slip</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->employee?->name }} ({{ $record->employee?->employee_code }})</td>
                            <td>{{ str_replace('_', ' ', ucfirst($record->employee?->employee_type ?? '-')) }}</td>
                            <td>{{ $record->site?->name ?? '-' }}</td>
                            <td>{{ number_format((float) $record->basic_amount, 2) }}</td>
                            <td>{{ number_format((float) $record->extra_allowance_total, 2) }}</td>
                            <td>{{ number_format((float) $record->deduction_total, 2) }}</td>
                            <td>{{ number_format((float) $record->net_salary, 2) }}</td>
                            <td>
                                @if(auth()->user()->hasPermission('salary_slips.view'))
                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('salary-reports.slip', $record) }}">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No salary records for selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $records->links() }}</div>
@endsection
