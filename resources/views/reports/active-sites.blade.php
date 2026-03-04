@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Active Sites Report</h1>

    <form class="mb-3" method="GET" action="{{ route('reports.active-sites') }}">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search by site/customer">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Site Name</th>
                        <th>Site Code</th>
                        <th>Customer</th>
                        <th>Subscriber</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeSites as $site)
                        <tr>
                            <td>{{ $site->name }}</td>
                            <td>{{ $site->site_code }}</td>
                            <td>{{ $site->customer?->name ?? '-' }}</td>
                            <td>{{ $site->subscriber?->name ?? '-' }}</td>
                            <td><span class="badge text-bg-success">Active</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No active sites found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $activeSites->links() }}</div>
@endsection
