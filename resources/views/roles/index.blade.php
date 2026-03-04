@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Roles List</h1>
        @if(auth()->user()->hasPermission('rbac.matrix.view'))
            <a class="btn btn-outline-primary btn-sm" href="{{ route('roles.matrix') }}">Open Matrix</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('roles.index') }}">
        <div class="row g-2">
            <div class="col-md-4">
                <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="Search role name or slug">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Search</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->users_count }}</td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasRole('leadership') && auth()->user()->hasPermission('rbac.roles.manage') && $role->slug !== 'leadership')
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('roles.edit', $role) }}">Edit Permissions</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
