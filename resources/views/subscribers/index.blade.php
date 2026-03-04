@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Subscribers</h1>
        @if(auth()->user()->hasPermission('subscribers.manage'))
            <a href="{{ route('subscribers.create') }}" class="btn btn-primary btn-sm">Add Subscriber</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('subscribers.index') }}">
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
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td>{{ $subscriber->name }}</td>
                        <td>{{ $subscriber->code }}</td>
                        <td><span class="badge text-bg-{{ $subscriber->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($subscriber->status) }}</span></td>
                        <td class="text-end">
                            @if(auth()->user()->hasPermission('subscribers.manage'))
                                <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                <form action="{{ route('subscribers.destroy', $subscriber) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this subscriber?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No subscribers found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $subscribers->links() }}</div>
@endsection
