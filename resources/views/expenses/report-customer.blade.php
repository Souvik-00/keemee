@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Customer-wise Expense Report</h1>

    <form class="mb-3" method="GET" action="{{ route('expenses.customer-report') }}">
        <div class="row g-2">
            <div class="col-md-2">
                <select class="form-select" name="customer_id">
                    <option value="">All customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="site_id">
                    <option value="">All sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" @selected((string) request('site_id') === (string) $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="grouping">
                    @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('grouping', $grouping) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input class="form-control" type="date" name="date_from" value="{{ request('date_from') }}"></div>
            <div class="col-md-2"><input class="form-control" type="date" name="date_to" value="{{ request('date_to') }}"></div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Apply</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Overall Total</div>
                    <div class="h5 mb-0">{{ number_format((float) ($overallTotal->total_amount ?? 0), 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-8 d-flex justify-content-end align-items-end">
            <a class="btn btn-outline-primary" href="{{ route('expenses.site-report', request()->query()) }}">Switch to Site-wise Report</a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Grouped Subtotals ({{ ucfirst($grouping) }})</div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Customer</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedSubtotals as $row)
                        <tr>
                            <td>{{ $row->period_label }}</td>
                            <td>{{ $row->customer?->name ?? 'N/A' }}</td>
                            <td>{{ number_format((float) $row->subtotal_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No grouped data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Customer Totals</div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerTotals as $row)
                        <tr>
                            <td>{{ $row->customer?->name ?? 'N/A' }}</td>
                            <td>{{ number_format((float) $row->customer_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">No totals found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
