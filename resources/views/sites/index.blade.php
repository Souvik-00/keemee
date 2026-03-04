@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Sites</h1>
        @if(auth()->user()->hasPermission('sites.create'))
            <a href="{{ route('sites.create') }}" class="btn btn-primary btn-sm">Add Site</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('sites.index') }}">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search by name/site code">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>Site</th>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sites as $site)
                    <tr>
                        <td>{{ $site->name }}</td>
                        <td>{{ $site->site_code }}</td>
                        <td>{{ $site->customer?->name ?? '-' }}</td>
                        <td><span class="badge text-bg-{{ $site->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($site->status) }}</span></td>
                        <td class="text-end">
                            @if(auth()->user()->hasPermission('sites.update'))
                                <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            @endif
                            @if(auth()->user()->hasPermission('sites.delete'))
                                <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this site?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No sites found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $sites->links() }}</div>
@endsection
