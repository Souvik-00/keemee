@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Role-Permission Matrix</h1>
        @if(auth()->user()->hasPermission('rbac.roles.view'))
            <a class="btn btn-outline-primary btn-sm" href="{{ route('roles.index') }}">Roles List</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('roles.matrix') }}">
        <div class="row g-2">
            <div class="col-md-3">
                <select class="form-select" name="module">
                    <option value="">All modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ ucfirst($module) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 260px;">Permission</th>
                        @foreach($roles as $role)
                            <th class="text-center" style="min-width: 140px;">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $permission->name }}</div>
                                <div class="text-muted small"><code>{{ $permission->slug }}</code> / {{ $permission->module }}</div>
                            </td>
                            @foreach($roles as $role)
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" disabled @checked($role->permissions->contains('id', $permission->id))>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $roles->count() + 1 }}" class="text-center text-muted py-4">No permissions found for selected filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
