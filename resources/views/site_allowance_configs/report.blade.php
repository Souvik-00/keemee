@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Site Allowance Report</h1>

    <form class="mb-3" method="GET" action="{{ route('site-allowance-report') }}">
        <div class="row g-2">
            <div class="col-md-3">
                <select class="form-select" name="site_id">
                    <option value="">All sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" @selected((string) request('site_id') === (string) $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input class="form-control" type="number" min="2000" max="2100" name="year" value="{{ request('year', $filters['year']) }}" placeholder="Year">
            </div>
            <div class="col-md-2">
                <input class="form-control" type="number" min="1" max="12" name="month" value="{{ request('month', $filters['month']) }}" placeholder="Month">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="allowance_type">
                    <option value="">All types</option>
                    <option value="food" @selected(request('allowance_type') === 'food')>Food</option>
                    <option value="night_shift" @selected(request('allowance_type') === 'night_shift')>Night Shift</option>
                    <option value="other" @selected(request('allowance_type') === 'other')>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Apply</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        @forelse($totalsByType as $total)
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ str_replace('_', ' ', ucfirst($total->allowance_type)) }}</div>
                        <div class="h5 mb-0">{{ number_format((float) $total->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted">No totals available for selected filters.</div>
        @endforelse
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Effective Period</th>
                        <th>Status</th>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No report rows found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $configs->links() }}</div>
@endsection
