@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Customers</h1>
        @if(auth()->user()->hasPermission('customers.create'))
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">Add Customer</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('customers.index') }}">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search by name/code">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Subscriber</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->code }}</td>
                        <td>{{ $customer->subscriber?->name ?? '-' }}</td>
                        <td><span class="badge text-bg-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($customer->status) }}</span></td>
                        <td class="text-end">
                            @if(auth()->user()->hasPermission('customers.update'))
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            @endif
                            @if(auth()->user()->hasPermission('customers.delete'))
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this customer?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No customers found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $customers->links() }}</div>
@endsection
