@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Salary Slip</h1>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">Print</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div><strong>Slip No:</strong> {{ $salaryRecord->slip_no }}</div>
                    <div><strong>Period:</strong> {{ $salaryRecord->year }}-{{ str_pad((string) $salaryRecord->month, 2, '0', STR_PAD_LEFT) }}</div>
                    <div><strong>Employee:</strong> {{ $salaryRecord->employee?->name }} ({{ $salaryRecord->employee?->employee_code }})</div>
                    <div><strong>Employee Type:</strong> {{ str_replace('_', ' ', ucfirst($salaryRecord->employee?->employee_type ?? '-')) }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div><strong>Subscriber:</strong> {{ $salaryRecord->payrollRun?->subscriber?->name ?? '-' }}</div>
                    <div><strong>Site:</strong> {{ $salaryRecord->site?->name ?? '-' }}</div>
                    <div><strong>Customer:</strong> {{ $salaryRecord->site?->customer?->name ?? '-' }}</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <h2 class="h6">Earnings</h2>
                    <table class="table table-sm">
                        <tbody>
                            @foreach($salaryRecord->components->where('component_type', 'earning') as $component)
                                <tr>
                                    <td>{{ $component->component_name }}</td>
                                    <td class="text-end">{{ number_format((float) $component->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h2 class="h6">Deductions</h2>
                    <table class="table table-sm">
                        <tbody>
                            @foreach($salaryRecord->components->where('component_type', 'deduction') as $component)
                                <tr>
                                    <td>{{ $component->component_name }}</td>
                                    <td class="text-end">{{ number_format((float) $component->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4"><strong>Basic:</strong> {{ number_format((float) $salaryRecord->basic_amount, 2) }}</div>
                <div class="col-md-4"><strong>Extra:</strong> {{ number_format((float) $salaryRecord->extra_allowance_total, 2) }}</div>
                <div class="col-md-4"><strong>Deductions:</strong> {{ number_format((float) $salaryRecord->deduction_total, 2) }}</div>
            </div>
            <div class="mt-2 h5"><strong>Net Salary: {{ number_format((float) $salaryRecord->net_salary, 2) }}</strong></div>
        </div>
    </div>
@endsection
