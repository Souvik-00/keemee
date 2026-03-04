@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Users Management</h1>
        <a class="btn btn-primary btn-sm" href="{{ route('admin.users.create') }}">Create User</a>
    </div>

    <form class="mb-3" method="GET" action="{{ route('admin.users.index') }}">
        <div class="row g-2">
            <div class="col-md-3">
                <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="Search name/username/email">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="subscriber_id">
                    <option value="">All subscribers</option>
                    <option value="none" @selected(request('subscriber_id') === 'none')>No subscriber</option>
                    @foreach($subscribers as $subscriber)
                        <option value="{{ $subscriber->id }}" @selected((string) request('subscriber_id') === (string) $subscriber->id)>{{ $subscriber->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="role_id">
                    <option value="">All roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) request('role_id') === (string) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Any status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-secondary" type="submit">Apply</button>
                <a class="btn btn-outline-dark" href="{{ route('admin.users.index') }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Subscriber</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Must Change Password</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->subscriber?->name ?? 'N/A' }}</td>
                            <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                            <td>
                                <span class="badge text-bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->must_change_password ? 'Yes' : 'No' }}</td>
                            <td class="text-end">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.users.show', $user) }}">View</a>
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                <a class="btn btn-outline-warning btn-sm" href="{{ route('admin.users.reset-password.form', $user) }}">Reset Password</a>

                                <form class="d-inline" method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'inactive' : 'active' }}">
                                    <button class="btn btn-outline-{{ $user->status === 'active' ? 'danger' : 'success' }} btn-sm" type="submit">
                                        {{ $user->status === 'active' ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
