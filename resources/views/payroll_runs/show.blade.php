@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Payroll Run: {{ $payrollRun->year }}-{{ str_pad((string) $payrollRun->month, 2, '0', STR_PAD_LEFT) }}</h1>
            <div class="text-muted small">
                Subscriber: {{ $payrollRun->subscriber?->name ?? '-' }} |
                Status: {{ ucfirst($payrollRun->status) }} |
                Processed by: {{ $payrollRun->processor?->name ?? '-' }}
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($payrollRun->status === 'draft' && auth()->user()->hasPermission('payroll.finalize'))
                <form method="POST" action="{{ route('payroll-runs.finalize', $payrollRun) }}">
                    @csrf
                    <button class="btn btn-success btn-sm" type="submit" onclick="return confirm('Finalize this payroll run?')">Finalize</button>
                </form>
            @endif
            @if(auth()->user()->hasPermission('salary_lists.view_all') || auth()->user()->hasPermission('salary_lists.view_guards') || auth()->user()->hasPermission('salary_lists.view_site_managers'))
                @php
                    $defaultType = auth()->user()->hasPermission('salary_lists.view_all')
                        ? 'all'
                        : (auth()->user()->hasPermission('salary_lists.view_guards') ? 'guards' : 'site_managers');
                @endphp
                <a href="{{ route('salary-reports.list', ['year' => $payrollRun->year, 'month' => $payrollRun->month, 'type' => $defaultType]) }}" class="btn btn-outline-primary btn-sm">Salary List</a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Basic</th>
                        <th>Extra</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Slip No</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaryRecords as $record)
                        <tr>
                            <td>{{ $record->employee?->name }} ({{ $record->employee?->employee_code }})</td>
                            <td>{{ number_format((float) $record->basic_amount, 2) }}</td>
                            <td>{{ number_format((float) $record->extra_allowance_total, 2) }}</td>
                            <td>{{ number_format((float) $record->deduction_total, 2) }}</td>
                            <td>{{ number_format((float) $record->net_salary, 2) }}</td>
                            <td>{{ $record->slip_no }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('salary_slips.view'))
                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('salary-reports.slip', $record) }}">Slip</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No salary records found for this run.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $salaryRecords->links() }}</div>
@endsection
