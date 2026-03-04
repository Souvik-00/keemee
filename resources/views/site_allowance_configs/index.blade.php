@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Site Allowance Configs</h1>
        @if(auth()->user()->hasPermission('allowances.manage'))
            <a href="{{ route('site-allowance-configs.create') }}" class="btn btn-primary btn-sm">Add Config</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('site-allowance-configs.index') }}">
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
                <select class="form-select" name="allowance_type">
                    <option value="">All types</option>
                    <option value="food" @selected(request('allowance_type') === 'food')>Food</option>
                    <option value="night_shift" @selected(request('allowance_type') === 'night_shift')>Night Shift</option>
                    <option value="other" @selected(request('allowance_type') === 'other')>Other</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
                <a class="btn btn-outline-primary" href="{{ route('site-allowance-report') }}">Site Allowance Report</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Effective</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($configs as $config)
                        <tr>
                            <td>{{ $config->site?->name }}</td>
                            <td>{{ $config->customer?->name }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($config->allowance_type)) }}</td>
                            <td>{{ number_format((float) $config->amount, 2) }}</td>
                            <td>{{ optional($config->effective_from)->format('Y-m-d') }} to {{ optional($config->effective_to)->format('Y-m-d') ?: 'Open' }}</td>
                            <td><span class="badge text-bg-{{ $config->is_active ? 'success' : 'secondary' }}">{{ $config->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('allowances.manage'))
                                    <a href="{{ route('site-allowance-configs.edit', $config) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="{{ route('site-allowance-configs.destroy', $config) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit" onclick="return confirm('Delete this config?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No allowance configs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $configs->links() }}</div>
@endsection
