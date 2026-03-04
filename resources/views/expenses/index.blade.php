@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Expenses</h1>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('expenses.create'))
                <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">Add Expense</a>
            @endif
        </div>
    </div>

    <form class="mb-3" method="GET" action="{{ route('expenses.index') }}">
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
                <select class="form-select" name="category">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input class="form-control" type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input class="form-control" type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
                @if(auth()->user()->hasPermission('expenses.reports'))
                    <a class="btn btn-outline-primary" href="{{ route('expenses.site-report') }}">Reports</a>
                @endif
            </div>
        </div>
    </form>

    <div class="alert alert-light border mb-3">
        <strong>Total:</strong> {{ number_format((float) ($totals->total_amount ?? 0), 2) }}
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Site</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ optional($expense->expense_date)->format('Y-m-d') }}</td>
                            <td>{{ $expense->customer?->name }}</td>
                            <td>{{ $expense->site?->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($expense->category) }}</td>
                            <td>{{ number_format((float) $expense->amount, 2) }}</td>
                            <td>{{ $expense->description ?: '-' }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('expenses.update'))
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('expenses.edit', $expense) }}">Edit</a>
                                @endif
                                @if(auth()->user()->hasPermission('expenses.delete'))
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit" onclick="return confirm('Delete this expense?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-muted py-4" colspan="7">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $expenses->links() }}</div>
@endsection
